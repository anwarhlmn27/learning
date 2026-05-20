@extends('layouts.lms')

@section('header_title', 'Classroom Dashboard')

@section('content')
<style>
    /* Styling Premium & Harmonious HSL Palette */
    :root {
        --primary: #4f46e5;
        --primary-light: #eff6ff;
        --secondary: #0ea5e9;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --background-card: #ffffff;
        --border-color: #e2e8f0;
        --text-primary: #1e293b;
        --text-muted: #64748b;
        --radius-lg: 12px;
        --radius-md: 8px;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.02);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .class-header-card {
        background: linear-gradient(135deg, #4f46e5 0%, #31108f 100%);
        color: white;
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
    }
    .class-header-card::after {
        content: '';
        position: absolute;
        right: -50px;
        top: -50px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        pointer-events: none;
    }

    /* Tabs System styling */
    .tabs-bar {
        display: flex;
        border-bottom: 2px solid var(--border-color);
        margin-bottom: 2rem;
        gap: 2rem;
        overflow-x: auto;
    }
    .tab-trigger {
        padding: 0.85rem 0.5rem;
        background: none;
        border: none;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        position: relative;
        transition: var(--transition);
        white-space: nowrap;
    }
    .tab-trigger:hover {
        color: var(--primary);
    }
    .tab-trigger.active {
        color: var(--primary);
    }
    .tab-trigger.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--primary);
        border-radius: 9999px;
    }

    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Accordion / Sessions styling */
    .session-accordion {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .session-item {
        background: var(--background-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        transition: var(--transition);
    }
    .session-item:hover {
        box-shadow: var(--shadow-md);
        border-color: #cbd5e1;
    }
    .session-trigger {
        width: 100%;
        padding: 1.25rem 1.5rem;
        background: #f8fafc;
        border: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        text-align: left;
        transition: var(--transition);
    }
    .session-trigger:hover {
        background: #f1f5f9;
    }
    .session-details {
        display: none;
        padding: 1.5rem;
        border-top: 1px solid var(--border-color);
        background: white;
    }
    .session-details.open {
        display: block;
    }

    /* Activity cards styling */
    .activity-card {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        border: 1px solid #f1f5f9;
        border-radius: var(--radius-md);
        margin-bottom: 0.75rem;
        transition: var(--transition);
        background: #fafafb;
    }
    .activity-card:hover {
        transform: translateX(4px);
        background: #ffffff;
        box-shadow: var(--shadow-sm);
        border-color: var(--border-color);
    }
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .activity-icon.materi { background-color: #eff6ff; color: #3b82f6; }
    .activity-icon.assignment { background-color: #ecfdf5; color: #10b981; }
    .activity-icon.forum { background-color: #f5f3ff; color: #8b5cf6; }
    .activity-icon.quiz { background-color: #fffbeb; color: #f59e0b; }

    /* Interactive Table / Gradebook Matrix */
    .matrix-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }
    .matrix-table th {
        background-color: #f8fafc;
        padding: 1rem;
        font-weight: 600;
        text-align: left;
        border-bottom: 2px solid var(--border-color);
        color: var(--text-primary);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .matrix-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.9rem;
        color: var(--text-primary);
    }
    .matrix-table tr:hover {
        background-color: #f8fafc;
    }

    .badge-score {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-weight: 700;
        font-size: 0.8rem;
    }
    .badge-score.empty { background: #f1f5f9; color: #94a3b8; }
    .badge-score.passed { background: #dcfce7; color: #15803d; }
    .badge-score.pending { background: #fffde7; color: #a16207; }

    /* Floating Modal styling */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(4px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-box {
        background: white;
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 550px;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        overflow: hidden;
        animation: popUp 0.25s ease;
    }
    @keyframes popUp {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>

<!-- Classroom Banner Card -->
<div class="class-header-card">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <span style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; background: rgba(255,255,255,0.2); padding: 0.25rem 0.75rem; border-radius: 9999px;">
                {{ $class->tahun_akademik }} - Semester {{ $class->semester }}
            </span>
            <h1 style="margin: 0.75rem 0 0.5rem 0; font-size: 2.25rem; font-weight: 800; letter-spacing: -0.025em; line-height: 1.2;">{{ $class->nama_kelas }}</h1>
            <p style="margin: 0; font-size: 1.1rem; opacity: 0.9; font-weight: 500;">
                Mata Kuliah: {{ optional($class->subject)->nama_subject }} ({{ optional($class->subject)->code ?? '-' }})
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="{{ route('classes.index') }}" class="btn" style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.25); border-radius: 9999px;">
                ← Back to List
            </a>
            @if($class->is_active)
                <span style="background: #22c55e; color: white; padding: 0.4rem 1rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span style="width: 8px; height: 8px; background: white; border-radius: 50%; display: inline-block;"></span> Active
                </span>
            @else
                <span style="background: #64748b; color: white; padding: 0.4rem 1rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 700;">
                    Inactive
                </span>
            @endif
        </div>
    </div>
</div>

@if(session('error'))
    <div style="background-color: #fef2f2; color: #991b1b; padding: 1rem; border: 1px solid #fecaca; border-radius: var(--radius-md); margin-bottom: 1.5rem;">{{ session('error') }}</div>
@endif

@if(session('success'))
    <div style="background-color: #f0fdf4; color: #166534; padding: 1rem; border: 1px solid #bbf7d0; border-radius: var(--radius-md); margin-bottom: 1.5rem;">{{ session('success') }}</div>
@endif

<!-- Tab Navigation Header -->
<div class="tabs-bar">
    <button class="tab-trigger active" data-tab="tab-classwork">
        📚 Sesi & Classwork
    </button>
    <button class="tab-trigger" data-tab="tab-people">
        👥 Peserta & Staff (People)
    </button>
    <button class="tab-trigger" data-tab="tab-grades">
        📝 Penugasan & Nilai (Grades)
    </button>
    <button class="tab-trigger" data-tab="tab-settings">
        ⚙️ Settings
    </button>
</div>

<!-- ============================================ -->
<!-- TAB 1: SESI & CLASSWORK                     -->
<!-- ============================================ -->
<div id="tab-classwork" class="tab-content active">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">Timeline 14 Sesi Dinamis</h2>
        @if(Auth::user()->hasRole(['admin', 'kaprodi', 'dosen']))
            <div style="display: flex; gap: 0.5rem;">
                <button class="btn btn-outline" onclick="openAddClassworkModal()">
                    <i>➕</i> Add Classwork Item
                </button>
                <form action="{{ route('classes.generate_lms', $class) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Generate sesi otomatis berdasarkan RPS?')">
                    @csrf
                    <button type="submit" class="btn" style="background-color: #f59e0b; color: white;">
                        <i>⚡</i> Import from RPS Syllabus
                    </button>
                </form>
            </div>
        @endif
    </div>

    <div class="session-accordion">
        @foreach($sessions as $number => $sessionData)
            <div class="session-item">
                <button class="session-trigger" onclick="toggleSession('session-{{ $number }}')">
                    <span style="font-weight: 700; font-size: 1.1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.75rem;">
                        <span style="width: 28px; height: 28px; border-radius: 50%; background: #e0e7ff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 800;">
                            {{ $number }}
                        </span>
                        Sesi {{ $number }} : 
                        @if($sessionData['topics']->count() > 0)
                            <span style="font-weight: 500; font-size: 1rem; color: var(--text-primary);">{{ $sessionData['topics']->first()->title }}</span>
                        @else
                            <span style="font-weight: 400; font-size: 1rem; color: var(--text-muted); font-style: italic;">Topik Belum Diisi</span>
                        @endif
                    </span>
                    <span id="icon-session-{{ $number }}" style="font-size: 1.25rem; color: var(--text-muted); transition: var(--transition);">▼</span>
                </button>
                <div id="session-{{ $number }}" class="session-details {{ $loop->first ? 'open' : '' }}">
                    @forelse($sessionData['topics'] as $topic)
                        <div class="activity-card">
                            <div class="activity-icon {{ $topic->type }}">
                                @if($topic->type == 'materi') 📖 @elseif($topic->type == 'assignment') 📝 @elseif($topic->type == 'forum') 💬 @else ⏱️ @endif
                            </div>
                            <div style="flex-grow: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <h4 style="margin: 0 0 0.25rem 0; font-size: 1.05rem; font-weight: 700; color: var(--text-primary);">
                                        {{ $topic->title }}
                                    </h4>
                                    <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; color: var(--text-muted);">
                                        {{ $topic->type }}
                                    </span>
                                </div>
                                
                                @if($topic->type == 'materi' && $topic->material)
                                    <p style="margin: 0 0 0.5rem 0; font-size: 0.9rem; color: var(--text-muted);">{{ $topic->material->description }}</p>
                                    @if($topic->material->link)
                                        <a href="{{ $topic->material->link }}" target="_blank" class="btn btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <i>🔗</i> Buka Tautan Materi
                                        </a>
                                    @endif
                                @elseif($topic->type == 'assignment' && $topic->assignment)
                                    <p style="margin: 0 0 0.5rem 0; font-size: 0.9rem; color: var(--text-muted);">{{ Str::limit($topic->assignment->instruction, 150) }}</p>
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                                        <span style="font-size: 0.8rem; color: #dc2626; font-weight: 600;">
                                            Deadline: {{ date('d M Y - H:i', strtotime($topic->assignment->deadline)) }}
                                        </span>
                                        @if(Auth::user()->hasRole('mahasiswa'))
                                            <a href="{{ route('assignments.show', $topic->assignment) }}" class="btn" style="padding: 0.35rem 0.85rem; font-size: 0.8rem;">
                                                Kumpulkan Tugas
                                            </a>
                                        @else
                                            <a href="{{ route('assignments.show', $topic->assignment) }}" class="btn btn-outline" style="padding: 0.35rem 0.85rem; font-size: 0.8rem;">
                                                Grading & Submissions
                                            </a>
                                        @endif
                                    </div>
                                @elseif($topic->type == 'forum' && $topic->forum)
                                    <p style="margin: 0 0 0.5rem 0; font-size: 0.9rem; color: var(--text-muted);">{{ $topic->forum->description }}</p>
                                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.75rem; margin-top: 0.5rem;">
                                        <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">💬 Ruang Diskusi Kelas Aktif</span>
                                    </div>
                                @elseif($topic->type == 'quiz' && $topic->quiz)
                                    <p style="margin: 0 0 0.5rem 0; font-size: 0.9rem; color: var(--text-muted);">{{ $topic->quiz->description }}</p>
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                                        <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">
                                            Durasi: {{ $topic->quiz->duration_minutes }} Menit | Pilihan Ganda (Auto-Grade)
                                        </span>
                                        @if(Auth::user()->hasRole('mahasiswa'))
                                            @php
                                                $bestAttempt = isset($quizAttempts[Auth::id()]) 
                                                    ? $quizAttempts[Auth::id()]->where('quiz_id', $topic->quiz->id)->sortByDesc('score')->first() 
                                                    : null;
                                            @endphp
                                            @if($bestAttempt)
                                                <span class="badge-score passed" style="font-size: 0.8rem;">Nilai: {{ $bestAttempt->score }}/100 (Selesai)</span>
                                            @else
                                                <a href="{{ route('classes.take_quiz', [$class, $topic->quiz]) }}" class="btn" style="padding: 0.35rem 0.85rem; font-size: 0.8rem; background-color: #f59e0b; color: white;">
                                                    Mulai Kuis
                                                </a>
                                            @endif
                                        @else
                                            <span style="font-size: 0.8rem; background: #fef3c7; color: #d97706; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: 600;">Auto-Grade Enabled</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p style="margin: 0; color: var(--text-muted); font-size: 0.875rem; text-align: center; padding: 1rem 0;">Belum ada aktivitas pada sesi ini.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- ============================================ -->
<!-- TAB 2: PESERTA & STAFF (PEOPLE)              -->
<!-- ============================================ -->
<div id="tab-people" class="tab-content">
    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
        
        <!-- Lecturers (Dosen) Section -->
        <div class="card">
            <div class="card-header" style="background: white; border-bottom: 1px solid var(--border-color); padding: 1.25rem;">
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--text-primary);">Dosen Pengampu / Lecturer</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <ul style="list-style: none; margin: 0; padding: 0;">
                    @forelse($lecturers as $dosenUser)
                        <li style="padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem; border-bottom: 1px solid var(--border-color);">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: #e0e7ff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                {{ strtoupper(substr($dosenUser->name, 0, 1)) }}
                            </div>
                            <div>
                                <strong style="display: block; color: var(--text-primary);">{{ $dosenUser->name }}</strong>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $dosenUser->email }}</span>
                            </div>
                        </li>
                    @empty
                        <li style="padding: 2rem; text-align: center; color: var(--text-muted);">Tidak ada dosen yang terdaftar di kelas ini.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- BAAK Staff Section -->
        <div class="card">
            <div class="card-header" style="background: white; border-bottom: 1px solid var(--border-color); padding: 1.25rem;">
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--text-primary);">BAAK Staff</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <ul style="list-style: none; margin: 0; padding: 0;">
                    @forelse($baakStaff as $baakUser)
                        <li style="padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem; border-bottom: 1px solid var(--border-color);">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; color: var(--text-muted); display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                {{ strtoupper(substr($baakUser->name, 0, 1)) }}
                            </div>
                            <div>
                                <strong style="display: block; color: var(--text-primary);">{{ $baakUser->name }}</strong>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $baakUser->email }}</span>
                            </div>
                        </li>
                    @empty
                        <li style="padding: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.9rem;">Belum ada staff BAAK yang didelegasikan ke kelas ini.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Enrolled Students (Mahasiswa) Section -->
        <div class="card">
            <div class="card-header" style="background: white; border-bottom: 1px solid var(--border-color); padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--text-primary);">
                    Enrolled Students <span style="background: #e0e7ff; color: var(--primary); padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 700;">{{ $enrollments->count() }}</span>
                </h3>
                @if(Auth::user()->hasRole(['admin', 'kaprodi', 'dosen']))
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('classes.template') }}" class="btn btn-outline" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">
                            <i>📥</i> Template
                        </a>
                        <button class="btn btn-outline" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;" onclick="document.getElementById('modal-import').style.display = 'flex'">
                            <i>📄</i> Import CSV
                        </button>
                        <button class="btn" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;" onclick="document.getElementById('modal-add').style.display = 'flex'">
                            <i>➕</i> Enroll Student
                        </button>
                    </div>
                @endif
            </div>
            <div class="card-body" style="padding: 0; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 1rem; border-bottom: 1px solid var(--border-color); background: #f8fafc; text-align: left; width: 50px;">No</th>
                            <th style="padding: 1rem; border-bottom: 1px solid var(--border-color); background: #f8fafc; text-align: left;">NIM</th>
                            <th style="padding: 1rem; border-bottom: 1px solid var(--border-color); background: #f8fafc; text-align: left;">Nama Mahasiswa</th>
                            <th style="padding: 1rem; border-bottom: 1px solid var(--border-color); background: #f8fafc; text-align: left;">Angkatan</th>
                            @if(Auth::user()->hasRole(['admin', 'kaprodi', 'dosen']))
                                <th style="padding: 1rem; border-bottom: 1px solid var(--border-color); background: #f8fafc; text-align: right;">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $index => $enrollment)
                        <tr>
                            <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">{{ $index + 1 }}</td>
                            <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">{{ optional($enrollment->student)->nim ?? '-' }}</td>
                            <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">
                                <strong>{{ optional($enrollment->student)->nama_student ?? 'Deleted Student' }}</strong>
                                @if(isset($enrollment->student->user->email))
                                    <br><span style="font-size: 0.75rem; color: var(--text-muted);">{{ $enrollment->student->user->email }}</span>
                                @endif
                            </td>
                            <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">{{ optional($enrollment->student)->angkatan ?? '-' }}</td>
                            @if(Auth::user()->hasRole(['admin', 'kaprodi', 'dosen']))
                                <td style="padding: 1rem; border-bottom: 1px solid var(--border-color); text-align: right;">
                                    <form action="{{ route('classes.unenroll', [$class, $enrollment]) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin mengeluarkan mahasiswa ini dari kelas?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #dc2626; border-color: #fecaca;">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-muted);">Belum ada mahasiswa yang terdaftar di kelas ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- ============================================ -->
