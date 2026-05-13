<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ClassRoom;
use App\Models\Assignment;
use App\Models\Enrollment;

class LmsController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        $data = [
            'total_classes' => 0,
            'total_assignments' => 0,
            'classes' => [],
            'assignments' => []
        ];

        // Jika Dosen (berdasarkan role atau mengecek jika ada di tabel dosens)
        if ($user->hasRole('dosen')) {
            // Ambil data dosen terkait
            $dosen = \App\Models\Dosen::where('user_id', $user->id)->first();
            
            if ($dosen) {
                $classes = ClassRoom::where('dosen_id', $dosen->id)
                            ->where('is_active', true)
                            ->with('subject')
                            ->get();
                            
                $data['total_classes'] = $classes->count();
                $data['classes'] = $classes;
                
                // Tugas di kelas dosen tersebut
                $classIds = $classes->pluck('id');
                $assignments = Assignment::whereIn('class_room_id', $classIds)
                                ->orderBy('deadline', 'asc')
                                ->take(5)
                                ->get();
                                
                $data['total_assignments'] = Assignment::whereIn('class_room_id', $classIds)->count();
                $data['assignments'] = $assignments;
            }
            
            $data['view_type'] = 'dosen';
            
        } else {
            // Asumsi Mahasiswa (Student)
            $student = \App\Models\Student::where('user_id', $user->id)->first();
            
            if ($student) {
                $enrollments = Enrollment::where('student_id', $student->id)->with(['classRoom.subject'])->get();
                $classIds = $enrollments->pluck('class_room_id');
                
                $data['total_classes'] = $enrollments->count();
                $data['classes'] = $enrollments->map(function($e) { return $e->classRoom; });
                
                $assignments = Assignment::whereIn('class_room_id', $classIds)
                                ->orderBy('deadline', 'asc')
                                ->take(5)
                                ->get();
                                
                $data['total_assignments'] = Assignment::whereIn('class_room_id', $classIds)->count();
                $data['assignments'] = $assignments;
            }
            
            $data['view_type'] = 'student';
        }

        return view('lms.dashboard', compact('data', 'user'));
    }
}
