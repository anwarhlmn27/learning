<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('roles.id', $request->role);
            });
        }

        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'password' => [
                'nullable',
                'string',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : Hash::make('LmsHorizon$01'),
                'status' => 'active',
            ]);

            $user->roles()->attach($request->role_id, ['id' => (string) Str::uuid()]);
            $this->syncDosenRecord($user, $request->role_id);
        });

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        return redirect()->route('users.index');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'password' => [
                'nullable',
                'string',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        DB::transaction(function () use ($request, $user) {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Sync role, generate new UUID for the pivot id if attaching a new one
            // To ensure safety, we can detach and attach, or use sync with UUID if needed.
            // Using detach and attach ensures we always have a UUID.
            $user->roles()->detach();
            $user->roles()->attach($request->role_id, ['id' => (string) Str::uuid()]);
            $this->syncDosenRecord($user, $request->role_id);
        });

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function toggleStatus(User $user)
    {
        $user->status = $user->status === 'active' ? 'Inactive' : 'active';
        $user->save();

        return back()->with('success', 'Status user berhasil diperbarui.');
    }

    public function import(Request $request)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        
        // Detect Delimiter
        $fileHandle = fopen($file->path(), 'r');
        $firstLine = fgets($fileHandle);
        fclose($fileHandle);
        $delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';

        $handle = fopen($file->path(), 'r');
        $header = true;
        $successCount = 0;
        $skippedCount = 0;
        $errorRows = [];
        
        $defaultPassword = Hash::make('LmsHorizon$01');
        $roleMap = Role::all()->keyBy(fn($r) => strtolower($r->name));
        $existingEmails = User::pluck('email')->flip()->toArray();

        DB::beginTransaction();
        try {
            $rowCount = 0;
            while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $rowCount++;
                if ($header) {
                    $header = false;
                    continue;
                }

                // Skip empty rows
                if (empty(array_filter($data))) continue;

                // Format CSV: name, email, role_name
                if (count($data) >= 3) {
                    $name = trim($data[0]);
                    $email = trim($data[1]);
                    $roleName = strtolower(trim($data[2]));

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $skippedCount++;
                        continue;
                    }

                    if (!isset($existingEmails[$email])) {
                        $user = User::create([
                            'name' => $name,
                            'email' => $email,
                            'password' => $defaultPassword,
                            'status' => 'active',
                        ]);

                        if (isset($roleMap[$roleName])) {
                            $role = $roleMap[$roleName];
                            $user->roles()->attach($role->id, ['id' => (string) Str::uuid()]);
                            $this->syncDosenRecord($user, $role->id);
                        }

                        $existingEmails[$email] = true;
                        $successCount++;
                    } else {
                        $skippedCount++;
                    }
                } else {
                    $errorRows[] = "Baris $rowCount: Format kolom tidak sesuai (terdeteksi " . count($data) . " kolom).";
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat import: ' . $e->getMessage());
        } finally {
            fclose($handle);
        }

        if ($successCount === 0 && !empty($errorRows)) {
            return back()->with('error', 'Gagal mengimpor user. ' . implode(' ', $errorRows));
        }

        $message = "$successCount user berhasil diimport.";
        if ($skippedCount > 0) {
            $message .= " ($skippedCount data dilewati karena email sudah ada atau format salah).";
        }

        return redirect()->route('users.index')->with('success', $message);
    }

    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=user_import_template.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['name', 'email', 'role_name'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['Contoh User', 'user@example.com', 'dosen']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        try {
            $user->delete();
            return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->withErrors(['error' => $this->handleException($e, 'Gagal menghapus user.')]);
        }
    }

    private function syncDosenRecord(User $user, $roleIdOrName)
    {
        $role = Role::where('id', $roleIdOrName)->orWhere('name', $roleIdOrName)->first();
        if ($role && in_array($role->name, ['dosen', 'rektor', 'dekan', 'kaprodi'])) {
            if (!$user->dosen) {
                $defaultProdi = \App\Models\Prodi::first();
                $prodiId = $defaultProdi ? $defaultProdi->id : null;
                if ($prodiId) {
                    \App\Models\Dosen::create([
                        'user_id' => $user->id,
                        'prodi_id' => $prodiId,
                        'nidn' => 'TEMP-' . rand(10000000, 99999999),
                        'nama_dosen' => $user->name,
                        'gelar' => null,
                    ]);
                }
            } else {
                $user->dosen->update([
                    'nama_dosen' => $user->name
                ]);
            }
        }
    }
}
