<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\StudentGrade;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('mahasiswa')) {
            // Mahasiswa To-Do List
            if (!$user->student) {
                return back()->with('error', 'Data mahasiswa tidak valid.');
            }

            $enrolledClassIds = Enrollment::where('student_id', $user->student->id)->pluck('class_room_id');
            
            $assignments = Assignment::with(['classRoom.subject', 'submissions' => function($q) use ($user) {
                $q->where('student_id', $user->student->id);
            }])
            ->whereIn('class_room_id', $enrolledClassIds)
            ->where('status', 'Published') // Only show published ones
            ->orderBy('deadline', 'asc')
            ->paginate(15);

            return view('lms.assignments.student_index', compact('assignments'));
        } 
        
        if ($user->hasRole(['admin', 'kaprodi', 'dosen'])) {
            // Dosen Grading Center
            $query = Assignment::with(['classRoom.subject', 'classRoom.dosens']);
            
            if ($user->hasRole('dosen') && !$user->hasRole(['admin', 'kaprodi'])) {
                if (!$user->dosen) {
                    return back()->with('error', 'Data dosen tidak valid.');
                }
                $query->whereHas('classRoom.users', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }

            $assignments = $query->orderBy('created_at', 'desc')->paginate(15);
            return view('lms.assignments.dosen_index', compact('assignments'));
        }

        return redirect()->route('dashboard')->with('error', 'Akses tidak diizinkan.');
    }

    public function show(Assignment $assignment)
    {
        $user = Auth::user();

        if ($user->hasRole('mahasiswa')) {
            if (!$user->student) {
                return back()->with('error', 'Data mahasiswa tidak valid.');
            }
            
            $assignment->load('classRoom.subject');
            
            $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                                              ->where('student_id', $user->student->id)
                                              ->first();
                                              
            return view('lms.assignments.student_show', compact('assignment', 'submission'));
        }

        if ($user->hasRole(['admin', 'kaprodi', 'dosen'])) {
            $assignment->load(['classRoom.subject', 'submissions.student.user']);
            
            // Check if user is the assigned dosen
            if ($user->hasRole('dosen') && !$user->hasRole(['admin', 'kaprodi'])) {
                $isEnrolled = $assignment->classRoom->users()->where('user_id', $user->id)->exists();
                if (!$isEnrolled) {
                    return back()->with('error', 'Anda tidak memiliki akses ke tugas ini.');
                }
            }

            // Also load grades
            $grades = StudentGrade::where('rps_assessment_id', $assignment->rps_assessment_id)->get()->keyBy('enrollment_id');
            
            $enrollments = Enrollment::where('class_room_id', $assignment->class_room_id)->get();

            return view('lms.assignments.dosen_show', compact('assignment', 'enrollments', 'grades'));
        }

        return redirect()->route('dashboard');
    }

    public function publish(Assignment $assignment)
    {
        $assignment->update(['status' => 'Published']);
        return back()->with('success', 'Assignment berhasil dipublish ke mahasiswa.');
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $user = Auth::user();
        if (!$user->hasRole('mahasiswa') || !$user->student) {
            return back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'text_answer' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // max 10MB
        ]);

        if (!$request->text_answer && !$request->file('file')) {
            return back()->with('error', 'Harap isi teks jawaban atau unggah file.');
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('submissions', 'public');
        }

        $isLate = now()->gt($assignment->deadline);

        AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'student_id' => $user->student->id,
            ],
            [
                'text_answer' => $request->text_answer,
                'file_path' => $filePath,
                'status' => $isLate ? 'Late' : 'Submitted',
            ]
        );

        return back()->with('success', 'Tugas berhasil dikumpulkan.');
    }

    public function grade(Request $request, Assignment $assignment, Enrollment $enrollment)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi', 'dosen'])) {
            return back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update submission status and feedback
            $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                                              ->where('student_id', $enrollment->student_id)
                                              ->first();
            
            if ($submission) {
                $submission->update([
                    'status' => 'Graded',
                    'feedback' => $request->feedback,
                ]);
            }

            // Save to StudentGrade table for OBE Analytics
            StudentGrade::updateOrCreate(
                [
                    'enrollment_id' => $enrollment->id,
                    'rps_assessment_id' => $assignment->rps_assessment_id,
                ],
                [
                    'score' => $request->score,
                ]
            );

            DB::commit();
            return back()->with('success', 'Nilai berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }
}