<!-- TAB 3: PENUGASAN & NILAI (GRADES)            -->
<!-- ============================================ -->
<div id="tab-grades" class="tab-content">
    
    @if(Auth::user()->hasRole(['admin', 'kaprodi', 'dosen']))
        <!-- Dosen Gradebook Matrix Dashboard -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-header" style="background: white; border-bottom: 1px solid var(--border-color); padding: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">Gradebook Matrix (Lecturer View)</h3>
                    <p style="margin: 0.25rem 0 0 0; font-size: 0.8rem; color: var(--text-muted);">
                        Nilai tersinkronisasi otomatis dengan <strong>Pusat Nilai / OBE Analytics</strong> di sidebar untuk kalkulasi CLO/PLO.
                    </p>
                </div>
                <span style="font-size: 0.8rem; background: #e0e7ff; color: var(--primary); padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 600;">
                    OBE Sync Enabled
                </span>
            </div>
            <div class="card-body" style="padding: 0; overflow-x: auto;">
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mahasiswa</th>
                            <!-- Assignment Columns -->
                            @foreach($assignments as $assign)
                                <th style="min-width: 120px;">Tugas: {{ Str::limit($assign->title, 15) }}</th>
                            @endforeach
                            <!-- Quiz Columns -->
                            @foreach($quizzes as $quiz)
                                <th style="min-width: 120px;">Kuis: {{ Str::limit($quiz->title, 15) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $idx => $enroll)
                            @php
                                $studentId = $enroll->student_id;
                                $studentUserId = optional($enroll->student)->user_id;
                            @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <strong>{{ optional($enroll->student)->nama_student }}</strong>
                                    <br><span style="font-size: 0.75rem; color: var(--text-muted);">{{ optional($enroll->student)->nim }}</span>
                                </td>
                                <!-- Render Assignment scores -->
                                @foreach($assignments as $assign)
                                    @php
                                        $sub = isset($submissions[$studentId]) 
                                            ? $submissions[$studentId]->where('assignment_id', $assign->id)->first() 
                                            : null;
                                        
                                        // Retrieve synced score from student_grades
                                        $gradeObj = \App\Models\StudentGrade::where('enrollment_id', $enroll->id)
                                            ->where('rps_assessment_id', $assign->rps_assessment_id)
                                            ->first();
                                    @endphp
                                    <td>
                                        @if($gradeObj)
                                            <span class="badge-score passed">{{ $gradeObj->score }}</span>
                                        @elseif($sub)
                                            <a href="{{ route('assignments.show', $assign) }}" class="badge-score pending" style="text-decoration: none; display: inline-block;">
                                                {{ $sub->status }} (Grade)
                                            </a>
                                        @else
                                            <span class="badge-score empty">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                <!-- Render Quiz scores -->
                                @foreach($quizzes as $quiz)
                                    @php
                                        $attempt = $studentUserId && isset($quizAttempts[$studentUserId]) 
                                            ? $quizAttempts[$studentUserId]->where('quiz_id', $quiz->id)->sortByDesc('score')->first() 
                                            : null;
                                    @endphp
                                    <td>
                                        @if($attempt)
                                            <span class="badge-score passed">{{ $attempt->score }}</span>
                                        @else
                                            <span class="badge-score empty">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 2 + $assignments->count() + $quizzes->count() }}" style="padding: 2rem; text-align: center; color: var(--text-muted);">Belum ada data nilai di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <!-- Student Personal Scorecard -->
        <div class="card">
            <div class="card-header" style="background: white; border-bottom: 1px solid var(--border-color); padding: 1.25rem;">
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">Rapor Penugasan & Kuis Saya</h3>
                <p style="margin: 0.25rem 0 0 0; font-size: 0.8rem; color: var(--text-muted);">Semua capaian nilai penugasan dan kuis Anda di kelas ini.</p>
            </div>
            <div class="card-body" style="padding: 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 1rem; border-bottom: 1px solid var(--border-color); background: #f8fafc; text-align: left;">Nama Kegiatan</th>
                            <th style="padding: 1rem; border-bottom: 1px solid var(--border-color); background: #f8fafc; text-align: left;">Tipe</th>
                            <th style="padding: 1rem; border-bottom: 1px solid var(--border-color); background: #f8fafc; text-align: left;">Status Pengumpulan</th>
                            <th style="padding: 1rem; border-bottom: 1px solid var(--border-color); background: #f8fafc; text-align: right;">Capaian Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Student Assignment List -->
                        @foreach($assignments as $assign)
                            @php
                                $studentId = Auth::user()->student->id ?? null;
                                $sub = $studentId && isset($submissions[$studentId]) 
                                    ? $submissions[$studentId]->where('assignment_id', $assign->id)->first() 
                                    : null;
                                
                                $studentEnroll = \App\Models\Enrollment::where('class_room_id', $class->id)
                                    ->where('student_id', $studentId)
                                    ->first();

                                $gradeObj = $studentEnroll 
                                    ? \App\Models\StudentGrade::where('enrollment_id', $studentEnroll->id)
                                        ->where('rps_assessment_id', $assign->rps_assessment_id)
                                        ->first() 
                                    : null;
                            @endphp
                            <tr>
                                <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">
                                    <strong>{{ $assign->title }}</strong>
                                </td>
                                <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">Tugas (Assignment)</td>
                                <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">
                                    @if($sub)
                                        <span style="color: var(--success); font-weight: 600;">{{ $sub->status }}</span>
                                    @else
                                        <span style="color: var(--danger); font-weight: 600;">Missing</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; border-bottom: 1px solid var(--border-color); text-align: right;">
                                    @if($gradeObj)
                                        <strong style="font-size: 1.1rem; color: var(--success);">{{ $gradeObj->score }} / 100</strong>
                                    @else
                                        <span style="color: var(--text-muted); font-style: italic;">Belum Dinilai</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        <!-- Student Quiz List -->
                        @foreach($quizzes as $quiz)
                            @php
                                $bestAttempt = isset($quizAttempts[Auth::id()]) 
                                    ? $quizAttempts[Auth::id()]->where('quiz_id', $quiz->id)->sortByDesc('score')->first() 
                                    : null;
                            @endphp
                            <tr>
                                <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">
                                    <strong>{{ $quiz->title }}</strong>
                                </td>
                                <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">Kuis Pilihan Ganda</td>
                                <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">
                                    @if($bestAttempt)
                                        <span style="color: var(--success); font-weight: 600;">Submitted</span>
                                    @else
                                        <span style="color: var(--danger); font-weight: 600;">Not Attempted</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; border-bottom: 1px solid var(--border-color); text-align: right;">
                                    @if($bestAttempt)
                                        <strong style="font-size: 1.1rem; color: var(--success);">{{ $bestAttempt->score }} / 100</strong>
                                    @else
                                        <span style="color: var(--text-muted); font-style: italic;">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

