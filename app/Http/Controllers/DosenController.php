<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\User;
use App\Models\Role;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dosen::with(['user', 'prodi']);

        // ── Filter by prodi ──────────────────────────────────────────────────
        $prodiId = $request->prodi_id ?? session('selected_prodi_id');
        $selectedProdi = null;
        if ($prodiId) {
            $selectedProdi = \App\Models\Prodi::find($prodiId);
            $query->where('prodi_id', $prodiId);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('nidn', 'like', "%{$search}%")
              ->orWhere('nama_dosen', 'like', "%{$search}%");
        }

        if ($request->filled('prodi')) {
            $query->where('prodi_id', $request->prodi);
        }

        $dosens = $query->latest()->paginate(10)->withQueryString();
        $prodis = Prodi::orderBy('nama_prodi')->get();
        return view('admin.dosens.index', compact('dosens', 'prodis', 'selectedProdi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nidn' => 'required|string|unique:dosens,nidn',
            'nama_dosen' => 'required|string|max:255',
            'gelar' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'prodi_id' => 'required|exists:prodis,id',
            'password' => 'nullable|string|min:8',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->nama_dosen,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : Hash::make('LmsHorizon$01'),
                'status' => 'active',
            ]);

            $role = Role::where('name', 'dosen')->first();
            if ($role) {
                $user->roles()->attach($role->id, ['id' => (string) Str::uuid()]);
            }

            Dosen::create([
                'user_id' => $user->id,
                'prodi_id' => $request->prodi_id,
                'nidn' => $request->nidn,
                'nama_dosen' => $request->nama_dosen,
                'gelar' => $request->gelar,
            ]);

            DB::commit();
            return redirect()->route('dosen.index')->with('success', 'Data Dosen berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan Data Dosen: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, Dosen $dosen)
    {
        $request->validate([
            'nidn' => 'required|string|unique:dosens,nidn,' . $dosen->id,
            'nama_dosen' => 'required|string|max:255',
            'gelar' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $dosen->user_id,
            'prodi_id' => 'required|exists:prodis,id',
        ]);

        DB::beginTransaction();
        try {
            $dosen->user->update([
                'name' => $request->nama_dosen,
                'email' => $request->email,
            ]);

            $dosen->update([
                'prodi_id' => $request->prodi_id,
                'nidn' => $request->nidn,
                'nama_dosen' => $request->nama_dosen,
                'gelar' => $request->gelar,
            ]);

            DB::commit();
            return redirect()->route('dosen.index')->with('success', 'Data Dosen berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui Data Dosen: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Dosen $dosen)
    {
        DB::beginTransaction();
        try {
            $user = $dosen->user;
            $dosen->delete();
            if ($user) {
                $user->delete();
            }
            DB::commit();
            return redirect()->route('dosen.index')->with('success', 'Data Dosen berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dosen.index')->withErrors(['error' => 'Gagal menghapus data dosen: ' . $e->getMessage()]);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        
        $fileHandle = fopen($file->path(), 'r');
        $firstLine = fgets($fileHandle);
        fclose($fileHandle);
        $delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';

        $handle = fopen($file->path(), 'r');
        $header = true;
        $successCount = 0;
        $skippedCount = 0;
        $errorRows = [];
        
        $role = Role::where('name', 'dosen')->first();

        DB::beginTransaction();
        try {
            $rowCount = 0;
            while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $rowCount++;
                if ($header) {
                    $header = false;
                    continue;
                }

                if (empty(array_filter($data))) continue;

                // Format CSV: email, kode_prodi, nidn, nama_dosen, gelar
                if (count($data) >= 5) {
                    $email = trim($data[0]);
                    $kode_prodi = trim($data[1]);
                    $nidn = trim($data[2]);
                    $nama_dosen = trim($data[3]);
                    $gelar = trim($data[4]);

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errorRows[] = "Baris $rowCount: Email tidak valid.";
                        $skippedCount++;
                        continue;
                    }

                    $prodi = Prodi::where('kode_prodi', $kode_prodi)->first();
                    if (!$prodi) {
                        $errorRows[] = "Baris $rowCount: Prodi dengan kode $kode_prodi tidak ditemukan.";
                        $skippedCount++;
                        continue;
                    }

                    $existingDosen = Dosen::where('nidn', $nidn)->first();
                    $existingUser = User::where('email', $email)->first();

                    if (!$existingDosen && !$existingUser) {
                        $user = User::create([
                            'name' => $nama_dosen,
                            'email' => $email,
                            'password' => Hash::make('LmsHorizon$01'),
                            'status' => 'active',
                        ]);

                        if ($role) {
                            $user->roles()->attach($role->id, ['id' => (string) Str::uuid()]);
                        }

                        Dosen::create([
                            'user_id' => $user->id,
                            'prodi_id' => $prodi->id,
                            'nidn' => $nidn,
                            'nama_dosen' => $nama_dosen,
                            'gelar' => $gelar,
                        ]);
                        
                        $successCount++;
                    } else {
                        $errorRows[] = "Baris $rowCount: Email atau NIDN sudah ada.";
                        $skippedCount++;
                    }
                } else {
                    $errorRows[] = "Baris $rowCount: Format kolom tidak sesuai (terdeteksi " . count($data) . " kolom).";
                    $skippedCount++;
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
            return back()->with('error', 'Gagal mengimpor data dosen. ' . implode(' ', array_slice($errorRows, 0, 3)) . (count($errorRows) > 3 ? ' ...' : ''));
        }

        $message = "$successCount data dosen berhasil diimport.";
        if ($skippedCount > 0) {
            $message .= " ($skippedCount data dilewati karena error: " . implode(' ', array_slice($errorRows, 0, 2)) . (count($errorRows) > 2 ? ' ...' : '') . ").";
        }

        return redirect()->route('dosen.index')->with('success', $message);
    }

    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=dosen_import_template.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['email', 'kode_prodi', 'nidn', 'nama_dosen', 'gelar'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['dosen@example.com', 'PRD01', '0123456789', 'Budi Santoso', 'S.Kom., M.Kom.']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
