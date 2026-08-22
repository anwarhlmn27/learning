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
use App\Models\QuizQuestion;
use App\Models\AssignmentSubmission;
use App\Models\Material;
use App\Models\Forum;
use App\Models\ClassTopic;
use App\Models\ClassLecturerFeedback;
use App\Models\SessionRating;
use App\Models\StudentQuizAttempt;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ClassRoomController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isStaffOrAdmin = $user->hasRole(['admin', 'kaprodi', 'rektor', 'dekan', 'baak', 'finance', 'kemahasiswaan']);
        $isKaprodiOnly = $user->hasRole('kaprodi') && !$user->hasRole(['admin', 'rektor', 'dekan', 'baak']);
        $kaprodiProdiIds = $isKaprodiOnly ? \App\Models\Prodi::withoutGlobalScopes()->where('kaprodi_id', $user->id)->pluck('id')->toArray() : [];

        // 1. User's own teaching classes / enrolled classes
        $myClassesQuery = ClassRoom::with(['subject.prodi'])->visible()->active()->where(function($q) use ($user) {
            $q->whereHas('users', fn($q2) => $q2->where('user_id', $user->id));
            if ($user->student) {
                $q->orWhereHas('enrollments', fn($q3) => $q3->where('student_id', $user->student->id));
            }
        });
        $myClassesCount = (clone $myClassesQuery)->count();

        // 2. Filter by prodi (Only for staff / kaprodi / admin)
        $selectedProdi = null;
        $prodiId = null;

        if ($isStaffOrAdmin) {
            $prodiId = $request->prodi_id ?? session('selected_prodi_id');

            if ($isKaprodiOnly) {
                if ($prodiId && in_array($prodiId, $kaprodiProdiIds)) {
                    $selectedProdi = \App\Models\Prodi::withoutGlobalScopes()->find($prodiId);
                } else {
                    $selectedProdi = \App\Models\Prodi::withoutGlobalScopes()->where('kaprodi_id', $user->id)->first();
                    $prodiId = $selectedProdi?->id;
                    if ($prodiId) {
                        session(['selected_prodi_id' => $prodiId]);
                    }
                }
            } else {
                if ($prodiId) {
                    $selectedProdi = \App\Models\Prodi::withoutGlobalScopes()->find($prodiId);
                }
            }
        }

        // Determine active tab: 'my_classes' vs 'prodi_classes'
        if (!$isStaffOrAdmin) {
            $activeTab = 'my_classes';
        } else {
            $activeTab = $request->get('tab');
            if (!$activeTab) {
                if ($request->filled('prodi_id')) {
                    $activeTab = 'prodi_classes';
                } elseif ($selectedProdi && $myClassesCount == 0) {
                    $activeTab = 'prodi_classes';
                } elseif ($myClassesCount > 0 && (!$selectedProdi || ClassRoom::visible()->active()->whereHas('subject', fn($q) => $q->where('id_prodi', $selectedProdi->id))->count() == 0)) {
                    $activeTab = 'my_classes';
                } else {
                    $activeTab = $myClassesCount > 0 ? 'my_classes' : 'prodi_classes';
                }
            }
        }

        // Build query based on active tab
        $query = ClassRoom::with(['subject.prodi'])->visible()->active();

        if ($activeTab === 'my_classes' || !$isStaffOrAdmin) {
            $query->where(function($q) use ($user) {
                $q->whereHas('users', fn($q2) => $q2->where('user_id', $user->id));
                if ($user->student) {
                    $q->orWhereHas('enrollments', fn($q3) => $q3->where('student_id', $user->student->id));
                }
            });
        } else {
            if ($selectedProdi) {
                $query->whereHas('subject', fn($q) => $q->where('id_prodi', $selectedProdi->id));
            } else {
                if (!$user->hasRole(['admin', 'rektor', 'dekan', 'baak'])) {
                    $query->where(function($q) use ($user) {
                        $q->whereHas('users', fn($q2) => $q2->where('user_id', $user->id));
                    });
                }
            }
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

        // Calculate count for prodi classes
        $prodiClassesCount = 0;
        if ($selectedProdi) {
            $prodiClassesCount = ClassRoom::visible()->active()->whereHas('subject', fn($q) => $q->where('id_prodi', $selectedProdi->id))->count();
        }

        // Subjects for modal creation
        $subjects = $selectedProdi
            ? Subject::withoutGlobalScopes()->where('id_prodi', $selectedProdi->id)->orderBy('nama_subject')->get()
            : Subject::withoutGlobalScopes()->orderBy('nama_subject')->get();

        $dosens = Dosen::with(['prodi.fakultas'])->orderBy('nama_dosen')->get();

        $classPrimaryDosenMap = [];
        foreach ($classRooms as $cr) {
            $firstDosenUser = $cr->dosens()->first();
            if ($firstDosenUser && $firstDosenUser->dosen) {
                $classPrimaryDosenMap[$cr->id] = $firstDosenUser->dosen->id;
            } else {
                $classPrimaryDosenMap[$cr->id] = '';
            }
        }

        $allFakultas = \App\Models\Fakultas::orderBy('nama_fakultas')->get();
        $allProdis = $isKaprodiOnly
            ? \App\Models\Prodi::withoutGlobalScopes()->where('kaprodi_id', $user->id)->orderBy('nama_prodi')->get()
            : \App\Models\Prodi::withoutGlobalScopes()->orderBy('nama_prodi')->get();

        return view('lms.classes.index', compact(
            'classRooms', 'subjects', 'dosens', 'classPrimaryDosenMap', 
            'selectedProdi', 'allFakultas', 'allProdis', 
            'activeTab', 'myClassesCount', 'prodiClassesCount'
        ));
    }

    public function exportActiveClasses(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi', 'baak', 'rektor', 'dekan'])) {
            abort(403, 'Anda tidak memiliki akses untuk export kelas.');
        }

        $query = ClassRoom::with(['subject', 'dosens.dosen', 'enrollments'])->visible()->active();
        
        // Filter by prodi if passed
        if ($request->filled('prodi_id')) {
            $query->whereHas('subject', function($q) use ($request) {
                $q->where('id_prodi', $request->prodi_id);
            });
        }

        $classes = $query->latest()->get();

        $filename = "Kelas_Aktif_" . date('Y-m-d_H-i-s') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Kode Kelas', 'Nama Kelas', 'Tahun Akademik', 'Semester', 'Nama Pengajar', 'Jumlah Siswa', 'Status'];

        $callback = function() use($classes, $columns) {
            $file = fopen('php://output', 'w');
            // Add BOM so Excel opens UTF-8 properly
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
            
            fputcsv($file, $columns, ';');

            foreach ($classes as $index => $class) {
                $dosenName = '-';
                $firstDosenUser = $class->dosens()->first();
                if ($firstDosenUser) {
                    $dosenName = $firstDosenUser->dosen->nama_dosen ?? $firstDosenUser->name;
                }
                
                $kodeKelas = optional($class->subject)->kode_subject ?? '-';
                $jumlahSiswa = $class->enrollments ? $class->enrollments->count() : 0;

                $row = [
                    $index + 1,
                    $kodeKelas,
                    $class->nama_kelas,
                    $class->tahun_akademik,
                    $class->semester,
                    $dosenName,
                    $jumlahSiswa,
                    'Aktif'
                ];

                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi', 'baak']) && !$user->can('create-classes')) {
            return back()->with('error', 'Anda tidak memiliki akses untuk membuat kelas.');
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'dosen_id' => 'required|exists:dosens,id',
            'nama_kelas' => 'required|string|max:255',
            'tahun_akademik' => 'required|string|max:50',
            'semester' => 'required|in:Ganjil,Genap,Antara',
        ]);

        $activeRps = Rps::where('subject_id', $request->subject_id)
            ->whereIn('status', ['active', 'Active', 'aktif', 'Aktif'])
            ->first();

        if (!$activeRps) {
            $subject = Subject::find($request->subject_id);
            $subjectName = $subject ? $subject->nama_subject : 'Mata kuliah';
            return back()->with('error', 'Gagal membuat kelas! Mata kuliah "' . $subjectName . '" belum memiliki RPS berstatus Aktif. Silakan buat atau aktifkan RPS mata kuliah terlebih dahulu.');
        }

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

    public function edit(ClassRoom $class)
    {
        return redirect()->route('classes.show', $class);
    }

    public function update(Request $request, ClassRoom $class)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi', 'baak']) && !$user->can('edit-classes')) {
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

        $activeRps = Rps::where('subject_id', $request->subject_id)
            ->whereIn('status', ['active', 'Active', 'aktif', 'Aktif'])
            ->first();

        if (!$activeRps) {
            $subject = Subject::find($request->subject_id);
            $subjectName = $subject ? $subject->nama_subject : 'Mata kuliah';
            return back()->with('error', 'Gagal memperbarui kelas! Mata kuliah "' . $subjectName . '" belum memiliki RPS berstatus Aktif.');
        }

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
                $dosenRoles = \App\Models\Role::whereIn('name', ['dosen', 'rektor', 'dekan', 'kaprodi'])->pluck('id')->toArray();
                if (!empty($dosenRoles)) {
                    $dosenUserIds = $class->users()
                        ->whereHas('roles', function($q) use ($dosenRoles) {
                            $q->whereIn('role_id', $dosenRoles);
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

        return redirect()->back()->with('success', 'Data Kelas berhasil diperbarui.');
        // return redirect()->route('classes.index', ['prodi_id' => $request->id_prodi])
        //          ->with('success', 'Data Kelas berhasil diperbarui.');
    }

    /**
     * Soft-delete: only allowed if class is archived OR has no content.
     * Active class with any topics/assignments cannot be deleted.
     */
    public function destroy(ClassRoom $class)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi', 'baak']) && !$user->can('delete-classes')) {
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
     * Toggle status: active <-> archived.
     * Archiving is permitted for Dosen (with feedback), BAAK, Kaprodi, Dekan, Admin.
     * Reactivating (unarchiving) is strictly restricted to BAAK, Kaprodi, Dekan, and Admin.
     */
    public function archive(Request $request, ClassRoom $class)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'rektor', 'dekan', 'kaprodi', 'dosen', 'baak'])) {
            return back()->with('error', 'Anda tidak memiliki akses.');
        }

        if ($class->status === 'active') {
            // Only enrolled users or staff/admins can archive
            if (!$user->hasRole(['admin', 'rektor', 'dekan', 'kaprodi', 'baak'])) {
                $isEnrolled = $class->users()->where('user_id', $user->id)->exists();
                if (!$isEnrolled) {
                    return back()->with('error', 'Anda tidak terdaftar di kelas ini.');
                }
            }

            // Check if feedback is provided in the request
            if ($request->has('rating_lms')) {
                $request->validate([
                    'rating_lms'    => 'required|integer|min:1|max:5',
                    'rating_materi' => 'nullable|integer|min:1|max:5',
                    'kendala'       => 'nullable|string|max:2000',
                    'saran'         => 'nullable|string|max:2000',
                ]);

                \App\Models\ClassLecturerFeedback::updateOrCreate(
                    [
                        'class_room_id' => $class->id,
                        'user_id'       => $user->id,
                    ],
                    [
                        'dosen_id'      => $user->dosen?->id,
                        'rating_lms'    => (int) $request->input('rating_lms', 5),
                        'rating_materi' => (int) $request->input('rating_materi', 5),
                        'kendala'       => $request->input('kendala'),
                        'saran'         => $request->input('saran'),
                    ]
                );
            } else {
                // If feedback was not submitted in this request, verify if it already exists
                if (!$class->hasLecturerFeedback()) {
                    return back()->with('error', 'Kelas belum dapat diarsipkan! Dosen pengampu wajib mengisi Formulir Evaluasi & Umpan Balik terlebih dahulu.');
                }
            }

            $class->update(['status' => 'archived']);
            return back()->with('success', 'Evaluasi dosen tersimpan dan kelas "' . $class->nama_kelas . '" berhasil diarsipkan. Semua konten menjadi read-only.');
        } elseif ($class->status === 'archived') {
            // Reactivation is only allowed for BAAK, Kaprodi, Dekan, Rektor, Admin
            if (!$user->hasRole(['admin', 'rektor', 'dekan', 'kaprodi', 'baak'])) {
                return back()->with('error', 'Dosen tidak dapat mengaktifkan kembali kelas yang telah diarsipkan. Proses aktivasi kembali hanya dapat dilakukan oleh BAAK, Kaprodi, Dekan, atau Admin.');
            }

            $class->update(['status' => 'active']);
            return back()->with('success', 'Kelas "' . $class->nama_kelas . '" berhasil diaktifkan kembali.');
        }

        return back()->with('error', 'Status kelas tidak valid untuk diubah.');
    }

    /**
     * Helper to perform deep copying of topics, materials, assignments, quizzes, and forums.
     */
    private function performDeepCopyTopics(ClassRoom $sourceClass, ClassRoom $targetClass): int
    {
        $copiedCount = 0;
        $sourceTopics = ClassTopic::where('class_room_id', $sourceClass->id)->orderBy('session_number')->get();

        foreach ($sourceTopics as $topic) {
            $newContentId = null;

            if ($topic->type === 'materi') {
                $srcMat = Material::find($topic->content_id);
                if ($srcMat) {
                    $newMat = $srcMat->replicate();
                    $newMat->save();
                    $newContentId = $newMat->id;
                }
            } elseif ($topic->type === 'assignment') {
                $srcAssign = Assignment::find($topic->content_id);
                if ($srcAssign) {
                    $newAssign = $srcAssign->replicate(['submissions_count']);
                    $newAssign->class_room_id = $targetClass->id;
                    $newAssign->status = 'Draft';
                    $newAssign->save();
                    $newContentId = $newAssign->id;
                }
            } elseif ($topic->type === 'quiz') {
                $srcQuiz = Quiz::with('questions')->find($topic->content_id);
                if ($srcQuiz) {
                    $newQuiz = $srcQuiz->replicate();
                    $newQuiz->class_room_id = $targetClass->id;
                    $newQuiz->save();

                    foreach ($srcQuiz->questions as $question) {
                        $newQ = $question->replicate();
                        $newQ->quiz_id = $newQuiz->id;
                        $newQ->save();
                    }
                    $newContentId = $newQuiz->id;
                }
            } elseif ($topic->type === 'forum') {
                $srcForum = Forum::find($topic->content_id);
                if ($srcForum) {
                    $newForum = $srcForum->replicate();
                    $newForum->save();
                    $newContentId = $newForum->id;
                }
            }

            if ($newContentId) {
                ClassTopic::create([
                    'class_room_id'  => $targetClass->id,
                    'session_number' => $topic->session_number,
                    'title'          => $topic->title,
                    'type'           => $topic->type,
                    'content_id'     => $newContentId,
                ]);
                $copiedCount++;
            }
        }

        return $copiedCount;
    }

    /**
     * Clone an existing/archived class into a brand new active class with all material content.
     */
    public function cloneToNew(Request $request, ClassRoom $class)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi', 'baak', 'dosen']) && !$user->can('create-classes')) {
            return back()->with('error', 'Anda tidak memiliki akses untuk membuat kelas.');
        }

        $request->validate([
            'nama_kelas'     => 'required|string|max:255',
            'tahun_akademik' => 'required|string|max:50',
            'semester'       => 'required|in:Ganjil,Genap,Antara',
            'dosen_id'       => 'nullable|exists:dosens,id',
        ]);

        $newClass = null;

        DB::transaction(function () use ($request, $class, &$newClass) {
            $newClass = ClassRoom::create([
                'subject_id'     => $class->subject_id,
                'nama_kelas'     => $request->nama_kelas,
                'tahun_akademik' => $request->tahun_akademik,
                'semester'       => $request->semester,
                'status'         => 'active',
            ]);

            // Assign lecturer
            if ($request->filled('dosen_id')) {
                $dosen = Dosen::find($request->dosen_id);
                if ($dosen && $dosen->user_id) {
                    $newClass->users()->attach($dosen->user_id, ['id' => (string) \Illuminate\Support\Str::uuid()]);
                }
            } else {
                // Keep the primary lecturer from source class if any
                $sourceLecturer = $class->users()->first();
                if ($sourceLecturer) {
                    $newClass->users()->attach($sourceLecturer->id, ['id' => (string) \Illuminate\Support\Str::uuid()]);
                }
            }

            // Copy all content
            $this->performDeepCopyTopics($class, $newClass);
        });

        return redirect()->route('classes.show', $newClass)
            ->with('success', 'Kelas baru "' . $newClass->nama_kelas . '" berhasil dibuat dan seluruh materi perkuliahan berhasil dikloning!');
    }

    /**
     * Copy topics and contents from another class into the current target class.
     */
    public function copyContentFrom(Request $request, ClassRoom $class)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi', 'baak', 'dosen']) && !$user->can('edit-classes')) {
            return back()->with('error', 'Anda tidak memiliki akses.');
        }

        $request->validate([
            'source_class_id' => 'required|exists:class_rooms,id',
        ]);

        if ($request->source_class_id === $class->id) {
            return back()->with('error', 'Kelas sumber tidak boleh sama dengan kelas tujuan.');
        }

        $sourceClass = ClassRoom::findOrFail($request->source_class_id);

        $copiedCount = 0;
        DB::transaction(function () use ($sourceClass, $class, &$copiedCount) {
            $copiedCount = $this->performDeepCopyTopics($sourceClass, $class);
        });

        return redirect()->route('classes.show', $class)
            ->with('success', "Berhasil menyalin {$copiedCount} item materi, tugas, dan kuis dari kelas \"{$sourceClass->nama_kelas}\" ke kelas ini!");
    }

    /**
     * Archived classes page.
     */
    public function archivedIndex(Request $request)
    {
        $user = Auth::user();
        $isStaffOrAdmin = $user->hasRole(['admin', 'kaprodi', 'rektor', 'dekan', 'baak', 'finance', 'kemahasiswaan']);
        $isKaprodiOnly = $user->hasRole('kaprodi') && !$user->hasRole(['admin', 'rektor', 'dekan', 'baak']);
        $kaprodiProdiIds = $isKaprodiOnly ? \App\Models\Prodi::withoutGlobalScopes()->where('kaprodi_id', $user->id)->pluck('id')->toArray() : [];

        // 1. User's own archived classes
        $myClassesQuery = ClassRoom::with(['subject.prodi'])->where('status', 'archived')->where(function($q) use ($user) {
            $q->whereHas('users', fn($q2) => $q2->where('user_id', $user->id));
            if ($user->student) {
                $q->orWhereHas('enrollments', fn($q3) => $q3->where('student_id', $user->student->id));
            }
        });
        $myClassesCount = (clone $myClassesQuery)->count();

        // 2. Filter by prodi (Only for staff / kaprodi / admin)
        $selectedProdi = null;
        $prodiId = null;

        if ($isStaffOrAdmin) {
            $prodiId = $request->prodi_id ?? session('selected_prodi_id');

            if ($isKaprodiOnly) {
                if ($prodiId && in_array($prodiId, $kaprodiProdiIds)) {
                    $selectedProdi = \App\Models\Prodi::withoutGlobalScopes()->find($prodiId);
                } else {
                    $selectedProdi = \App\Models\Prodi::withoutGlobalScopes()->where('kaprodi_id', $user->id)->first();
                    $prodiId = $selectedProdi?->id;
                    if ($prodiId) {
                        session(['selected_prodi_id' => $prodiId]);
                    }
                }
            } else {
                if ($prodiId) {
                    $selectedProdi = \App\Models\Prodi::withoutGlobalScopes()->find($prodiId);
                }
            }
        }

        if (!$isStaffOrAdmin) {
            $activeTab = 'my_classes';
        } else {
            $activeTab = $request->get('tab');
            if (!$activeTab) {
                $activeTab = $request->filled('prodi_id') ? 'prodi_classes' : ($myClassesCount > 0 ? 'my_classes' : 'prodi_classes');
            }
        }

        $query = ClassRoom::with(['subject.prodi'])->where('status', 'archived');

        if ($activeTab === 'my_classes' || !$isStaffOrAdmin) {
            $query->where(function($q) use ($user) {
                $q->whereHas('users', fn($q2) => $q2->where('user_id', $user->id));
                if ($user->student) {
                    $q->orWhereHas('enrollments', fn($q3) => $q3->where('student_id', $user->student->id));
                }
            });
        } else {
            if ($selectedProdi) {
                $query->whereHas('subject', fn($q) => $q->where('id_prodi', $selectedProdi->id));
            } else {
                if (!$user->hasRole(['admin', 'rektor', 'dekan', 'baak'])) {
                    $query->where(function($q) use ($user) {
                        $q->whereHas('users', fn($q2) => $q2->where('user_id', $user->id));
                    });
                }
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhereHas('subject', fn($q2) => $q2->where('nama_subject', 'like', "%{$search}%"));
            });
        }

        $classRooms = $query->latest()->paginate(10)->withQueryString();
        $prodiClassesCount = $selectedProdi ? ClassRoom::where('status', 'archived')->whereHas('subject', fn($q) => $q->where('id_prodi', $selectedProdi->id))->count() : 0;
        $dosens = Dosen::with(['prodi.fakultas'])->orderBy('nama_dosen')->get();

        return view('lms.classes.archived', compact('classRooms', 'selectedProdi', 'activeTab', 'myClassesCount', 'prodiClassesCount', 'dosens'));
    }

    public function show(Request $request, ClassRoom $class)
    {
        $user = Auth::user();
        
        // Authorization check: non-admin / non-rektor / non-dekan
        if (!$user->hasRole(['admin', 'rektor', 'dekan', 'baak'])) {
            $isTeachingStaff = $class->users()->where('user_id', $user->id)->exists();
            $isStudent = $user->student && $class->enrollments()->where('student_id', $user->student->id)->exists();
            
            $isKaprodiOfThisClass = false;
            if ($user->hasRole('kaprodi')) {
                $kaprodiProdiIds = \App\Models\Prodi::withoutGlobalScopes()->where('kaprodi_id', $user->id)->pluck('id')->toArray();
                $isKaprodiOfThisClass = in_array($class->subject->id_prodi ?? null, $kaprodiProdiIds);
            }

            if (!$isTeachingStaff && !$isStudent && !$isKaprodiOfThisClass) {
                return back()->with('error', 'Anda tidak memiliki akses ke kelas ini.');
            }
        }

        $class->load(['subject', 'subject.prodi', 'lecturerFeedback.user', 'lecturerFeedback.dosen']);
        
        // 1. People / Peserta Tab data
        $enrollments = Enrollment::with('student.user')
                        ->where('class_room_id', $class->id)
                        ->latest()
                        ->get();
        
        $lecturers = $class->dosens()->get();
        $baakStaff = $class->baaks()->get();

        $selectedEnrollProdiId = $request->input('enroll_prodi_id', $class->subject->id_prodi ?? null);
        $selectedEnrollFakultasId = $request->input('enroll_fakultas_id', $class->subject->prodi->id_fakultas ?? null);
        
        $availableStudents = [];
        if ($selectedEnrollProdiId) {
            $enrolledStudentIds = $enrollments->pluck('student_id')->toArray();
            $availableStudents = Student::where('prodi_id', $selectedEnrollProdiId)
                                    ->whereNotIn('id', $enrolledStudentIds)
                                    ->orderBy('nama_student')
                                    ->get();
        }
        $allFakultas = \App\Models\Fakultas::orderBy('nama_fakultas')->get();
        $allProdis = \App\Models\Prodi::orderBy('nama_prodi')->get();
        $allAngkatans = \App\Models\Student::select('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan')->filter()->values();

        // 2. AUTO-SYNC: Sinkronisasi sesi & modul dari RPS secara otomatis
        // Hanya berjalan jika kelas memiliki RPS dan tidak memperlambat halaman
        // karena menggunakan firstOrCreate (tidak duplikat).
        $this->autoSyncRpsToClass($class);

        // 2. Classwork / Sesi Tab data (timeline of 14 sessions)
        // Reload topics SETELAH auto-sync agar modul RPS langsung tampil
        $topics = $class->topics()->with(['material', 'assignment', 'forum.posts', 'quiz'])->get();
        
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
        $availableDosens = Dosen::with(['prodi.fakultas'])->whereHas('user', function($q) use ($enrolledDosenUserIds) {
            $q->whereNotIn('id', $enrolledDosenUserIds);
        })->orderBy('nama_dosen')->get();

        // Available BAAK staff to add (not already in this class)
        $availableBaaks = User::whereHas('roles', function($q) {
            $q->where('name', 'baak');
        })->whereNotIn('id', $enrolledBaakUserIds)->orderBy('name')->get();

        // Fetch current student's ratings for this class
        $myRatings = [];
        if ($user->student) {
            $myRatings = \App\Models\SessionRating::where('class_room_id', $class->id)
                ->where('student_id', $user->student->id)
                ->get()
                ->groupBy('session_number');
        }

        // Fetch ratings for dosen or admins
        $allRatings = [];
        if ($user->hasRole(['admin', 'kaprodi', 'baak']) || $user->dosen) {
            $queryRatings = \App\Models\SessionRating::with(['student', 'dosen'])
                ->where('class_room_id', $class->id);

            // Dosen should only see their own ratings
            if (!$user->hasRole(['admin', 'kaprodi']) && $user->dosen) {
                $queryRatings->where('dosen_id', $user->dosen->id);
            }

            $allRatings = $queryRatings->get()->groupBy('session_number');
        }

        // 4. Leaderboard Calculation with Multi-Column Tie-Breaker
        $leaderboard = $enrollments->map(function ($enrollment) use ($submissions, $quizAttempts) {
            $student = $enrollment->student;
            $user = $student->user ?? null;
            $userId = $user->id ?? null;
            $studentId = $student->id;

            // Assignment score & submit timestamp
            $studentSubmissions = $submissions->get($studentId, collect());
            $assignmentScoreSum = (float) $studentSubmissions->sum('score');
            $assignmentCount = $studentSubmissions->whereNotNull('score')->count();
            $lastAssignmentSubmittedAt = $studentSubmissions->max('submitted_at') ?? $studentSubmissions->max('created_at');

            // Quiz score, duration, & submit timestamp
            $userAttempts = $userId ? $quizAttempts->get($userId, collect()) : collect();
            $quizScoreSum = (float) $userAttempts->sum('score');
            $quizCount = $userAttempts->whereNotNull('score')->count();
            $totalQuizDurationInSeconds = (int) $userAttempts->sum('duration_in_seconds');
            $lastQuizSubmittedAt = $userAttempts->max('submitted_at') ?? $userAttempts->max('created_at');

            $totalScore = $assignmentScoreSum + $quizScoreSum;

            $lastSubmitOverall = null;
            if ($lastAssignmentSubmittedAt && $lastQuizSubmittedAt) {
                $lastSubmitOverall = $lastAssignmentSubmittedAt > $lastQuizSubmittedAt ? $lastAssignmentSubmittedAt : $lastQuizSubmittedAt;
            } else {
                $lastSubmitOverall = $lastAssignmentSubmittedAt ?? $lastQuizSubmittedAt;
            }

            return [
                'student_id' => $studentId,
                'user_id' => $userId,
                'name' => $student->nama_student ?? ($user->name ?? 'Mahasiswa'),
                'nim' => $student->nim ?? '-',
                'avatar' => $user->avatar ?? null,
                'assignment_score' => $assignmentScoreSum,
                'quiz_score' => $quizScoreSum,
                'total_score' => $totalScore,
                'total_quiz_duration' => $totalQuizDurationInSeconds,
                'last_submitted_at' => $lastSubmitOverall,
                'completed_tasks_count' => $assignmentCount + $quizCount,
            ];
        })
        ->sort(function ($a, $b) {
            // 1. Primary Sort: Total Score DESC
            if ($a['total_score'] != $b['total_score']) {
                return $b['total_score'] <=> $a['total_score'];
            }
            // 2. Secondary Sort (Tie-Breaker 1): Quiz Duration ASC
            if ($a['total_quiz_duration'] != $b['total_quiz_duration']) {
                return $a['total_quiz_duration'] <=> $b['total_quiz_duration'];
            }
            // 3. Tertiary Sort (Tie-Breaker 2): Last Submit Timestamp ASC
            if ($a['last_submitted_at'] != $b['last_submitted_at']) {
                if (!$a['last_submitted_at']) return 1;
                if (!$b['last_submitted_at']) return -1;
                return strcmp((string)$a['last_submitted_at'], (string)$b['last_submitted_at']);
            }
            return 0;
        })
        ->values();

        // Build per-session assessment map for JS-driven filtering in the classwork form
        $rpsSessionsWithAssessments = collect();
        $rpsForClass = Rps::where('subject_id', $class->subject_id)
                ->whereIn('status', ['active', 'Active', 'aktif', 'Aktif'])
                ->latest()->first()
            ?? Rps::where('subject_id', $class->subject_id)->latest()->first();
        if ($rpsForClass) {
            $rpsSessionsWithAssessments = RpsSession::where('rps_id', $rpsForClass->id)
                ->with(['assessments.clo'])
                ->orderBy('session_number')
                ->get()
                ->map(function ($s) {
                    return [
                        'session_number' => $s->session_number,
                        'topic_name'     => $s->topic_name,
                        'assessments'    => $s->assessments->map(function ($a) {
                            // Label: field "Tugas" (assessment_type) is the main visible name
                            $mainLabel = $a->assessment_type
                                ? $a->assessment_type
                                : ($a->assignment_activities
                                    ? \Illuminate\Support\Str::limit($a->assignment_activities, 60)
                                    : ('Assessment #' . $a->id));
                            return [
                                'id'              => $a->id,
                                'label'           => $mainLabel . ' (Bobot: ' . $a->weight . '%)',
                                'assessment_type' => $a->assessment_type ?? '',
                                'instruction'     => $a->assignment_activities ?? '',
                            ];
                        })->values(),
                    ];
                });
        }

        // Available source classes with the same subject that have topics for cloning/copying
        $availableSourceClasses = ClassRoom::where('id', '!=', $class->id)
            ->where('subject_id', $class->subject_id)
            ->whereHas('topics')
            ->latest()
            ->get(['id', 'nama_kelas', 'tahun_akademik', 'semester', 'status']);

        return view('lms.classes.show', compact(
            'class', 
            'topics',
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
            'availableBaaks',
            'myRatings',
            'allRatings',
            'selectedEnrollProdiId',
            'selectedEnrollFakultasId',
            'allFakultas',
            'allProdis',
            'allAngkatans',
            'leaderboard',
            'rpsSessionsWithAssessments',
            'availableSourceClasses'
        ));
    }

    /**
     * API Endpoint to retrieve available students based on filters (Faculty, Prodi, Angkatan).
     */
    public function getAvailableStudents(Request $request, ClassRoom $class)
    {
        $query = Student::query();

        // Exclude students who are already enrolled in this class
        $enrolledStudentIds = Enrollment::where('class_room_id', $class->id)->pluck('student_id')->toArray();
        $query->whereNotIn('id', $enrolledStudentIds);

        // Filter by Fakultas (through prodi)
        if ($request->filled('fakultas_id')) {
            $query->whereHas('prodi', function($q) use ($request) {
                $q->where('id_fakultas', $request->fakultas_id);
            });
        }

        // Filter by Prodi
        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        // Filter by Angkatan
        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->angkatan);
        }

        $students = $query->orderBy('nama_student')->get(['id', 'nim', 'nama_student', 'angkatan']);

        return response()->json($students);
    }

    public function addStaff(Request $request, ClassRoom $class)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi', 'baak']) && !$user->can('edit-classes') && !$user->can('enroll-students')) {
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
        if (!$authUser->hasRole(['admin', 'kaprodi', 'baak']) && !$authUser->can('edit-classes') && !$authUser->can('enroll-students')) {
            return back()->with('error', 'Anda tidak memiliki akses.');
        }

        $class->users()->detach($user->id);

        return back()->with('success', 'Staff berhasil dihapus dari kelas.');
    }

    /**
     * Auto-sync RPS sessions & materials ke LMS class.
     * Dipanggil otomatis saat show() — menggunakan firstOrCreate agar tidak duplikat.
     * Menggantikan tombol "Import from RPS Syllabus" yang sudah dihapus dari UI.
     */
    private function autoSyncRpsToClass(ClassRoom $class): void
    {
        try {
            // Cari RPS aktif untuk subject kelas ini
            $rps = Rps::where('subject_id', $class->subject_id)
                ->whereIn('status', ['active', 'Active', 'aktif', 'Aktif'])
                ->latest()->first()
                ?? Rps::where('subject_id', $class->subject_id)->latest()->first();

            if (!$rps || strtolower($rps->status) === 'draft') {
                return; // Tidak ada RPS atau masih draft, skip
            }

            $rpsSessions = RpsSession::where('rps_id', $rps->id)
                ->with('resources')
                ->orderBy('session_number')
                ->get();

            foreach ($rpsSessions as $session) {
                // 1. Buat ClassSession record (untuk tracking absensi/status sesi)
                ClassSession::firstOrCreate(
                    [
                        'class_room_id'  => $class->id,
                        'rps_session_id' => $session->id,
                    ],
                    [
                        'title'  => $session->topic_name,
                        'status' => 'Draft',
                    ]
                );

                // 2. Sync modul/resource dari RPS ke ClassTopic + Material
                foreach ($session->resources as $resource) {
                    // Skip jika resource tidak punya file path
                    if (empty($resource->file_path)) {
                        continue;
                    }

                    // Cek apakah Material ini sudah ada (by rps_resource_id)
                    $material = \App\Models\Material::where('rps_resource_id', $resource->id)->first();

                    if (!$material) {
                        // Buat Material baru dari RPS resource
                        $material = \App\Models\Material::create([
                            'rps_resource_id'   => $resource->id,
                            'title'             => $resource->nm_resource ?? 'Modul Sesi ' . $session->session_number,
                            'file_path'         => json_encode([$resource->file_path]),
                            'original_filename' => json_encode([$resource->nm_resource ?? basename($resource->file_path)]),
                        ]);

                        // Buat ClassTopic jika belum ada untuk material ini
                        \App\Models\ClassTopic::firstOrCreate(
                            [
                                'class_room_id' => $class->id,
                                'session_number'=> $session->session_number,
                                'type'          => 'materi',
                                'content_id'    => $material->id,
                            ],
                            [
                                'title' => $material->title,
                            ]
                        );
                    }
                }
            }
        } catch (\Exception $e) {
            // Gagal sync tidak boleh crash halaman — hanya log warning
            \Illuminate\Support\Facades\Log::warning('[AutoSync RPS] Gagal sync untuk kelas ' . $class->id . ': ' . $e->getMessage());
        }
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
                // Only create ClassSession — do NOT auto-create assignments.
                // Dosen will manually create classwork and choose which RPS assessment it refers to.
                ClassSession::firstOrCreate(
                    [
                        'class_room_id' => $class->id,
                        'rps_session_id' => $session->id,
                    ],
                    [
                        'title'  => $session->topic_name,
                        'status' => 'Draft',
                    ]
                );

                // Sync materials (SessionResource → Material) — still runs as before
                $resources = \App\Models\SessionResource::where('rps_session_id', $session->id)->get();

                $existingMaterialTopics = \App\Models\ClassTopic::where('class_room_id', $class->id)
                    ->where('session_number', $session->session_number)
                    ->where('type', 'materi')
                    ->orderBy('created_at')
                    ->get();

                foreach ($resources as $index => $res) {
                    $material = \App\Models\Material::where('rps_resource_id', $res->id)->first();

                    if (!$material && isset($existingMaterialTopics[$index])) {
                        $material = \App\Models\Material::find($existingMaterialTopics[$index]->content_id);
                        if ($material) {
                            $material->update([
                                'rps_resource_id'   => $res->id,
                                'title'             => $res->nm_resource,
                                'file_path'         => json_encode([$res->file_path]),
                                'original_filename' => json_encode([$res->nm_resource]),
                            ]);
                            $existingMaterialTopics[$index]->update(['title' => $material->title]);
                        } else {
                            $material = \App\Models\Material::create([
                                'rps_resource_id'   => $res->id,
                                'title'             => $res->nm_resource,
                                'file_path'         => json_encode([$res->file_path]),
                                'original_filename' => json_encode([$res->nm_resource]),
                            ]);
                            $existingMaterialTopics[$index]->update([
                                'content_id' => $material->id,
                                'title'      => $material->title,
                            ]);
                        }
                    }

                    if (!$material) {
                        $material = \App\Models\Material::create([
                            'rps_resource_id'   => $res->id,
                            'title'             => $res->nm_resource,
                            'file_path'         => json_encode([$res->file_path]),
                            'original_filename' => json_encode([$res->nm_resource]),
                        ]);

                        \App\Models\ClassTopic::firstOrCreate(
                            [
                                'class_room_id'  => $class->id,
                                'session_number' => $session->session_number,
                                'type'           => 'materi',
                                'content_id'     => $material->id,
                            ],
                            [
                                'title' => $material->title,
                            ]
                        );
                    }
                }
            }

            DB::commit();
            return back()->with('success', 'Sesi dan Materi LMS berhasil disinkronkan dari RPS. Silakan buat Classwork secara manual pada setiap sesi.');
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
        set_time_limit(300);
        ini_set('memory_limit', '512M');

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

        $studentMap = Student::pluck('id', 'nim')->toArray();
        $existingEnrollments = Enrollment::where('class_room_id', $class->id)->pluck('student_id')->flip()->toArray();

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

                    if (!isset($studentMap[$nim])) {
                        $errorRows[] = "Baris $rowCount: Mahasiswa dengan NIM $nim tidak ditemukan.";
                        $skippedCount++;
                        continue;
                    }

                    $studentId = $studentMap[$nim];
                    $exists = isset($existingEnrollments[$studentId]);

                    if (!$exists) {
                        Enrollment::create([
                            'class_room_id' => $class->id,
                            'student_id' => $studentId,
                        ]);
                        $existingEnrollments[$studentId] = true;
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
        if (!$user->hasRole(['admin', 'kaprodi', 'dosen', 'rektor', 'dekan'])) {
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
            'session_number'    => 'required|integer|min:1|max:14',
            'title'             => 'required|string|max:255',
            'instruction'       => 'required|string',
            'deadline'          => 'required|date',
            'rps_assessment_id' => 'required|exists:rps_assessments,id',
        ], [
            'rps_assessment_id.required' => 'Penilaian OBE (RPS) wajib dipilih agar CLO dapat terlacak.',
            'rps_assessment_id.exists'   => 'Penilaian OBE yang dipilih tidak valid.',
        ]);

        // Resolve the ClassSession for the chosen session number (if one exists from RPS sync)
        $classSession = ClassSession::where('class_room_id', $class->id)
            ->whereHas('rpsSession', fn ($q) => $q->where('session_number', $request->session_number))
            ->first();

        $assignment = Assignment::create([
            'class_room_id'     => $class->id,
            'class_session_id'  => $classSession?->id,
            'title'             => $request->title,
            'instruction'       => $request->instruction,
            'deadline'          => $request->deadline,
            'rps_assessment_id' => $request->rps_assessment_id,
            'status'            => 'Published',
        ]);

        \App\Models\ClassTopic::create([
            'class_room_id'  => $class->id,
            'session_number' => $request->session_number,
            'type'           => 'assignment',
            'content_id'     => $assignment->id,
            'title'          => $assignment->title,
        ]);

        return back()->with('success', 'Tugas berhasil ditambahkan pada Sesi ' . $request->session_number);
    }

    public function updateAssignment(Request $request, ClassRoom $class, Assignment $assignment)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi', 'dosen', 'rektor', 'dekan'])) {
            return back()->with('error', 'Akses ditolak.');
        }

        // Ensure assignment belongs to this class
        if ($assignment->class_room_id !== $class->id) {
            abort(403);
        }

        $request->validate([
            'session_number'    => 'required|integer|min:1|max:14',
            'title'             => 'required|string|max:255',
            'instruction'       => 'required|string',
            'deadline'          => 'required|date',
            'rps_assessment_id' => 'required|exists:rps_assessments,id',
        ], [
            'rps_assessment_id.required' => 'Penilaian OBE (RPS) wajib dipilih agar CLO dapat terlacak.',
            'rps_assessment_id.exists'   => 'Penilaian OBE yang dipilih tidak valid.',
        ]);

        // Resolve the ClassSession for the chosen session number
        $classSession = ClassSession::where('class_room_id', $class->id)
            ->whereHas('rpsSession', fn ($q) => $q->where('session_number', $request->session_number))
            ->first();

        $assignment->update([
            'class_session_id'  => $classSession?->id,
            'title'             => $request->title,
            'instruction'       => $request->instruction,
            'deadline'          => $request->deadline,
            'rps_assessment_id' => $request->rps_assessment_id,
        ]);

        // Keep ClassTopic title and session_number in sync
        \App\Models\ClassTopic::where('content_id', $assignment->id)
            ->where('type', 'assignment')
            ->update([
                'session_number' => $request->session_number,
                'title'          => $request->title,
            ]);

        return back()->with('success', 'Tugas berhasil diperbarui.');
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

    public function updateForum(Request $request, ClassRoom $class, \App\Models\Forum $forum)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'kaprodi', 'dosen', 'rektor', 'dekan'])) {
            return back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $forum->update([
            'title'   => $request->title,
            'context' => $request->description,
        ]);

        // Keep ClassTopic title in sync
        \App\Models\ClassTopic::where('content_id', $forum->id)
            ->where('type', 'forum')
            ->update(['title' => $request->title]);

        return back()->with('success', 'Forum diskusi berhasil diperbarui.');
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

        // Prevent deletion if the topic is a material synced from RPS
        if ($topic->type === 'materi' && $topic->content && !empty($topic->content->rps_resource_id)) {
            return back()->with('error', 'Modul ini disinkronkan otomatis dari RPS dan tidak dapat dihapus dari kelas. Silahkan hapus modul dari Master RPS jika tidak diperlukan.');
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
    public function getAchievements(ClassRoom $class)
    {
        $enrollments = Enrollment::with('student.user')->where('class_room_id', $class->id)->get();
        
        $assignments = Assignment::with('rpsAssessment.clo')->where('class_room_id', $class->id)->get();
        $quizIds = ClassTopic::where('class_room_id', $class->id)->where('type', 'quiz')->pluck('content_id');
        $quizzes = Quiz::with('rpsAssessment.clo')->whereIn('id', $quizIds)->get();

        $submissions = AssignmentSubmission::whereIn('assignment_id', $assignments->pluck('id'))->get()->groupBy('student_id');
        $quizAttempts = \App\Models\StudentQuizAttempt::whereIn('quiz_id', $quizzes->pluck('id'))->get()->groupBy('user_id');

        $gradesCount = ['A' => 0, 'A-' => 0, 'B+' => 0, 'B' => 0, 'B-' => 0, 'C+' => 0, 'C' => 0, 'D' => 0];
        $passCount = 0;
        $failCount = 0;
        
        $cloStats = []; 
        $subjectClos = \App\Models\Clo::where('subject_id', $class->subject_id)->orderBy('kode_clo')->get();
        foreach ($subjectClos as $clo) {
            $cloStats[$clo->kode_clo] = ['total_score_weight' => 0, 'total_weight' => 0];
        }

        foreach ($enrollments as $enrollment) {
            $student = $enrollment->student;
            $userId = $student->user->id ?? null;
            $studentFinalScore = 0;

            $studentSubs = $submissions->get($student->id, collect());
            $studentQuizzes = $userId ? $quizAttempts->get($userId, collect()) : collect();

            foreach ($assignments as $assignment) {
                $sub = $studentSubs->where('assignment_id', $assignment->id)->first();
                $score = $sub ? (float)$sub->score : 0;
                $weight = $assignment->rpsAssessment ? (float)$assignment->rpsAssessment->weight : 0;
                
                $studentFinalScore += ($score * ($weight / 100));

                if ($assignment->rpsAssessment && $assignment->rpsAssessment->clo) {
                    $cloCode = $assignment->rpsAssessment->clo->kode_clo;
                    if (!isset($cloStats[$cloCode])) {
                        $cloStats[$cloCode] = ['total_score_weight' => 0, 'total_weight' => 0];
                    }
                    $cloStats[$cloCode]['total_score_weight'] += ($score * $weight);
                    $cloStats[$cloCode]['total_weight'] += $weight;
                }
            }

            foreach ($quizzes as $quiz) {
                $attempt = $studentQuizzes->where('quiz_id', $quiz->id)->sortByDesc('score')->first();
                $score = $attempt ? (float)$attempt->score : 0;
                $weight = $quiz->rpsAssessment ? (float)$quiz->rpsAssessment->weight : 0;

                $studentFinalScore += ($score * ($weight / 100));

                if ($quiz->rpsAssessment && $quiz->rpsAssessment->clo) {
                    $cloCode = $quiz->rpsAssessment->clo->kode_clo;
                    if (!isset($cloStats[$cloCode])) {
                        $cloStats[$cloCode] = ['total_score_weight' => 0, 'total_weight' => 0];
                    }
                    $cloStats[$cloCode]['total_score_weight'] += ($score * $weight);
                    $cloStats[$cloCode]['total_weight'] += $weight;
                }
            }

            if ($studentFinalScore >= 90) { $gradesCount['A']++; $passCount++; }
            elseif ($studentFinalScore >= 85) { $gradesCount['A-']++; $passCount++; }
            elseif ($studentFinalScore >= 80) { $gradesCount['B+']++; $passCount++; }
            elseif ($studentFinalScore >= 70) { $gradesCount['B']++; $passCount++; }
            elseif ($studentFinalScore >= 65) { $gradesCount['B-']++; $passCount++; }
            elseif ($studentFinalScore >= 60) { $gradesCount['C+']++; $passCount++; }
            elseif ($studentFinalScore >= 50) { $gradesCount['C']++; $passCount++; }
            else { $gradesCount['D']++; $failCount++; }
        }

        $cloLabels = [];
        $cloData = [];
        
        foreach ($cloStats as $cloCode => $stats) {
            $cloLabels[] = $cloCode;
            if ($stats['total_weight'] > 0) {
                $cloData[] = round($stats['total_score_weight'] / $stats['total_weight'], 2);
            } else {
                $cloData[] = 0;
            }
        }

        return response()->json([
            'grades' => [
                'labels' => ['A (≥90)', 'A- (85-89)', 'B+ (80-84)', 'B (70-79)', 'B- (65-69)', 'C+ (60-64)', 'C (50-59)', 'D (<50)'],
                'data' => [
                    $gradesCount['A'], $gradesCount['A-'], $gradesCount['B+'], 
                    $gradesCount['B'], $gradesCount['B-'], $gradesCount['C+'], 
                    $gradesCount['C'], $gradesCount['D']
                ]
            ],
            'pass_fail' => [
                'labels' => ['Pass (A-C)', 'Fail (D)'],
                'data' => [$passCount, $failCount]
            ],
            'clos' => [
                'labels' => $cloLabels,
                'data' => $cloData
            ]
        ]);
    }
}
