<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Dosen;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Rps;
use App\Models\ClassSession;
use App\Models\Assignment;
use App\Models\RpsSession;
use App\Models\RpsAssessment;
use App\Models\Quiz;
use App\Models\AssignmentSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ClassRoomController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ClassRoom::with(['subject'])->visible(); // exclude deleted

        // Non-admin / non-kaprodi only see classes they are enrolled in
        if (!$user->hasRole(['admin', 'kaprodi'])) {
            $query->whereHas('users', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // Default: only active classes on this page
        $query->where('status', 'active');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('tahun_akademik', 'like', "%{$search}%")
                  ->orWhereHas('subject', function($q2) use ($search) {
                      $q2->where('nama_subject', 'like', "%{$search}%");
                  });
            });
        }

        $classRooms = $query->latest()->paginate(10)->withQueryString();
        
        $subjects = Subject::orderBy('nama_subject')->get();
        $dosens = Dosen::orderBy('nama_dosen')->get();

        // Build a map of class_id => primary_dosen_id for the edit modal pre-selection
        $classPrimaryDosenMap = [];
        foreach ($classRooms as $cr) {
            $firstDosenUser = $cr->dosens()->first();
            if ($firstDosenUser && $firstDosenUser->dosen) {
                $classPrimaryDosenMap[$cr->id] = $firstDosenUser->dosen->id;
            } else {
                $classPrimaryDosenMap[$cr->id] = '';
            }
        }

        return view('lms.classes.index', compact('classRooms', 'subjects', 'dosens', 'classPrimaryDosenMap'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi'])) {
            return back()->with('error', 'Anda tidak memiliki akses untuk membuat kelas.');
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'dosen_id' => 'required|exists:dosens,id',
            'nama_kelas' => 'required|string|max:255',
            'tahun_akademik' => 'required|string|max:50',
            'semester' => 'required|in:Ganjil,Genap,Antara',
        ]);

        DB::transaction(function() use ($request) {
            $class = ClassRoom::create([
                'subject_id'    => $request->subject_id,
                'nama_kelas'    => $request->nama_kelas,
                'tahun_akademik'=> $request->tahun_akademik,
                'semester'      => $request->semester,
                'status'        => 'active',
            ]);

            // Add the lecturer to class_users pivot
            $dosen = Dosen::find($request->dosen_id);
            if ($dosen && $dosen->user_id) {
                $class->users()->attach($dosen->user_id, ['id' => (string) \Illuminate\Support\Str::uuid()]);
            }
        });

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil dibuat.');
    }

    public function update(Request $request, ClassRoom $class)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi'])) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah kelas.');
        }

        $request->validate([
            'subject_id'     => 'required|exists:subjects,id',
            'dosen_id'       => 'required|exists:dosens,id',
            'nama_kelas'     => 'required|string|max:255',
            'tahun_akademik' => 'required|string|max:50',
            'semester'       => 'required|in:Ganjil,Genap,Antara',
            'status'         => 'required|in:active,archived',
        ]);

        DB::transaction(function() use ($request, $class) {
            $class->update([
                'subject_id'     => $request->subject_id,
                'nama_kelas'     => $request->nama_kelas,
                'tahun_akademik' => $request->tahun_akademik,
                'semester'       => $request->semester,
                'status'         => $request->status,
            ]);

            // Replace primary dosen: detach ALL existing dosens, then attach the chosen one.
            $newDosen = Dosen::find($request->dosen_id);
            if ($newDosen && $newDosen->user_id) {
                $dosenRole = \App\Models\Role::where('name', 'dosen')->first();
                if ($dosenRole) {
                    $dosenUserIds = $class->users()
                        ->whereHas('roles', function($q) use ($dosenRole) {
                            $q->where('role_id', $dosenRole->id);
                        })
                        ->pluck('users.id')
                        ->toArray();
                    $class->users()->detach($dosenUserIds);
                }
                if (!$class->users()->where('user_id', $newDosen->user_id)->exists()) {
                    $class->users()->attach($newDosen->user_id, ['id' => (string) \Illuminate\Support\Str::uuid()]);
                }
            }
        });

        return redirect()->route('classes.index')->with('success', 'Data Kelas berhasil diperbarui.');
    }

    /**
     * Soft-delete: only allowed if class is archived OR has no content.
     * Active class with any topics/assignments cannot be deleted.
     */
    public function destroy(ClassRoom $class)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi'])) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus kelas.');
        }

        // Guard: active class with content cannot be deleted
        if ($class->status === 'active' && $class->hasActiveContent()) {
            return back()->with('error',
                'Kelas "' . $class->nama_kelas . '" tidak dapat dihapus karena masih berstatus Aktif dan memiliki kegiatan di dalamnya. '
                . 'Arsipkan kelas terlebih dahulu sebelum menghapusnya.');
        }

        // Soft delete: just mark as deleted
        $class->update(['status' => 'deleted']);

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil dihapus (soft-delete). Data tetap tersimpan di sistem.');
    }

    /**
     * Toggle status: active <-> archived (by dosen, baak, kaprodi, admin).
     */
    public function archive(Request $request, ClassRoom $class)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi', 'dosen', 'baak'])) {
            return back()->with('error', 'Anda tidak memiliki akses.');
        }

        // Only enrolled users or admins can archive
        if (!$user->hasRole(['admin', 'kaprodi'])) {
            $isEnrolled = $class->users()->where('user_id', $user->id)->exists();
            if (!$isEnrolled) {
                return back()->with('error', 'Anda tidak terdaftar di kelas ini.');
            }
        }

        if ($class->status === 'active') {
            $class->update(['status' => 'archived']);
            return back()->with('success', 'Kelas "' . $class->nama_kelas . '" berhasil diarsipkan. Semua konten menjadi read-only.');
        } elseif ($class->status === 'archived') {
            $class->update(['status' => 'active']);
            return back()->with('success', 'Kelas "' . $class->nama_kelas . '" berhasil diaktifkan kembali.');
        }

        return back()->with('error', 'Status kelas tidak valid untuk diubah.');
    }

    /**
     * Archived classes page.
     */
    public function archivedIndex(Request $request)
    {
        $user = Auth::user();

        $query = ClassRoom::with(['subject'])->where('status', 'archived');

        if (!$user->hasRole(['admin', 'kaprodi'])) {
            $query->whereHas('users', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhereHas('subject', function($q2) use ($search) {
                      $q2->where('nama_subject', 'like', "%{$search}%");
                  });
            });
        }

        $classRooms = $query->latest()->paginate(10)->withQueryString();

        return view('lms.classes.archived', compact('classRooms'));
    }

    public function show(ClassRoom $class)
    {
        $user = Auth::user();
        
        // Authorization check: non-admin / non-kaprodi must be enrolled in class_users
        if (!$user->hasRole(['admin', 'kaprodi'])) {
            $isEnrolled = $class->users()->where('user_id', $user->id)->exists();
            if (!$isEnrolled) {
                return back()->with('error', 'Anda tidak memiliki akses ke kelas ini.');
            }
        }

        $class->load(['subject', 'subject.prodi']);
        
        // 1. People / Peserta Tab data
        $enrollments = Enrollment::with('student.user')
                        ->where('class_room_id', $class->id)
                        ->latest()
                        ->get();
        
        $lecturers = $class->dosens()->get();
        $baakStaff = $class->baaks()->get();

        $prodiId = $class->subject->id_prodi ?? null;
        $availableStudents = [];
        if ($prodiId) {
            $enrolledStudentIds = $enrollments->pluck('student_id')->toArray();
            $availableStudents = Student::where('prodi_id', $prodiId)
                                    ->whereNotIn('id', $enrolledStudentIds)
                                    ->orderBy('nama_student')
                                    ->get();
        }

        // 2. Classwork / Sesi Tab data (timeline of 14 sessions)
        $topics = $class->topics()->with(['material', 'assignment', 'forum', 'quiz'])->get();
        
        $sessions = [];
        for ($i = 1; $i <= 14; $i++) {
            $sessions[$i] = [
                'number' => $i,
                'topics' => $topics->where('session_number', $i)
            ];
        }

        // 3. Penugasan & Nilai / Grades Tab data
        $assignments = Assignment::where('class_room_id', $class->id)->get();
        
        // Find quizzes belonging to this class (via topics)
        $quizIds = $topics->where('type', 'quiz')->pluck('content_id');
        $quizzes = Quiz::whereIn('id', $quizIds)->get();
        
        // Fetch submissions & attempts for gradebook
        $submissions = AssignmentSubmission::whereIn('assignment_id', $assignments->pluck('id'))
            ->get()
            ->groupBy('student_id');
        
        $quizAttempts = \App\Models\StudentQuizAttempt::whereIn('quiz_id', $quizzes->pluck('id'))
            ->get()
            ->groupBy('user_id');

        $subjects = Subject::orderBy('nama_subject')->get();
        $dosens = Dosen::orderBy('nama_dosen')->get();

        // People tab: get all dosen/baak user IDs already in this class so we can exclude them from dropdowns
        $enrolledDosenUserIds = $lecturers->pluck('id')->toArray();
        $enrolledBaakUserIds = $baakStaff->pluck('id')->toArray();

        // Available dosens to add (not already in this class)
        $availableDosens = Dosen::whereHas('user', function($q) use ($enrolledDosenUserIds) {
            $q->whereNotIn('id', $enrolledDosenUserIds);
        })->orderBy('nama_dosen')->get();

        // Available BAAK staff to add (not already in this class)
        $availableBaaks = User::whereHas('roles', function($q) {
            $q->where('name', 'baak');
        })->whereNotIn('id', $enrolledBaakUserIds)->orderBy('name')->get();

        return view('lms.classes.show', compact(
            'class', 
            'enrollments', 
            'availableStudents', 
            'lecturers', 
            'baakStaff', 
            'sessions', 
            'assignments', 
            'quizzes', 
            'submissions', 
            'quizAttempts',
            'subjects',
            'dosens',
            'availableDosens',
            'availableBaaks'
        ));
    }

    public function addStaff(Request $request, ClassRoom $class)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi'])) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengelola staff kelas.');
        }

        $request->validate([
            'staff_type' => 'required|in:dosen,baak',
            'user_id'   => 'required_if:staff_type,baak|nullable|exists:users,id',
            'dosen_id'  => 'required_if:staff_type,dosen|nullable|exists:dosens,id',
        ]);

        if ($request->staff_type === 'dosen') {
            $dosen = Dosen::find($request->dosen_id);
            if (!$dosen || !$dosen->user_id) {
                return back()->with('error', 'Data dosen tidak ditemukan.');
            }
            $targetUserId = $dosen->user_id;
            $label = $dosen->nama_dosen;
        } else {
            $targetUserId = $request->user_id;
            $baakUser = User::find($targetUserId);
            $label = $baakUser ? $baakUser->name : 'Staff BAAK';
        }

        if ($class->users()->where('user_id', $targetUserId)->exists()) {
            return back()->with('error', $label . ' sudah terdaftar di kelas ini.');
        }

        $class->users()->attach($targetUserId, ['id' => (string) \Illuminate\Support\Str::uuid()]);

        return back()->with('success', $label . ' berhasil ditambahkan ke kelas.');
    }

    public function removeStaff(ClassRoom $class, User $user)
    {
        $authUser = Auth::user();
        if (!$authUser->hasRole(['admin', 'kaprodi'])) {
            return back()->with('error', 'Anda tidak memiliki akses.');
        }

        $class->users()->detach($user->id);

        return back()->with('success', 'Staff berhasil dihapus dari kelas.');
    }

    public function generateLmsFromRps(ClassRoom $class)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi'])) {
            $isEnrolled = $class->users()->where('user_id', $user->id)->exists();
            if (!$isEnrolled) {
                return back()->with('error', 'Anda tidak memiliki akses ke kelas ini.');
            }
        }

        // Try to find an active RPS first
        $rps = Rps::where('subject_id', $class->subject_id)
                  ->where('status', 'active')
                  ->latest()
                  ->first();

        if (!$rps) {
            $rps = Rps::where('subject_id', $class->subject_id)->latest()->first();
        }

        if (!$rps) {
            return back()->with('error', 'Tidak ada data RPS yang ditemukan untuk mata kuliah ini.');
        }

        // Block import if RPS is still in Draft status
        if (strtolower($rps->status) === 'draft') {
            return back()->with('error', 'RPS "' . optional($rps->subject)->nama_subject . '" masih berstatus Draf dan belum dapat diimport. Silakan ubah status RPS menjadi Aktif terlebih dahulu.');
        }

        DB::beginTransaction();
        try {
            $rpsSessions = RpsSession::where('rps_id', $rps->id)->get();

            foreach ($rpsSessions as $session) {
                $classSession = ClassSession::firstOrCreate(
                    [
                        'class_room_id' => $class->id,
                        'rps_session_id' => $session->id,
                    ],
                    [
                        'title' => $session->topic_name,
                        'status' => 'Draft',
                    ]
                );

                $assessments = RpsAssessment::where('rps_session_id', $session->id)->get();
                foreach ($assessments as $assessment) {
                    // Create Assignment
                    $assignment = Assignment::firstOrCreate(
                        [
                            'class_room_id' => $class->id,
                            'rps_assessment_id' => $assessment->id,
                        ],
                        [
                            'class_session_id' => $classSession->id,
                            'title' => 'Tugas Sesi ' . $session->session_number . ': ' . $session->topic_name,
                            'instruction' => $assessment->assignment_activities ?? 'Silakan kerjakan tugas sesuai arahan dosen.',
                            'deadline' => now()->addDays(7),
                            'status' => 'Draft',
                        ]
                    );

                    // Sync dynamic classwork timeline (class_topics)
                    \App\Models\ClassTopic::firstOrCreate(
                        [
                            'class_room_id' => $class->id,
                            'session_number' => $session->session_number,
                            'type' => 'assignment',
                            'content_id' => $assignment->id,
                        ],
                        [
                            'title' => $assignment->title,
                        ]
                    );
                }
            }

            DB::commit();
            return back()->with('success', 'Struktur LMS, Tugas, dan Timeline berhasil digenerate dari RPS.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function enroll(Request $request, ClassRoom $class)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $exists = Enrollment::where('class_room_id', $class->id)
                            ->where('student_id', $request->student_id)
                            ->exists();

        if ($exists) {
            return back()->with('error', 'Mahasiswa tersebut sudah terdaftar di kelas ini.');
        }

        Enrollment::create([
            'class_room_id' => $class->id,
            'student_id' => $request->student_id,
        ]);

        return back()->with('success', 'Mahasiswa berhasil ditambahkan ke kelas.');
    }

    public function unenroll(ClassRoom $class, Enrollment $enrollment)
    {
        if ($enrollment->class_room_id !== $class->id) {
            return back()->with('error', 'Data enrollment tidak valid.');
        }

        $enrollment->delete();
        return back()->with('success', 'Mahasiswa berhasil dihapus dari kelas.');
    }

    public function importStudents(Request $request, ClassRoom $class)
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

                // Format CSV: nim
                if (isset($data[0])) {
                    $nim = trim($data[0]);

                    if (empty($nim)) continue;

                    $student = Student::where('nim', $nim)->first();
                    
                    if (!$student) {
                        $errorRows[] = "Baris $rowCount: Mahasiswa dengan NIM $nim tidak ditemukan.";
                        $skippedCount++;
                        continue;
                    }

                    $exists = Enrollment::where('class_room_id', $class->id)
                                        ->where('student_id', $student->id)
                                        ->exists();

                    if (!$exists) {
                        Enrollment::create([
                            'class_room_id' => $class->id,
                            'student_id' => $student->id,
                        ]);
                        $successCount++;
                    } else {
                        $errorRows[] = "Baris $rowCount: Mahasiswa (NIM: $nim) sudah terdaftar.";
                        $skippedCount++;
                    }
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
            return back()->with('error', 'Gagal mengimpor data. ' . implode(' ', array_slice($errorRows, 0, 3)) . (count($errorRows) > 3 ? ' ...' : ''));
        }

        $message = "$successCount mahasiswa berhasil ditambahkan ke kelas.";
        if ($skippedCount > 0) {
            $message .= " ($skippedCount data dilewati karena error: " . implode(' ', array_slice($errorRows, 0, 2)) . (count($errorRows) > 2 ? ' ...' : '') . ").";
        }

        return back()->with('success', $message);
    }

    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=enrollment_import_template.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['nim'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['123456789']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function storeMaterial(Request $request, ClassRoom $class)
    {
        $request->validate([
            'session_number' => 'required|integer|min:1|max:14',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
        ]);

        $material = \App\Models\Material::create([
            'class_room_id' => $class->id,
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
        ]);

        \App\Models\ClassTopic::create([
            'class_room_id' => $class->id,
            'session_number' => $request->session_number,
            'type' => 'materi',
            'content_id' => $material->id,
            'title' => $material->title,
        ]);

        return back()->with('success', 'Materi berhasil ditambahkan pada Sesi ' . $request->session_number);
    }

    public function storeAssignment(Request $request, ClassRoom $class)
    {
        $request->validate([
            'session_number' => 'required|integer|min:1|max:14',
            'title' => 'required|string|max:255',
            'instruction' => 'required|string',
            'deadline' => 'required|date',
            'rps_assessment_id' => 'nullable|exists:rps_assessments,id',
        ]);

        $assignment = Assignment::create([
            'class_room_id' => $class->id,
            'title' => $request->title,
            'instruction' => $request->instruction,
            'deadline' => $request->deadline,
            'rps_assessment_id' => $request->rps_assessment_id,
            'status' => 'Published',
        ]);

        \App\Models\ClassTopic::create([
            'class_room_id' => $class->id,
            'session_number' => $request->session_number,
            'type' => 'assignment',
            'content_id' => $assignment->id,
            'title' => $assignment->title,
        ]);

        return back()->with('success', 'Tugas berhasil ditambahkan pada Sesi ' . $request->session_number);
    }

    public function storeForum(Request $request, ClassRoom $class)
    {
        $request->validate([
            'session_number' => 'required|integer|min:1|max:14',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $forum = \App\Models\Forum::create([
            'class_room_id' => $class->id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        \App\Models\ClassTopic::create([
            'class_room_id' => $class->id,
            'session_number' => $request->session_number,
            'type' => 'forum',
            'content_id' => $forum->id,
            'title' => $forum->title,
        ]);

        return back()->with('success', 'Forum diskusi berhasil ditambahkan pada Sesi ' . $request->session_number);
    }

    public function storeQuiz(Request $request, ClassRoom $class)
    {
        $request->validate([
            'session_number' => 'required|integer|min:1|max:14',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'rps_assessment_id' => 'nullable|exists:rps_assessments,id',
        ]);

        $quiz = \App\Models\Quiz::create([
            'class_room_id' => $class->id,
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'rps_assessment_id' => $request->rps_assessment_id,
        ]);

        \App\Models\ClassTopic::create([
            'class_room_id' => $class->id,
            'session_number' => $request->session_number,
            'type' => 'quiz',
            'content_id' => $quiz->id,
            'title' => $quiz->title,
        ]);

        // Add 3 mock multiple-choice questions for demonstration so the quiz is immediately playable!
        $questions = [
            [
                'question_text' => 'Apakah kepanjangan dari OBE dalam kurikulum pendidikan?',
                'options' => json_encode(['Outcome Based Education', 'Objective Based Evaluation', 'Online Basic Education', 'Operational Business Environment']),
                'correct_option' => 'Outcome Based Education',
            ],
            [
                'question_text' => 'Siapakah yang berkewajiban menyusun RPS di perguruan tinggi?',
                'options' => json_encode(['Dosen Pengampu', 'Mahasiswa', 'Staff BAAK', 'Rektor']),
                'correct_option' => 'Dosen Pengampu',
            ],
            [
                'question_text' => 'Berapakah jumlah pertemuan tatap muka ideal dalam 1 semester?',
                'options' => json_encode(['16 Pertemuan (termasuk UTS/UAS)', '12 Pertemuan', '8 Pertemuan', '20 Pertemuan']),
                'correct_option' => '16 Pertemuan (termasuk UTS/UAS)',
            ]
        ];

        foreach ($questions as $q) {
            \App\Models\QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_text' => $q['question_text'],
                'options' => $q['options'],
                'correct_option' => $q['correct_option'],
            ]);
        }

        return back()->with('success', 'Kuis Pilihan Ganda beserta 3 Soal Mock berhasil ditambahkan pada Sesi ' . $request->session_number);
    }

    public function takeQuiz(ClassRoom $class, Quiz $quiz)
    {
        $quiz->load('questions');
        return view('lms.classes.take_quiz', compact('class', 'quiz'));
    }

    public function submitQuiz(Request $request, ClassRoom $class, Quiz $quiz)
    {
        $user = Auth::user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        if (!$student) {
            return back()->with('error', 'Hanya mahasiswa yang dapat mengikuti kuis.');
        }

        $attempt = \App\Models\StudentQuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'score' => 0,
            'started_at' => now(),
            'submitted_at' => now(),
        ]);

        $answers = $request->input('answers', []);
        foreach ($answers as $questionId => $selectedOption) {
            $question = \App\Models\QuizQuestion::find($questionId);
            $isCorrect = false;
            if ($question && strtolower(trim($question->correct_option)) === strtolower(trim($selectedOption))) {
                $isCorrect = true;
            }

            \App\Models\StudentQuizAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $questionId,
                'selected_option' => $selectedOption,
                'is_correct' => $isCorrect,
            ]);
        }

        // Run auto-grading & OBE gradebook synchronization!
        $finalScore = $attempt->gradeAttempt();

        return redirect()->route('classes.show', $class)->with('success', 'Kuis selesai dikerjakan! Nilai Anda: ' . $finalScore . '/100 (Nilai langsung disinkronisasi ke Pusat Nilai / OBE Analytics)');
    }
}
