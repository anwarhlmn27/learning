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
                $data['total_assignments'] = Assignment::whereIn('class_room_id', $classIds)->count();
            }
            $data['view_type'] = 'dosen';
        } else {
            $student = \App\Models\Student::where('user_id', $user->id)->first();
            if ($student) {
                $enrollments           = Enrollment::where('student_id', $student->id)->with(['classRoom.subject'])->get();
                $classIds              = $enrollments->pluck('class_room_id');
                $data['total_classes'] = $enrollments->count();
                $data['classes']       = $enrollments->map(fn($e) => $e->classRoom);
                $data['assignments']       = Assignment::whereIn('class_room_id', $classIds)->orderBy('deadline')->take(5)->get();
                $data['total_assignments'] = Assignment::whereIn('class_room_id', $classIds)->count();
            }
            $data['view_type'] = 'student';
        }

        $prodis = collect();
        return view('lms.dashboard', compact('data', 'user', 'prodis'));
    }
}