<!-- ============================================ -->
<!-- TAB 4: SETTINGS                              -->
<!-- ============================================ -->
<div id="tab-settings" class="tab-content">
    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
        
        <!-- Classroom Configuration -->
        <div class="card">
            <div class="card-header" style="background: white; border-bottom: 1px solid var(--border-color); padding: 1.25rem;">
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">Classroom Settings</h3>
            </div>
            <div class="card-body">
                @if(Auth::user()->hasRole(['admin', 'kaprodi']))
                    <form action="{{ route('classes.update', $class) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Nama Kelas <span style="color: red;">*</span></label>
                                <input type="text" name="nama_kelas" value="{{ $class->nama_kelas }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Tahun Akademik <span style="color: red;">*</span></label>
                                <input type="text" name="tahun_akademik" value="{{ $class->tahun_akademik }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Semester <span style="color: red;">*</span></label>
                                <select name="semester" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                                    <option value="Ganjil" {{ $class->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ $class->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                    <option value="Antara" {{ $class->semester == 'Antara' ? 'selected' : '' }}>Antara</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Mata Kuliah <span style="color: red;">*</span></label>
                                <select name="subject_id" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ $class->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->nama_subject }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Dosen Pengampu Utama <span style="color: red;">*</span></label>
                                <select name="dosen_id" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                                    @php
                                        $primaryDosen = $class->dosens()->first();
                                    @endphp
                                    @foreach($dosens as $dosen)
                                        <option value="{{ $dosen->id }}" {{ ($primaryDosen && $primaryDosen->dosen && $primaryDosen->dosen->id == $dosen->id) ? 'selected' : '' }}>{{ $dosen->nama_dosen }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="is_active" value="1" {{ $class->is_active ? 'checked' : '' }} style="width: 18px; height: 18px;">
                                Aktifkan kelas ini untuk pembelajaran mahasiswa (Active Classroom)
                            </label>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 0.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                            <button type="submit" class="btn">Save Configuration</button>
                        </div>
                    </form>
                @else
                    <div style="padding: 1.5rem; background: #f8fafc; border-radius: var(--radius-md); text-align: center;">
                        <p style="margin: 0; color: var(--text-muted);">
                            Hanya <strong>Admin System</strong> atau <strong>Ketua Program Studi (Kaprodi)</strong> yang diizinkan untuk mengubah konfigurasi kelas.
                        </p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

<!-- ============================================ -->
<!-- FLOATING MODALS                              -->
<!-- ============================================ -->

<!-- Modal Add Student -->
<div id="modal-add" class="modal-backdrop">
    <div class="modal-box">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700;">Enroll Student to Class</h3>
            <button onclick="document.getElementById('modal-add').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <div class="card-body" style="padding: 1.5rem;">
            @if(count($availableStudents) > 0)
            <form action="{{ route('classes.enroll', $class) }}" method="POST">
                @csrf
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Pilih Mahasiswa <span style="color: red;">*</span></label>
                    <select name="student_id" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach($availableStudents as $student)
                            <option value="{{ $student->id }}">{{ $student->nim }} - {{ $student->nama_student }}</option>
                        @endforeach
                    </select>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Menampilkan mahasiswa dari prodi terkait yang belum berada di kelas ini.</p>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-add').style.display = 'none'">Cancel</button>
                    <button type="submit" class="btn">Enroll Student</button>
                </div>
            </form>
            @else
            <div style="text-align: center; padding: 1rem 0;">
                <p style="margin: 0; color: var(--text-muted);">Semua mahasiswa dari prodi terkait sudah terdaftar di kelas ini.</p>
                <button type="button" class="btn btn-outline" style="margin-top: 1rem;" onclick="document.getElementById('modal-add').style.display = 'none'">Tutup</button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Import CSV -->
<div id="modal-import" class="modal-backdrop">
    <div class="modal-box">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700;">Import Students via CSV</h3>
            <button onclick="document.getElementById('modal-import').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <div class="card-body" style="padding: 1.5rem;">
            <div style="background: #eff6ff; color: #1e40af; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.85rem; line-height: 1.5;">
                <strong>Format CSV Required:</strong><br>
                Kolom 1: <code>nim</code><br>
                <p style="margin: 0.5rem 0 0 0;"><em>Hanya baris dengan NIM valid yang terdaftar di master data mahasiswa yang akan masuk ke kelas.</em></p>
            </div>
            <form action="{{ route('classes.import_students', $class) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Upload File CSV <span style="color: red;">*</span></label>
                    <input type="file" name="file" accept=".csv" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-import').style.display = 'none'">Cancel</button>
                    <button type="submit" class="btn" style="background-color: #10b981; color: white;">Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add Classwork Item -->
<div id="modal-classwork" class="modal-backdrop">
    <div class="modal-box" style="max-width: 600px;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700;">Add Classwork Activity</h3>
            <button onclick="closeAddClassworkModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <div class="card-body" style="padding: 1.5rem;">
            <!-- Select Type of Classwork -->
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Tipe Aktivitas <span style="color: red;">*</span></label>
                <select id="classwork-type" onchange="toggleFormFields(this.value)" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <option value="materi">📖 Materi Pembelajaran</option>
                    <option value="assignment">📝 Tugas (Assignment)</option>
                    <option value="forum">💬 Forum Diskusi</option>
                    <option value="quiz">⏱️ Kuis Pilihan Ganda (Auto-Grade)</option>
                </select>
            </div>

            <!-- Form Material -->
            <form id="form-materi" action="{{ route('classes.store_material', $class) }}" method="POST" class="classwork-form">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Sesi (1-14) <span style="color: red;">*</span></label>
                        <input type="number" name="session_number" min="1" max="14" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Judul Materi <span style="color: red;">*</span></label>
                        <input type="text" name="title" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    </div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Deskripsi / Ringkasan</label>
                    <textarea name="description" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"></textarea>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Link Tautan Eksternal</label>
                    <input type="url" name="link" placeholder="https://youtube.com/ atau drive link" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-outline" onclick="closeAddClassworkModal()">Cancel</button>
                    <button type="submit" class="btn">Publish Material</button>
                </div>
            </form>

            <!-- Form Assignment -->
            <form id="form-assignment" action="{{ route('classes.store_assignment', $class) }}" method="POST" class="classwork-form" style="display: none;">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Sesi (1-14) <span style="color: red;">*</span></label>
                        <input type="number" name="session_number" min="1" max="14" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Judul Tugas <span style="color: red;">*</span></label>
                        <input type="text" name="title" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    </div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Instruksi Tugas <span style="color: red;">*</span></label>
                    <textarea name="instruction" rows="3" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Batas Pengumpulan <span style="color: red;">*</span></label>
                        <input type="datetime-local" name="deadline" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Sangkutkan Penilaian OBE
                        </label>
                        <select name="rps_assessment_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                            <option value="">-- Pilih Indikator Capaian OBE --</option>
                            @php
                                $prodiId = $class->subject->id_prodi ?? null;
                                $rpsAssessments = [];
                                if ($prodiId) {
                                    $rps = \App\Models\Rps::where('subject_id', $class->subject_id)->latest()->first();
                                    if ($rps) {
                                        $rpsSessions = \App\Models\RpsSession::where('rps_id', $rps->id)->pluck('id');
                                        $rpsAssessments = \App\Models\RpsAssessment::whereIn('rps_session_id', $rpsSessions)->get();
                                    }
                                }
                            @endphp
                            @foreach($rpsAssessments as $assessment)
                                <option value="{{ $assessment->id }}">{{ $assessment->assessment_name }} (Bobot: {{ $assessment->weight }}%)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-outline" onclick="closeAddClassworkModal()">Cancel</button>
                    <button type="submit" class="btn">Publish Assignment</button>
                </div>
            </form>

            <!-- Form Forum -->
            <form id="form-forum" action="{{ route('classes.store_forum', $class) }}" method="POST" class="classwork-form" style="display: none;">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Sesi (1-14) <span style="color: red;">*</span></label>
                        <input type="number" name="session_number" min="1" max="14" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Judul Forum <span style="color: red;">*</span></label>
                        <input type="text" name="title" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    </div>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Topik Bahasan Forum</label>
                    <textarea name="description" rows="3" placeholder="Tuliskan arahan diskusi kelas..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"></textarea>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-outline" onclick="closeAddClassworkModal()">Cancel</button>
                    <button type="submit" class="btn">Create Forum</button>
                </div>
            </form>

            <!-- Form Quiz -->
            <form id="form-quiz" action="{{ route('classes.store_quiz', $class) }}" method="POST" class="classwork-form" style="display: none;">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Sesi (1-14) <span style="color: red;">*</span></label>
                        <input type="number" name="session_number" min="1" max="14" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Judul Kuis <span style="color: red;">*</span></label>
                        <input type="text" name="title" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    </div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Petunjuk Pengerjaan</label>
                    <textarea name="description" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Durasi (Menit) <span style="color: red;">*</span></label>
                        <input type="number" name="duration_minutes" value="15" min="1" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Sangkutkan Penilaian OBE
                        </label>
                        <select name="rps_assessment_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                            <option value="">-- Pilih Indikator Capaian OBE --</option>
                            @foreach($rpsAssessments as $assessment)
                                <option value="{{ $assessment->id }}">{{ $assessment->assessment_name }} (Bobot: {{ $assessment->weight }}%)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-outline" onclick="closeAddClassworkModal()">Cancel</button>
                    <button type="submit" class="btn" style="background-color: #f59e0b; color: white;">Publish Quiz</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    // 1. Tab Switching JavaScript
    document.querySelectorAll('.tab-trigger').forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active states
            document.querySelectorAll('.tab-trigger').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            // Add active states to clicked
            btn.classList.add('active');
            const tabId = btn.dataset.tab;
            document.getElementById(tabId).classList.add('active');
        });
    });

    // 2. Expand/Collapse Sesi Timeline
    function toggleSession(id) {
        const details = document.getElementById(id);
        const icon = document.getElementById('icon-' + id);
        if (details.classList.contains('open')) {
            details.classList.remove('open');
            icon.innerText = '▼';
        } else {
            details.classList.add('open');
            icon.innerText = '▲';
        }
    }

    // 3. Classwork Modal & Form toggling
    function openAddClassworkModal() {
        document.getElementById('modal-classwork').style.display = 'flex';
    }
    
    function closeAddClassworkModal() {
        document.getElementById('modal-classwork').style.display = 'none';
    }

    function toggleFormFields(type) {
        // Hide all forms first
        document.querySelectorAll('.classwork-form').forEach(f => f.style.display = 'none');
        
        // Show specific form
        if (type === 'materi') {
            document.getElementById('form-materi').style.display = 'block';
        } else if (type === 'assignment') {
            document.getElementById('form-assignment').style.display = 'block';
        } else if (type === 'forum') {
            document.getElementById('form-forum').style.display = 'block';
        } else if (type === 'quiz') {
            document.getElementById('form-quiz').style.display = 'block';
        }
    }
</script>
@endsection
