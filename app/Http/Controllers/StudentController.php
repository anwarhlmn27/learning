<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Role;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'prodi']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('nim', 'like', "%{$search}%")
              ->orWhere('nama_student', 'like', "%{$search}%");
        }

        if ($request->filled('prodi')) {
            $query->where('prodi_id', $request->prodi);
        }

        $students = $query->latest()->paginate(10)->withQueryString();
        $prodis = Prodi::orderBy('nama_prodi')->get();
        return view('admin.students.index', compact('students', 'prodis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|unique:students,nim',
            'nama_student' => 'required|string|max:255',
            'angkatan' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'email' => 'required|string|email|max:255|unique:users,email',
            'prodi_id' => 'required|exists:prodis,id',
            'password' => 'nullable|string|min:8',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->nama_student,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : Hash::make('LmsHorizon$01'),
                'status' => 'active',
            ]);

            $role = Role::where('name', 'mahasiswa')->first();
            if ($role) {
                $user->roles()->attach($role->id, ['id' => (string) Str::uuid()]);
            }

            Student::create([
                'user_id' => $user->id,
                'prodi_id' => $request->prodi_id,
                'nim' => $request->nim,
                'nama_student' => $request->nama_student,
                'angkatan' => $request->angkatan,
            ]);

            DB::commit();
            return redirect()->route('mahasiswa.index')->with('success', 'Data Mahasiswa berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan Data Mahasiswa: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nim' => 'required|string|unique:students,nim,' . $student->id,
            'nama_student' => 'required|string|max:255',
            'angkatan' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->user_id,
            'prodi_id' => 'required|exists:prodis,id',
        ]);

        DB::beginTransaction();
        try {
            $student->user->update([
                'name' => $request->nama_student,
                'email' => $request->email,
            ]);

            $student->update([
                'prodi_id' => $request->prodi_id,
                'nim' => $request->nim,
                'nama_student' => $request->nama_student,
                'angkatan' => $request->angkatan,
            ]);

            DB::commit();
            return redirect()->route('mahasiswa.index')->with('success', 'Data Mahasiswa berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui Data Mahasiswa: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Student $student)
    {
        DB::beginTransaction();
        try {
            $user = $student->user;
            $student->delete();
            if ($user) {
                $user->delete();
            }
            DB::commit();
            return redirect()->route('mahasiswa.index')->with('success', 'Data Mahasiswa berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('mahasiswa.index')->withErrors(['error' => 'Gagal menghapus data mahasiswa: ' . $e->getMessage()]);
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
        
        $role = Role::where('name', 'mahasiswa')->first();

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

                // Format CSV: email, kode_prodi, nim, nama_student, angkatan
                if (count($data) >= 5) {
                    $email = trim($data[0]);
                    $kode_prodi = trim($data[1]);
                    $nim = trim($data[2]);
                    $nama_student = trim($data[3]);
                    $angkatan = trim($data[4]);

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

                    $existingStudent = Student::where('nim', $nim)->first();
                    $existingUser = User::where('email', $email)->first();

                    if (!$existingStudent && !$existingUser) {
                        $user = User::create([
                            'name' => $nama_student,
                            'email' => $email,
                            'password' => Hash::make('LmsHorizon$01'),
                            'status' => 'active',
                        ]);

                        if ($role) {
                            $user->roles()->attach($role->id, ['id' => (string) Str::uuid()]);
                        }

                        Student::create([
                            'user_id' => $user->id,
                            'prodi_id' => $prodi->id,
                            'nim' => $nim,
                            'nama_student' => $nama_student,
                            'angkatan' => (int)$angkatan,
                        ]);
                        
                        $successCount++;
                    } else {
                        $errorRows[] = "Baris $rowCount: Email atau NIM sudah ada.";
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
            return back()->with('error', 'Gagal mengimpor data mahasiswa. ' . implode(' ', array_slice($errorRows, 0, 3)) . (count($errorRows) > 3 ? ' ...' : ''));
        }

        $message = "$successCount data mahasiswa berhasil diimport.";
        if ($skippedCount > 0) {
            $message .= " ($skippedCount data dilewati karena error: " . implode(' ', array_slice($errorRows, 0, 2)) . (count($errorRows) > 2 ? ' ...' : '') . ").";
        }

        return redirect()->route('mahasiswa.index')->with('success', $message);
    }

    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=student_import_template.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['email', 'kode_prodi', 'nim', 'nama_student', 'angkatan'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['mahasiswa@example.com', 'PRD01', '123456789', 'Siti Aminah', '2023']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
