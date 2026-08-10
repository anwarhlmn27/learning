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

        // All prodis for staff/admin/kaprodi view
        $prodis = Prodi::withoutGlobalScopes()->with(['fakultas', 'kaprodi'])->orderBy('nama_prodi')->get();

        $data = [
            'total_classes'     => 0,
            'total_assignments' => 0,
            'classes'           => collect(),
            'assignments'       => collect(),
            'view_type'         => 'user',
        ];

        // 1. Check if user is a lecturer or enrolled in class_users
        $teachingClasses = ClassRoom::whereHas('users', fn($q) => $q->where('user_id', $user->id))
            ->where('status', 'active')
            ->with(['subject'])
            ->get();

        if ($teachingClasses->count() > 0 || $user->hasRole('dosen')) {
            $data['total_classes']     = $teachingClasses->count();
            $data['classes']           = $teachingClasses;
            $classIds                  = $teachingClasses->pluck('id');
            $data['assignments']       = Assignment::whereIn('class_room_id', $classIds)->orderBy('deadline')->take(5)->get();
            
            $assignmentIds = Assignment::whereIn('class_room_id', $classIds)->pluck('id');
            $data['total_assignments'] = \App\Models\AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
                ->whereIn('status', ['Submitted', 'Late'])
                ->count();
            $data['view_type'] = 'dosen';
        } elseif ($user->student) {
            $student = $user->student;
            $enrollments = Enrollment::where('student_id', $student->id)
                ->whereHas('classRoom', function($q) {
                    $q->where('status', 'active');
                })
                ->with(['classRoom.subject'])
                ->get();
            $classIds              = $enrollments->pluck('class_room_id');
            $data['total_classes'] = $enrollments->count();
            $data['classes']       = $enrollments->map(fn($e) => $e->classRoom);
            
            $assignments = Assignment::whereIn('class_room_id', $classIds)->get();
            $assignmentIds = $assignments->pluck('id');
            
            $submittedAssignmentIds = \App\Models\AssignmentSubmission::where('student_id', $student->id)
                ->whereIn('assignment_id', $assignmentIds)
                ->pluck('assignment_id')
                ->toArray();

            $data['total_assignments'] = $assignments->whereNotIn('id', $submittedAssignmentIds)->count();
            
            $data['assignments']       = Assignment::whereIn('class_room_id', $classIds)
                ->whereNotIn('id', $submittedAssignmentIds)
                ->orderBy('deadline')
                ->take(5)
                ->get();
            $data['view_type'] = 'student';
        }

        return view('lms.dashboard', compact('data', 'user', 'prodis'));
    }
}
