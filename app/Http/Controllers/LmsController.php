<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ClassRoom;
use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\Prodi;

class LmsController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // ── Kaprodi: auto-scope to their prodi ──────────────────────────────
        if ($user->hasRole('kaprodi')) {
            $prodi = Prodi::where('kaprodi_id', $user->id)->first();
            if ($prodi) {
                session(['selected_prodi_id' => $prodi->id]);
                return redirect()->route('classes.index', ['prodi_id' => $prodi->id]);
            }
        }

        // ── Admin / Rektor / Dekan: show prodi picker ────────────────────────
        if ($user->hasRole(['admin', 'rektor', 'dekan'])) {
            $prodis = Prodi::with(['fakultas', 'kaprodi'])->orderBy('nama_prodi')->get();
            return view('lms.dashboard', compact('user', 'prodis'));
        }

        // ── Dosen / Mahasiswa: show personal dashboard ──────────────────────
        $data = [
            'total_classes'     => 0,
            'total_assignments' => 0,
            'classes'           => [],
            'assignments'       => [],
        ];

        if ($user->hasRole('dosen')) {
            $dosen = \App\Models\Dosen::where('user_id', $user->id)->first();
            if ($dosen) {
                $classes = ClassRoom::whereHas('users', fn($q) => $q->where('user_id', $user->id))
                    ->where('status', 'active')->with('subject')->get();
                $data['total_classes']     = $classes->count();
                $data['classes']           = $classes;
                $classIds                  = $classes->pluck('id');
                $data['assignments']       = Assignment::whereIn('class_room_id', $classIds)->orderBy('deadline')->take(5)->get();
                
                // Tugas Perlu Dinilai: hitung semua pengumpulan tugas mahasiswa yang berstatus 'Submitted' atau 'Late' di kelas-kelas dosen ini
                $assignmentIds = Assignment::whereIn('class_room_id', $classIds)->pluck('id');
                $data['total_assignments'] = \App\Models\AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
                    ->whereIn('status', ['Submitted', 'Late'])
                    ->count();
            }
            $data['view_type'] = 'dosen';
        } else {
            $student = \App\Models\Student::where('user_id', $user->id)->first();
            if ($student) {
                $enrollments           = Enrollment::where('student_id', $student->id)->with(['classRoom.subject'])->get();
                $classIds              = $enrollments->pluck('class_room_id');
                $data['total_classes'] = $enrollments->count();
                $data['classes']       = $enrollments->map(fn($e) => $e->classRoom);
                
                // Ambil daftar assignment di kelas yang diiikuti mahasiswa
                $assignments = Assignment::whereIn('class_room_id', $classIds)->get();
                $assignmentIds = $assignments->pluck('id');
                
                // Cari assignment yang sudah dikumpulkan oleh mahasiswa ini
                $submittedAssignmentIds = \App\Models\AssignmentSubmission::where('student_id', $student->id)
                    ->whereIn('assignment_id', $assignmentIds)
                    ->pluck('assignment_id')
                    ->toArray();

                // Tugas Tertunda: tugas di kelas terdaftar yang BELUM dikumpulkan
                $data['total_assignments'] = $assignments->whereNotIn('id', $submittedAssignmentIds)->count();
                
                // Tampilkan hanya tugas mendatang yang belum dikumpulkan
                $data['assignments']       = Assignment::whereIn('class_room_id', $classIds)
                    ->whereNotIn('id', $submittedAssignmentIds)
                    ->orderBy('deadline')
                    ->take(5)
                    ->get();
            }
            $data['view_type'] = 'student';
        }

        $prodis = collect();
        return view('lms.dashboard', compact('data', 'user', 'prodis'));
    }
}
