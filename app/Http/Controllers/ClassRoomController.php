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
        $user  = Auth::user();
        $query = ClassRoom::with(['subject'])->visible();

        // Non-admin / non-kaprodi only see their own classes
        if (!$user->hasRole(['admin', 'kaprodi'])) {
            $query->where(function($q) use ($user) {
                $q->whereHas('users', fn($q2) => $q2->where('user_id', $user->id));
                if ($user->student) {
                    $q->orWhereHas('enrollments', fn($q3) => $q3->where('student_id', $user->student->id));
                }
            });
        }

        $query->where('status', 'active');

        // ── Filter by prodi (passed from dashboard picker or kaprodi auto-scope) ──
        $prodiId = $request->prodi_id ?? session('selected_prodi_id');
        $selectedProdi = null;
        if ($prodiId) {
            $selectedProdi = \App\Models\Prodi::find($prodiId);
            $query->whereHas('subject', fn($q) => $q->where('id_prodi', $prodiId));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('tahun_akademik', 'like', "%{$search}%")
                  ->orWhereHas('subject', fn($q2) => $q2->where('nama_subject', 'like', "%{$search}%"));
            });
        }

        $classRooms = $query->latest()->paginate(10)->withQueryString();

        // Filter subjects/dosens by prodi if selected
        $subjects = $selectedProdi
            ? Subject::where('id_prodi', $prodiId)->orderBy('nama_subject')->get()
            : Subject::orderBy('nama_subject')->get();

        $dosens = $selectedProdi
            ? Dosen::where('prodi_id', $prodiId)->orderBy('nama_dosen')->get()
            : Dosen::orderBy('nama_dosen')->get();

        $classPrimaryDosenMap = [];
        foreach ($classRooms as $cr) {
            $firstDosenUser = $cr->dosens()->first();
            if ($firstDosenUser && $firstDosenUser->dosen) {
                $classPrimaryDosenMap[$cr->id] = $firstDosenUser->dosen->id;
            } else {
                $classPrimaryDosenMap[$cr->id] = '';
            }
        }

        return view('lms.classes.index', compact('classRooms', 'subjects', 'dosens', 'classPrimaryDosenMap', 'selectedProdi'));
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
            $query->where(function($q) use ($user) {
                $q->whereHas('users', fn($q2) => $q2->where('user_id', $user->id));
                if ($user->student) {
                    $q->orWhereHas('enrollments', fn($q3) => $q3->where('student_id', $user->student->id));
                }
            });
        }

        // ── Filter by prodi ──
        $prodiId = $request->prodi_id ?? session('selected_prodi_id');
        $selectedProdi = null;
        if ($prodiId) {
            $selectedProdi = \App\Models\Prodi::find($prodiId);
            $query->whereHas('subject', fn($q) => $q->where('id_prodi', $prodiId));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhereHas('subject', fn($q2) => $q2->where('nama_subject', 'like', "%{$search}%"));
            });
        }

        $classRooms = $query->latest()->paginate(10)->withQueryString();
        return view('lms.classes.archived', compact('classRooms', 'selectedProdi'));
    }

    public function show(ClassRoom $class)
    {
        $user = Auth::user();
        
        // Authorization check: non-admin / non-kaprodi must be enrolled in class_users or enrollments
        if (!$user->hasRole(['admin', 'kaprodi'])) {
            $isStaff = $class->users()->where('user_id', $user->id)->exists();
            $isStudent = $user->student && $class->enrollments()->where('student_id', $user->student->id)->exists();
            
            if (!$isStaff && !$isStudent) {
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
            $isStaff = $class->users()->where('user_id', $user->id)->exists();
            if (!$isStaff) {
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

                // Fetch existing assignments for this session via ClassTopic
                $existingAssignmentTopics = \App\Models\ClassTopic::where('class_room_id', $class->id)
                    ->where('session_number', $session->session_number)
                    ->where('type', 'assignment')
                    ->orderBy('created_at')
                    ->get();

                $assessments = RpsAssessment::where('rps_session_id', $session->id)->get();
                foreach ($assessments as $index => $assessment) {
                    $title = 'Tugas Sesi ' . $session->session_number . ': ' . $session->topic_name;
                    $instruction = $assessment->assignment_activities ?? 'Silakan kerjakan tugas sesuai arahan dosen.';

                    if (isset($existingAssignmentTopics[$index])) {
                        // Update existing assignment if it exists, otherwise create new (might be orphaned due to cascade delete)
                        $assignment = Assignment::find($existingAssignmentTopics[$index]->content_id);
                        if ($assignment) {
                            $assignment->update([
                                'rps_assessment_id' => $assessment->id,
                                'class_session_id' => $classSession->id,
                                'title' => $title,
                                'instruction' => $instruction,
                            ]);
                            $existingAssignmentTopics[$index]->update(['title' => $title]);
                        } else {
                            $assignment = Assignment::create([
                                'class_room_id' => $class->id,
                                'rps_assessment_id' => $assessment->id,
                                'class_session_id' => $classSession->id,
                                'title' => $title,
                                'instruction' => $instruction,
                                'deadline' => now()->addDays(7),
                                'status' => 'Draft',
                            ]);
                            $existingAssignmentTopics[$index]->update([
                                'content_id' => $assignment->id,
                                'title' => $title
                            ]);
                        }
                    } else {
                        // Create Assignment
                        $assignment = Assignment::firstOrCreate(
                            [
                                'class_room_id' => $class->id,
                                'rps_assessment_id' => $assessment->id,
                            ],
                            [
                                'class_session_id' => $classSession->id,
                                'title' => $title,
                                'instruction' => $instruction,
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

                // Copy Resources
                $resources = \App\Models\SessionResource::where('rps_session_id', $session->id)->get();
                
                $existingMaterialTopics = \App\Models\ClassTopic::where('class_room_id', $class->id)
                    ->where('session_number', $session->session_number)
                    ->where('type', 'materi')
                    ->orderBy('created_at')
                    ->get();

                foreach ($resources as $index => $res) {
                    $material = \App\Models\Material::where('rps_resource_id', $res->id)->first();
                    
                    if (!$material && isset($existingMaterialTopics[$index])) {
                        // Try to update existing material in the same slot
                        $material = \App\Models\Material::find($existingMaterialTopics[$index]->content_id);
                        if ($material) {
                            $material->update([
                                'rps_resource_id' => $res->id,
                                'title' => $res->nm_resource,
                                'file_path' => json_encode([$res->file_path]),
                                'original_filename' => json_encode([$res->nm_resource]),
                            ]);
                            $existingMaterialTopics[$index]->update(['title' => $material->title]);
                        } else {
                            $material = \App\Models\Material::create([
                                'rps_resource_id' => $res->id,
                                'title' => $res->nm_resource,
                                'file_path' => json_encode([$res->file_path]),
                                'original_filename' => json_encode([$res->nm_resource]),
                            ]);
                            $existingMaterialTopics[$index]->update([
                                'content_id' => $material->id,
                                'title' => $material->title
                            ]);
                        }
                    }
                    
                    if (!$material) {
                        $material = \App\Models\Material::create([
                            'rps_resource_id' => $res->id,
                            'title' => $res->nm_resource,
                            'file_path' => json_encode([$res->file_path]),
                            'original_filename' => json_encode([$res->nm_resource]),
                        ]);

                        \App\Models\ClassTopic::firstOrCreate(
                            [
                                'class_room_id' => $class->id,
                                'session_number' => $session->session_number,
                                'type' => 'materi',
                                'content_id' => $material->id,
                            ],
                            [
                                'title' => $material->title,
                            ]
                        );
                    }
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
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $enrolledCount = 0;
        foreach ($request->student_ids as $studentId) {
            $exists = Enrollment::where('class_room_id', $class->id)
                                ->where('student_id', $studentId)
                                ->exists();

            if (!$exists) {
                Enrollment::create([
                    'class_room_id' => $class->id,
                    'student_id' => $studentId,
                ]);
                $enrolledCount++;
            }
        }

        if ($enrolledCount > 0) {
            return back()->with('success', $enrolledCount . ' mahasiswa berhasil ditambahkan ke kelas.');
        }

        return back()->with('info', 'Mahasiswa yang dipilih sudah terdaftar di kelas ini.');
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

    private function convertToPdf(string $filePath)
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($extension, ['doc', 'docx', 'ppt', 'pptx'])) {
            return $filePath;
        }

        $sourcePath = \Illuminate\Support\Facades\Storage::path($filePath);
        $newFileName = preg_replace('/\.(doc|docx|ppt|pptx)$/i', '.pdf', basename($filePath));
        $newFilePath = 'materials/' . $newFileName;
        $targetPath = \Illuminate\Support\Facades\Storage::path($newFilePath);

        try {
            $scriptPath = base_path('convert_to_pdf.ps1');

            // Run the PowerShell conversion script with a strict 15-second timeout
            $result = \Illuminate\Support\Facades\Process::timeout(15)->run([
                'powershell',
                '-ExecutionPolicy', 'Bypass',
                '-File', $scriptPath,
                '-SourcePath', $sourcePath,
                '-TargetPath', $targetPath
            ]);

            $output = $result->output() . ' ' . $result->errorOutput();

            if ($result->successful() && strpos($output, 'SUCCESS') !== false && \Illuminate\Support\Facades\Storage::exists($newFilePath)) {
                // Delete original file
                if (\Illuminate\Support\Facades\Storage::exists($filePath)) {
                    \Illuminate\Support\Facades\Storage::delete($filePath);
                }
                return $newFilePath;
            } else {
                \Illuminate\Support\Facades\Log::warning("PDF Conversion failed or timed out. Output: " . $output);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("PDF Conversion Exception: " . $e->getMessage());
        }

        return $filePath; // Fallback to original file
    }

    public function storeMaterial(Request $request, ClassRoom $class)
    {
        $request->validate([
            'session_number' => 'required|integer|min:1|max:14',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'links.*' => 'nullable|url',
            'files.*' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:2048',
        ]);

        $filePaths = [];
        $originalFilenames = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $filename = \Illuminate\Support\Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('materials', $filename);

                // Convert doc/docx/ppt/pptx to pdf
                $convertedFilePath = $this->convertToPdf($filePath);
                if ($convertedFilePath !== $filePath) {
                    $filePath = $convertedFilePath;
                    // Change original filename's extension to .pdf
                    $originalName = preg_replace('/\.(doc|docx|ppt|pptx)$/i', '.pdf', $originalName);
                }

                $filePaths[] = $filePath;
                $originalFilenames[] = $originalName;
            }
        }

        $links = [];
        if ($request->has('links')) {
            $links = array_values(array_filter($request->links));
        }

        $material = \App\Models\Material::create([
            'title' => $request->title,
            'description' => $request->description,
            'link_url' => count($links) > 0 ? json_encode($links) : null,
            'file_path' => count($filePaths) > 0 ? json_encode($filePaths) : null,
            'original_filename' => count($originalFilenames) > 0 ? json_encode($originalFilenames) : null,
        ]);

        \App\Models\ClassTopic::create([
            'class_room_id' => $class->id,
            'session_number' => $request->session_number,
            'type' => 'materi',
            'content_id' => $material->id,
            'title' => $material->title,
        ]);

        // Share to RPS if checked
        if ($request->has('share_to_rps') && $request->share_to_rps == '1') {
            $rps = \App\Models\Rps::where('subject_id', $class->subject_id)
                      ->where('status', 'active')
                      ->latest()
                      ->first();
            if ($rps) {
                $rpsSession = \App\Models\RpsSession::where('rps_id', $rps->id)
                                ->where('session_number', $request->session_number)
                                ->first();
                if ($rpsSession) {
                    foreach ($filePaths as $index => $path) {
                        $nm_resource = $request->title;
                        if (count($filePaths) > 1 && isset($originalFilenames[$index])) {
                            $nm_resource = $originalFilenames[$index];
                        }
                        
                        $sessionResource = \App\Models\SessionResource::create([
                            'rps_session_id' => $rpsSession->id,
                            'nm_resource' => $nm_resource,
                            'type' => 'Modul',
                            'file_path' => $path,
                        ]);
                        
                        // Link the material back to the RPS resource so it doesn't get duplicated if pulled again
                        if ($index === 0) {
                            $material->rps_resource_id = $sessionResource->id;
                            $material->save();
                        }
                    }
                }
            }
        }

        return back()->with('success', 'Materi berhasil ditambahkan pada Sesi ' . $request->session_number);
    }

    public function updateMaterial(Request $request, ClassRoom $class, \App\Models\Material $material)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'links.*' => 'nullable|url',
            'files.*' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:2048',
        ]);

        $filePaths = $material->file_paths;
        $originalFilenames = $material->original_filenames;

        // Handle deleted files
        if ($request->has('deleted_files')) {
            foreach ($request->deleted_files as $deletedPath) {
                $idx = array_search($deletedPath, $filePaths);
                if ($idx !== false) {
                    if (\Illuminate\Support\Facades\Storage::exists($deletedPath)) {
                        \Illuminate\Support\Facades\Storage::delete($deletedPath);
                    }
                    unset($filePaths[$idx]);
                    unset($originalFilenames[$idx]);
                }
            }
            $filePaths = array_values($filePaths);
            $originalFilenames = array_values($originalFilenames);
        }

        // Handle newly uploaded files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $filename = \Illuminate\Support\Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('materials', $filename);

                // Convert doc/docx/ppt/pptx to pdf
                $convertedFilePath = $this->convertToPdf($filePath);
                if ($convertedFilePath !== $filePath) {
                    $filePath = $convertedFilePath;
                    // Change original filename's extension to .pdf
                    $originalName = preg_replace('/\.(doc|docx|ppt|pptx)$/i', '.pdf', $originalName);
                }

                $filePaths[] = $filePath;
                $originalFilenames[] = $originalName;
            }
        }

        // Handle link URLs
        $linkUrls = $material->link_urls;

        // Handle deleted links
        if ($request->has('deleted_links')) {
            foreach ($request->deleted_links as $deletedLink) {
                $idx = array_search($deletedLink, $linkUrls);
                if ($idx !== false) {
                    unset($linkUrls[$idx]);
                }
            }
            $linkUrls = array_values($linkUrls);
        }

        // Handle newly added links
        if ($request->has('links')) {
            $newLinks = array_values(array_filter($request->links));
            foreach ($newLinks as $newLink) {
                $linkUrls[] = $newLink;
            }
        }

        $material->update([
            'title' => $request->title,
            'description' => $request->description,
            'link_url' => count($linkUrls) > 0 ? json_encode($linkUrls) : null,
            'file_path' => count($filePaths) > 0 ? json_encode($filePaths) : null,
            'original_filename' => count($originalFilenames) > 0 ? json_encode($originalFilenames) : null,
        ]);

        // Also update topic title if it exists
        $topic = \App\Models\ClassTopic::where('type', 'materi')->where('content_id', $material->id)->first();
        if ($topic) {
            $topic->update(['title' => $material->title]);
        }

        return back()->with('success', 'Materi berhasil diperbarui.');
    }

    public function downloadMaterial(ClassRoom $class, \App\Models\Material $material, Request $request)
    {
        $user = Auth::user();

        // 1. Authorization Check: Ensure user has right to download this file
        if (!$user->hasRole(['admin', 'kaprodi', 'dosen'])) {
            // For students, verify they are actually enrolled in this class
            $isEnrolled = $class->enrollments()->where('student_id', $user->student->id ?? null)->exists();
            if (!$isEnrolled) {
                abort(403, 'Akses Ditolak: Anda tidak terdaftar di kelas ini.');
            }
        }

        $filePaths = $material->file_paths;
        $originalFilenames = $material->original_filenames;

        if (empty($filePaths)) {
            abort(404, 'File materi tidak ditemukan di server.');
        }

        $fileIndex = $request->query('file_index', 0);

        if (!isset($filePaths[$fileIndex])) {
            abort(404, 'File dengan indeks tersebut tidak ditemukan.');
        }

        $targetPath = $filePaths[$fileIndex];
        $originalName = $originalFilenames[$fileIndex] ?? 'Materi_LMS.pdf';

        // 2. Validate File Existence in secure storage or public storage (from RPS)
        if (!$targetPath) {
            abort(404, 'File materi tidak ditemukan di server.');
        }

        if (\Illuminate\Support\Facades\Storage::exists($targetPath)) {
            $path = \Illuminate\Support\Facades\Storage::path($targetPath);
        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($targetPath)) {
            $path = \Illuminate\Support\Facades\Storage::disk('public')->path($targetPath);
        } else {
            abort(404, 'File materi tidak ditemukan di server.');
        }

        // 3. Return Secure Download/File Response

        // Using response()->file() directly allows viewing in browser for PDFs
        return response()->file($path, [
            'Content-Disposition' => 'inline; filename="' . $originalName . '"'
        ]);
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
            'title' => $request->title,
            'context' => $request->description,
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
            'duration' => $request->duration_minutes,
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

    public function destroyTopic(ClassRoom $class, \App\Models\ClassTopic $topic)
    {
        if ($topic->class_room_id !== $class->id) {
            abort(404);
        }

        // Delete associated content
        $content = $topic->content;
        
        if ($content) {
            if ($topic->type === 'materi') {
                $filePaths = $content->file_paths;
                foreach ($filePaths as $path) {
                    if ($path && \Illuminate\Support\Facades\Storage::exists($path)) {
                        \Illuminate\Support\Facades\Storage::delete($path);
                    }
                }
            }
            // Some contents like assignments and quizzes might have submissions, 
            // but for simplicity we rely on database cascade deletes if they exist, 
            // or just delete the main record.
            $content->delete();
        }

        $topic->delete();

        return back()->with('success', 'Aktivitas berhasil dihapus.');
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

    public function exportGrades(ClassRoom $class)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi'])) {
            $isStaff = $class->users()->where('user_id', $user->id)->exists();
            if (!$isStaff) {
                return back()->with('error', 'Anda tidak memiliki akses ke kelas ini.');
            }
        }

        $enrollments = Enrollment::with('student.user')
                        ->where('class_room_id', $class->id)
                        ->latest()
                        ->get();

        $assignments = Assignment::where('class_room_id', $class->id)->get();
        $topics = $class->topics()->where('type', 'quiz')->get();
        $quizIds = $topics->pluck('content_id');
        $quizzes = Quiz::whereIn('id', $quizIds)->get();

        $quizAttempts = \App\Models\StudentQuizAttempt::whereIn('quiz_id', $quizzes->pluck('id'))
            ->get()
            ->groupBy('user_id');

        $filename = "Rekap_Nilai_{$class->nama_kelas}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=\"{$filename}\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['NIM', 'Nama Mahasiswa', 'Kelas', 'Nilai Tugas (Rata-rata)', 'Nilai UTS', 'Nilai UAS'];

        $callback = function() use($columns, $enrollments, $class, $assignments, $quizzes, $quizAttempts) {
            $file = fopen('php://output', 'w');
            // Write BOM for Excel UTF-8
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ';');

            foreach ($enrollments as $enrollment) {
                $student = $enrollment->student;
                if (!$student) continue;

                $studentId = $student->id;
                $studentUserId = $student->user_id;

                $tugasScores = [];
                $utsScore = '-';
                $uasScore = '-';

                // Process Assignments
                foreach ($assignments as $assign) {
                    $gradeObj = \App\Models\StudentGrade::where('enrollment_id', $enrollment->id)
                                ->where('rps_assessment_id', $assign->rps_assessment_id)
                                ->first();
                    $score = $gradeObj ? $gradeObj->score : 0;
                    
                    $title = strtolower($assign->title);
                    if (strpos($title, 'uts') !== false || strpos($title, 'tengah semester') !== false) {
                        $utsScore = $score;
                    } elseif (strpos($title, 'uas') !== false || strpos($title, 'akhir semester') !== false) {
                        $uasScore = $score;
                    } else {
                        $tugasScores[] = $score;
                    }
                }

                // Process Quizzes
                foreach ($quizzes as $quiz) {
                    $attempt = $studentUserId && isset($quizAttempts[$studentUserId]) 
                        ? $quizAttempts[$studentUserId]->where('quiz_id', $quiz->id)->sortByDesc('score')->first() 
                        : null;
                    $score = $attempt ? $attempt->score : 0;

                    $title = strtolower($quiz->title);
                    if (strpos($title, 'uts') !== false || strpos($title, 'tengah semester') !== false) {
                        $utsScore = $score;
                    } elseif (strpos($title, 'uas') !== false || strpos($title, 'akhir semester') !== false) {
                        $uasScore = $score;
                    } else {
                        $tugasScores[] = $score;
                    }
                }

                $rataTugas = count($tugasScores) > 0 ? round(array_sum($tugasScores) / count($tugasScores), 2) : '-';

                if ($student->is_frozen) {
                    $rataTugas = 'Belum Eligible';
                    $utsScore = 'Belum Eligible';
                    $uasScore = 'Belum Eligible';
                }

                fputcsv($file, [
                    $student->nim,
                    $student->nama_student,
                    $class->nama_kelas,
                    $rataTugas,
                    $utsScore,
                    $uasScore
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
