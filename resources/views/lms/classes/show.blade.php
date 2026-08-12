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

    /* Star Rating Styling */
    .rating-container {
        margin-top: 1.5rem;
        padding: 1.25rem;
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
    }
    .rating-stars-wrapper {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 0.25rem;
        margin: 0.5rem 0;
    }
    .rating-stars-wrapper input {
        display: none;
    }
    .rating-stars-wrapper label {
        font-size: 1.75rem;
        color: #cbd5e1;
        cursor: pointer;
        transition: color 0.15s ease-in-out;
        margin: 0;
    }
    .rating-stars-wrapper label:hover,
    .rating-stars-wrapper label:hover ~ label,
    .rating-stars-wrapper input:checked ~ label {
        color: #fbbf24;
    }
    .rating-history-item {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
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
    
    /* Fix for input sizes exceeding containers */
    input, select, textarea {
        box-sizing: border-box;
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
        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('classes.index') }}" class="btn" style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.25); border-radius: 9999px;">
                ← Back to List
            </a>
            @if($class->status === 'active')
                <span style="background: #22c55e; color: white; padding: 0.4rem 1rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span style="width: 8px; height: 8px; background: white; border-radius: 50%; display: inline-block;"></span> Aktif
                </span>
            @elseif($class->status === 'archived')
                <span style="background: #f59e0b; color: white; padding: 0.4rem 1rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                    🗃️ Arsip (Read-only)
                </span>
            @endif
            {{-- Archive / Restore button --}}
            @if(Auth::user()->hasRole(['admin', 'kaprodi', 'dosen', 'baak']))
                <form action="{{ route('classes.archive', $class) }}" method="POST" style="margin: 0;" class="swal-confirm-form" data-swal-msg="{{ $class->status === 'active' ? 'Arsipkan kelas ini? Semua konten akan menjadi read-only.' : 'Aktifkan kembali kelas ini?' }}">
                    @csrf
                    @if($class->status === 'active')
                        <button type="submit" class="btn" style="background: rgba(245,158,11,0.8); color: white; border: 1px solid rgba(255,255,255,0.25); border-radius: 9999px; font-size: 0.85rem;">
                            🗃️ Arsipkan Kelas
                        </button>
                    @elseif($class->status === 'archived')
                        <button type="submit" class="btn" style="background: rgba(34,197,94,0.8); color: white; border: 1px solid rgba(255,255,255,0.25); border-radius: 9999px; font-size: 0.85rem;">
                            ✅ Aktifkan Kembali
                        </button>
                    @endif
                </form>
            @endif
        </div>
    </div>
</div>



@if($class->status === 'archived')
<div style="background: linear-gradient(135deg, #fef3c7, #fde68a); border: 1px solid #f59e0b; border-radius: var(--radius-md); padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
    <span style="font-size: 1.5rem;">🗃️</span>
    <div>
        <strong style="color: #92400e; font-size: 0.95rem;">Kelas Ini Sudah Diarsipkan</strong>
        <p style="margin: 0.2rem 0 0; font-size: 0.8rem; color: #a16207;">Semua konten kelas ini bersifat <strong>read-only</strong>. Tidak dapat menambah atau mengubah data. Untuk mengaktifkan kembali, klik tombol &ldquo;Aktifkan Kembali&rdquo; di atas.</p>
    </div>
</div>
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
    <button class="tab-trigger" data-tab="tab-leaderboard">
        🏆 Leaderboard
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
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">Timeline 14 Sesi Dinamis</h2>
            @if($rpsSessionsWithAssessments->isNotEmpty())
            <p style="margin: 0.25rem 0 0; font-size: 0.8rem; color: var(--text-muted);"
            >📋 Sesi &amp; modul disinkronkan otomatis dari RPS — {{ optional($rpsSessionsWithAssessments->first())['topic_name'] ? 'RPS aktif terhubung' : '' }}</p>
            @endif
        </div>
        @if(Auth::user()->hasRole(['admin', 'kaprodi', 'dosen']))
            <div style="display: flex; gap: 0.5rem;">
                <button class="btn btn-outline" onclick="openAddClassworkModal()">
                    <i>➕</i> Add Classwork Item
                </button>
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
                        @php
                            $rpsSessionInfo = isset($rpsSessionsWithAssessments) ? $rpsSessionsWithAssessments->firstWhere('session_number', $number) : null;
                            $sessionTitle = $rpsSessionInfo['topic_name'] ?? null;
                        @endphp
                        @if($sessionTitle)
                            <span style="font-weight: 500; font-size: 1rem; color: var(--text-primary);">{{ $sessionTitle }}</span>
                        @elseif($sessionData['topics']->count() > 0)
                            <span style="font-weight: 500; font-size: 1rem; color: var(--text-primary);">{{ $sessionData['topics']->first()->title }}</span>
                        @else
                            <span style="font-weight: 400; font-size: 1rem; color: var(--text-muted); font-style: italic;">Topik Belum Diisi</span>
                        @endif
                    </span>
                    <span id="icon-session-{{ $number }}" style="font-size: 1.25rem; color: var(--text-muted); transition: var(--transition);">▼</span>
                </button>
                <div id="session-{{ $number }}" class="session-details">
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
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; color: var(--text-muted);">
                                            {{ $topic->type }}
                                        </span>
                                        @if(Auth::user()->hasRole(['admin', 'kaprodi', 'dosen']))
                                            @if($topic->type == 'materi' && $topic->material)
                                                <button type="button" style="background: none; border: none; padding: 0; color: var(--primary); cursor: pointer; font-size: 1.1rem; display: flex; align-items: center;" title="Edit Materi" 
                                                    data-id="{{ $topic->material->id }}"
                                                    data-title="{{ $topic->material->title }}"
                                                    data-desc="{{ $topic->material->description }}"
                                                    data-files="{{ json_encode($topic->material->original_filenames) }}"
                                                    data-paths="{{ json_encode($topic->material->file_paths) }}"
                                                    data-links="{{ json_encode($topic->material->link_urls) }}"
                                                    onclick="openEditMaterialModal(this)">✏️</button>
                                            @elseif($topic->type == 'assignment' && $topic->assignment)
                                                <button type="button" style="background: none; border: none; padding: 0; color: var(--primary); cursor: pointer; font-size: 1.1rem; display: flex; align-items: center;" title="Edit Tugas" 
                                                    data-id="{{ $topic->assignment->id }}"
                                                    data-title="{{ $topic->assignment->title }}"
                                                    data-instruction="{{ $topic->assignment->instruction }}"
                                                    data-deadline="{{ \Carbon\Carbon::parse($topic->assignment->deadline)->format('Y-m-d\TH:i') }}"
                                                    data-session="{{ $topic->session_number }}"
                                                    data-obe-id="{{ $topic->assignment->rps_assessment_id }}"
                                                    onclick="openEditAssignmentModal(this)">✏️</button>
                                            @elseif($topic->type == 'forum' && $topic->forum)
                                                <button type="button" style="background: none; border: none; padding: 0; color: var(--primary); cursor: pointer; font-size: 1.1rem; display: flex; align-items: center;" title="Edit Forum Diskusi" 
                                                    data-id="{{ $topic->forum->id }}"
                                                    data-title="{{ $topic->forum->title }}"
                                                    data-desc="{{ $topic->forum->context ?? $topic->forum->description }}"
                                                    onclick="openEditForumModal(this)">✏️</button>
                                            @endif
                                            <form action="{{ route('classes.destroy_topic', [$class, $topic]) }}" method="POST" style="margin: 0;" class="swal-confirm-form" data-swal-msg="Yakin ingin menghapus aktivitas ini?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: none; border: none; padding: 0; color: #dc2626; cursor: pointer; font-size: 1.1rem; display: flex; align-items: center;" title="Hapus Aktivitas">🗑️</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($topic->type == 'materi' && $topic->material)
                                    <p style="margin: 0 0 0.5rem 0; font-size: 0.9rem; color: var(--text-muted);">{{ $topic->material->description }}</p>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        @if($topic->material->file_path)
                                            @php
                                                $paths = $topic->material->file_paths;
                                                $names = $topic->material->original_filenames;
                                            @endphp
                                            @foreach($paths as $index => $path)
                                                @if(\Illuminate\Support\Facades\Storage::exists($path) || \Illuminate\Support\Facades\Storage::disk('public')->exists($path))
                                                    <a href="{{ route('classes.download_material', [$class, $topic->material]) }}?file_index={{ $index }}" target="_blank" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                        <i>👁️</i> Buka / Download ({{ $names[$index] ?? 'File ' . ($index + 1) }})
                                                    </a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($topic->material->link_url)
                                            @php
                                                $links = $topic->material->link_urls;
                                            @endphp
                                            @foreach($links as $index => $link)
                                                <a href="{{ $link }}" target="_blank" class="btn btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                    <i>🔗</i> Buka Tautan {{ count($links) > 1 ? '#' . ($index + 1) : '' }}
                                                </a>
                                            @endforeach
                                        @endif
                                    </div>
                                @elseif($topic->type == 'assignment' && $topic->assignment)
                                    <p style="margin: 0 0 0.5rem 0; font-size: 0.9rem; color: var(--text-muted);">{{ Str::limit($topic->assignment->instruction, 150) }}</p>
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem; gap: 0.5rem; flex-wrap: wrap;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <span style="font-size: 0.8rem; color: #dc2626; font-weight: 600;">
                                                Deadline: {{ date('d M Y - H:i', strtotime($topic->assignment->deadline)) }}
                                            </span>
                                            @if(!Auth::user()->hasRole('mahasiswa'))
                                                <button type="button"
                                                    data-id="{{ $topic->assignment->id }}"
                                                    data-title="{{ $topic->assignment->title }}"
                                                    data-instruction="{{ $topic->assignment->instruction }}"
                                                    data-deadline="{{ \Carbon\Carbon::parse($topic->assignment->deadline)->format('Y-m-d\TH:i') }}"
                                                    data-session="{{ $topic->session_number }}"
                                                    data-obe-id="{{ $topic->assignment->rps_assessment_id }}"
                                                    onclick="openEditAssignmentModal(this)"
                                                    title="Edit Tugas"
                                                    style="background: rgba(79,70,229,0.1); border: 1px solid rgba(79,70,229,0.3); color: #4f46e5; border-radius: 6px; padding: 0.2rem 0.5rem; font-size: 0.75rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.2rem;">
                                                    ✏️ Edit
                                                </button>
                                            @endif
                                        </div>
                                        @if(Auth::user()->hasRole('mahasiswa'))
                                            @php
                                                $submission = \App\Models\AssignmentSubmission::where('assignment_id', $topic->assignment->id)
                                                    ->where('student_id', optional(Auth::user()->student)->id)
                                                    ->first();
                                            @endphp
                                            @if($submission)
                                                <a href="{{ route('assignments.show', $topic->assignment) }}" class="btn btn-success" style="padding: 0.35rem 0.85rem; font-size: 0.8rem;">
                                                    <i class="fas fa-check-circle me-1"></i> Submitted
                                                </a>
                                            @else
                                                <a href="{{ route('assignments.show', $topic->assignment) }}" class="btn" style="padding: 0.35rem 0.85rem; font-size: 0.8rem;">
                                                    Kumpulkan Tugas
                                                </a>
                                            @endif
                                        @else
                                            <a href="{{ route('assignments.show', $topic->assignment) }}" class="btn btn-outline" style="padding: 0.35rem 0.85rem; font-size: 0.8rem;">
                                                Grading & Submissions
                                            </a>
                                        @endif
                                    </div>
                                @elseif($topic->type == 'forum' && $topic->forum)
                                    <p style="margin: 0 0 0.5rem 0; font-size: 0.9rem; color: var(--text-muted);">{{ $topic->forum->context ?? $topic->forum->description }}</p>
                                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem 1rem; margin-top: 0.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <span style="font-size: 0.85rem; color: var(--text-primary); font-weight: 600;">💬 Ruang Diskusi Kelas</span>
                                            <span style="background: #e0e7ff; color: #4f46e5; font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 12px;">
                                                {{ $topic->forum->posts->count() }} Diskusi
                                            </span>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <a href="{{ route('classes.forums.show', [$class, $topic->forum]) }}" class="btn" style="padding: 0.35rem 0.85rem; font-size: 0.8rem; background: var(--primary); color: white; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem;">
                                                💬 Masuk / Tulis Diskusi
                                            </a>
                                            @if(Auth::user()->hasRole(['admin', 'kaprodi', 'dosen']))
                                                <button type="button"
                                                    data-id="{{ $topic->forum->id }}"
                                                    data-title="{{ $topic->forum->title }}"
                                                    data-desc="{{ $topic->forum->context ?? $topic->forum->description }}"
                                                    onclick="openEditForumModal(this)"
                                                    title="Edit Forum"
                                                    style="background: rgba(79,70,229,0.1); border: 1px solid rgba(79,70,229,0.3); color: #4f46e5; border-radius: 6px; padding: 0.35rem 0.65rem; font-size: 0.8rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.2rem;">
                                                    ✏️ Edit
                                                </button>
                                            @endif
                                        </div>
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
                                            <a href="{{ route('quizzes.show', $topic->quiz) }}" class="btn btn-outline" style="padding: 0.35rem 0.85rem; font-size: 0.8rem;">
                                                Kelola Kuis
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p style="margin: 0; color: var(--text-muted); font-size: 0.875rem; text-align: center; padding: 1rem 0;">Belum ada aktivitas pada sesi ini.</p>
                    @endforelse

                    <!-- Rating Sesi / Pertemuan (Khusus Mahasiswa) -->
                    @if(Auth::user()->student && $lecturers->count() > 0)
                        @php
                            $sessionRatings = isset($myRatings[$number]) ? $myRatings[$number] : collect();
                            // Filter dosen mana saja yang sudah dinilai mahasiswa pada sesi ini
                            $ratedDosenIds = $sessionRatings->pluck('dosen_id')->toArray();
                            // Filter dosen yang terdaftar di kelas dan memiliki relasi 'dosen'
                            $classDosens = $lecturers->filter(fn($l) => $l->dosen !== null);
                        @endphp

                        @if($classDosens->count() > 0)
                            <div class="rating-container">
                                <h4 style="margin: 0 0 0.5rem 0; font-size: 1rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                                    ⭐ Penilaian Dosen untuk Sesi {{ $number }}
                                </h4>
                                <p style="margin: 0 0 1rem 0; font-size: 0.85rem; color: var(--text-muted);">
                                    Berikan penilaian Anda terhadap dosen pengampu pada pertemuan ini untuk membantu meningkatkan kualitas pengajaran.
                                </p>

                                <!-- Tampilkan riwayat rating yang sudah diberikan -->
                                @if($sessionRatings->count() > 0)
                                    <div style="margin-bottom: 1rem;">
                                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-primary); display: block; margin-bottom: 0.5rem;">Rating yang telah Anda kirimkan:</span>
                                        @foreach($sessionRatings as $ratingRecord)
                                            <div class="rating-history-item">
                                                <div>
                                                    <strong style="font-size: 0.85rem; color: var(--text-primary); display: block;">
                                                        {{ $ratingRecord->dosen->nama_dosen }}
                                                    </strong>
                                                    @if($ratingRecord->comments)
                                                        <span style="font-size: 0.8rem; color: var(--text-muted); font-style: italic; display: block; margin-top: 0.25rem;">
                                                            "{{ $ratingRecord->comments }}"
                                                        </span>
                                                    @endif
                                                </div>
                                                <div style="color: #fbbf24; font-weight: bold; font-size: 1rem; display: flex; align-items: center; gap: 0.15rem;">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        {{ $i <= $ratingRecord->rating ? '★' : '☆' }}
                                                    @endfor
                                                    <span style="font-size: 0.75rem; color: var(--text-muted); margin-left: 0.25rem; font-weight: normal;">({{ $ratingRecord->rating }}/5)</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Form rating (hanya jika ada dosen yang belum dinilai) -->
                                @php
                                    $unratedDosens = $classDosens->filter(fn($l) => !in_array($l->dosen->id, $ratedDosenIds));
                                @endphp

                                @if($unratedDosens->count() > 0)
                                    <form action="{{ route('classes.rate_session', [$class, $number]) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        
                                        <!-- Pemilihan Dosen (jika dosen pengampu lebih dari satu) -->
                                        <div style="margin-bottom: 0.75rem;">
                                            <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); display: block; margin-bottom: 0.25rem;">Pilih Dosen Pengajar:</label>
                                            @if($unratedDosens->count() == 1)
                                                @php $singleDosen = $unratedDosens->first(); @endphp
                                                <input type="hidden" name="dosen_id" value="{{ $singleDosen->dosen->id }}">
                                                <div style="padding: 0.5rem 0.75rem; background: white; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.9rem; color: var(--text-primary); font-weight: 500;">
                                                    {{ $singleDosen->name }}
                                                </div>
                                            @else
                                                <select name="dosen_id" required style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.9rem; background: white;">
                                                    <option value="" disabled selected>-- Pilih Dosen --</option>
                                                    @foreach($unratedDosens as $dUser)
                                                        <option value="{{ $dUser->dosen->id }}">{{ $dUser->name }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>

                                        <!-- Rating Bintang -->
                                        <div style="margin-bottom: 0.75rem;">
                                            <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); display: block; margin-bottom: 0.15rem;">Beri Rating:</label>
                                            <div class="rating-stars-wrapper">
                                                <input type="radio" id="star5-{{ $number }}" name="rating" value="5" required />
                                                <label for="star5-{{ $number }}" title="Sangat Baik">★</label>
                                                <input type="radio" id="star4-{{ $number }}" name="rating" value="4" />
                                                <label for="star4-{{ $number }}" title="Baik">★</label>
                                                <input type="radio" id="star3-{{ $number }}" name="rating" value="3" />
                                                <label for="star3-{{ $number }}" title="Cukup">★</label>
                                                <input type="radio" id="star2-{{ $number }}" name="rating" value="2" />
                                                <label for="star2-{{ $number }}" title="Kurang">★</label>
                                                <input type="radio" id="star1-{{ $number }}" name="rating" value="1" />
                                                <label for="star1-{{ $number }}" title="Sangat Kurang">★</label>
                                            </div>
                                        </div>

                                        <!-- Komentar -->
                                        <div style="margin-bottom: 1rem;">
                                            <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); display: block; margin-bottom: 0.25rem;">Ulasan / Komentar (Opsional):</label>
                                            <textarea name="comments" rows="2" placeholder="Tulis masukan Anda mengenai materi atau cara penyampaian dosen..." style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.9rem; resize: vertical; font-family: inherit;"></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary" style="padding: 0.4rem 1rem; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            📤 Kirim Penilaian
                                        </button>
                                    </form>
                                @else
                                    <div style="padding: 0.75rem; background: #ecfdf5; color: #047857; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-align: center; border: 1px solid #a7f3d0; margin-top: 0.5rem;">
                                        ✅ Anda sudah memberikan rating untuk semua dosen pengampu di pertemuan ini.
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endif

                    <!-- Ringkasan Rating Sesi / Pertemuan (Dinamis Berbasis Permission) -->
                    @if(Auth::user()->can('view-ratings-anonymous') || Auth::user()->can('view-ratings-transparent'))
                        @php
                            $sessionRatings = isset($allRatings[$number]) ? $allRatings[$number] : collect();
                            $avgRating = $sessionRatings->count() > 0 ? round($sessionRatings->avg('rating'), 1) : 0;
                        @endphp
                        
                        <div class="rating-container" style="border-left: 4px solid var(--primary); margin-top: 1.5rem;">
                            <h4 style="margin: 0 0 0.5rem 0; font-size: 1rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; justify-content: space-between;">
                                <span>📊 Umpan Balik & Rating Sesi {{ $number }}</span>
                                @if($sessionRatings->count() > 0)
                                    <span style="font-size: 0.85rem; background: #fef3c7; color: #d97706; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        ⭐ {{ $avgRating }} / 5.0
                                    </span>
                                @endif
                            </h4>
                            
                            @if($sessionRatings->count() > 0)
                                <p style="margin: 0 0 1rem 0; font-size: 0.85rem; color: var(--text-muted);">
                                    Menerima <strong>{{ $sessionRatings->count() }} penilaian</strong> dari mahasiswa pada sesi ini.
                                </p>
                                
                                <div style="max-height: 250px; overflow-y: auto; padding-right: 0.5rem;">
                                    @foreach($sessionRatings as $ratingRecord)
                                        <div class="rating-history-item" style="border-left: 3px solid #fbbf24; margin-bottom: 0.75rem; background: white; border: 1px solid var(--border-color); border-radius: 6px; padding: 0.75rem 1rem; display: flex; align-items: flex-start; justify-content: space-between;">
                                            <div style="flex: 1; width: 100%;">
                                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem; flex-wrap: wrap; gap: 0.5rem;">
                                                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary);">
                                                        @if(Auth::user()->can('view-ratings-transparent'))
                                                            {{ $ratingRecord->student->nama_student }} <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal;">(NIM: {{ $ratingRecord->student->nim }})</span>
                                                        @else
                                                            Mahasiswa (Anonim)
                                                        @endif
                                                    </span>
                                                    <span style="color: #fbbf24; font-weight: bold; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.1rem;">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            {{ $i <= $ratingRecord->rating ? '★' : '☆' }}
                                                        @endfor
                                                    </span>
                                                </div>
                                                
                                                @if(Auth::user()->can('view-ratings-transparent'))
                                                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">
                                                        Dinilai untuk: <strong>{{ $ratingRecord->dosen->nama_dosen }}</strong>
                                                    </span>
                                                @endif
                                                
                                                @if($ratingRecord->comments)
                                                    <span style="font-size: 0.8rem; color: var(--text-primary); font-style: italic; display: block; background: #f1f5f9; padding: 0.4rem 0.6rem; border-radius: 4px; margin-top: 0.25rem;">
                                                        "{{ $ratingRecord->comments }}"
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p style="margin: 0; padding: 1rem; background: white; border: 1px dashed var(--border-color); border-radius: 6px; text-align: center; color: var(--text-muted); font-size: 0.85rem;">
                                    Belum ada penilaian atau komentar yang dikirimkan oleh mahasiswa untuk sesi ini.
                                </p>
                            @endif
                        </div>
                    @endif
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
            <div class="card-header" style="background: white; border-bottom: 1px solid var(--border-color); padding: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--text-primary);">Dosen Pengampu / Lecturer</h3>
                @if(Auth::user()->can('edit-classes') || Auth::user()->hasRole(['admin', 'kaprodi', 'baak']))
                    <button class="btn btn-primary btn-sm" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;" onclick="document.getElementById('modal-add-dosen').style.display = 'flex'">
                        ➕ Tambah Dosen
                    </button>
                @endif
            </div>
            <div class="card-body" style="padding: 0;">
                <ul style="list-style: none; margin: 0; padding: 0;">
                    @forelse($lecturers as $dosenUser)
                        <li style="padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem; border-bottom: 1px solid var(--border-color);">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: #e0e7ff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                {{ strtoupper(substr($dosenUser->name, 0, 1)) }}
                            </div>
                            <div style="flex: 1;">
                                <strong style="display: block; color: var(--text-primary);">{{ $dosenUser->name }}</strong>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $dosenUser->email }}</span>
                                @if($dosenUser->dosen)
                                    <span style="font-size: 0.75rem; background: #e0e7ff; color: var(--primary); padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.25rem;">
                                        {{ $dosenUser->dosen->nidn ?? '' }}
                                    </span>
                                    <span style="font-size: 0.75rem; background: #fef3c7; color: #d97706; padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.25rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.15rem;" title="{{ __('Rata-rata Rating Kuliah') }}">
                                        ⭐ {{ number_format($dosenUser->dosen->average_rating, 1) }} ({{ $dosenUser->dosen->sessionRatings()->count() }} {{ __('ulasan') }})
                                    </span>
                                @endif
                            </div>
                            @if(Auth::user()->can('edit-classes') || Auth::user()->hasRole(['admin', 'kaprodi', 'baak']))
                                <form action="{{ route('classes.remove_staff', [$class, $dosenUser]) }}" method="POST" style="margin: 0;" class="swal-confirm-form" data-swal-msg="Hapus dosen ini dari kelas?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #dc2626; border-color: #fecaca;">Hapus</button>
                                </form>
                            @endif
                        </li>
                    @empty
                        <li style="padding: 2rem; text-align: center; color: var(--text-muted);">Tidak ada dosen yang terdaftar di kelas ini.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- BAAK Staff Section -->
        <div class="card">
            <div class="card-header" style="background: white; border-bottom: 1px solid var(--border-color); padding: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--text-primary);">BAAK Staff</h3>
                @if(Auth::user()->can('edit-classes') || Auth::user()->hasRole(['admin', 'kaprodi', 'baak']))
                    <button class="btn btn-primary btn-sm" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;" onclick="document.getElementById('modal-add-baak').style.display = 'flex'">
                        ➕ Tambah BAAK
                    </button>
                @endif
            </div>
            <div class="card-body" style="padding: 0;">
                <ul style="list-style: none; margin: 0; padding: 0;">
                    @forelse($baakStaff as $baakUser)
                        <li style="padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem; border-bottom: 1px solid var(--border-color);">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; color: var(--text-muted); display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                {{ strtoupper(substr($baakUser->name, 0, 1)) }}
                            </div>
                            <div style="flex: 1;">
                                <strong style="display: block; color: var(--text-primary);">{{ $baakUser->name }}</strong>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $baakUser->email }}</span>
                            </div>
                            @if(Auth::user()->can('edit-classes') || Auth::user()->hasRole(['admin', 'kaprodi', 'baak']))
                                <form action="{{ route('classes.remove_staff', [$class, $baakUser]) }}" method="POST" style="margin: 0;" class="swal-confirm-form" data-swal-msg="Hapus BAAK ini dari kelas?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #dc2626; border-color: #fecaca;">Hapus</button>
                                </form>
                            @endif
                        </li>
                    @empty
                        <li style="padding: 2rem; text-align: center; color: var(--text-muted);">Tidak ada staff BAAK yang terdaftar di kelas ini.</li>
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
                @if(Auth::user()->can('enroll-students') || Auth::user()->hasRole(['admin', 'kaprodi', 'dosen', 'baak']))
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('classes.template') }}" class="btn btn-outline btn-sm" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">
                            <i>📥</i> Template
                        </a>
                        <button class="btn btn-outline btn-sm" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;" onclick="document.getElementById('modal-import').style.display = 'flex'">
                            <i>📄</i> Import CSV
                        </button>
                        <button class="btn btn-primary btn-sm" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;" onclick="document.getElementById('modal-add').style.display = 'flex'">
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
                            <th style="padding: 1rem; border-bottom: 1px solid var(--border-color); background: #f8fafc; text-align: left;">Program Studi</th>
                            <th style="padding: 1rem; border-bottom: 1px solid var(--border-color); background: #f8fafc; text-align: left;">Angkatan</th>
                            @if(Auth::user()->can('enroll-students') || Auth::user()->hasRole(['admin', 'kaprodi', 'dosen', 'baak']))
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
                            <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">{{ optional($enrollment->student)->prodi->nama_prodi ?? '-' }}</td>
                            <td style="padding: 1rem; border-bottom: 1px solid var(--border-color);">{{ optional($enrollment->student)->angkatan ?? '-' }}</td>
                            @if(Auth::user()->can('enroll-students') || Auth::user()->hasRole(['admin', 'kaprodi', 'dosen', 'baak']))
                                <td style="padding: 1rem; border-bottom: 1px solid var(--border-color); text-align: right;">
                                    <form action="{{ route('classes.unenroll', [$class, $enrollment]) }}" method="POST" style="margin: 0;" class="form-unenroll">
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
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <a href="{{ route('classes.export_grades', $class) }}" class="btn btn-outline" style="font-size: 0.85rem; padding: 0.4rem 0.8rem; border-color: #10b981; color: #10b981; display: flex; align-items: center; gap: 0.4rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        Export to Excel
                    </a>
                    <span style="font-size: 0.8rem; background: #e0e7ff; color: var(--primary); padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 600;">
                        OBE Sync Enabled
                    </span>
                </div>
            </div>
            @php
                $classTopics = isset($topics) ? $topics : ($class->topics ?? collect());
                // Group assignments by session_number using $classTopics mapping
                $assignmentsBySession = $assignments->groupBy(function($assign) use ($classTopics) {
                    $topic = $classTopics->where('type', 'assignment')->where('content_id', $assign->id)->first();
                    return $topic ? $topic->session_number : ($assign->session_number ?? 1);
                })->sortKeys();
            @endphp
            <div class="card-body" style="padding: 0; overflow-x: auto;">
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mahasiswa</th>
                            <!-- Assignment Columns Grouped by Session -->
                            @foreach($assignmentsBySession as $sessNum => $sessAssignments)
                                <th style="min-width: 130px; text-align: center;">
                                    @if($sessAssignments->count() > 1)
                                        <div style="font-weight: 700;">Sesi {{ $sessNum }}</div>
                                        <div style="font-size: 0.7rem; color: #4f46e5; font-weight: 600;">({{ $sessAssignments->count() }} Tugas - Rata-rata)</div>
                                    @else
                                        <div style="font-weight: 700;">Tugas Sesi {{ $sessNum }}</div>
                                        <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: normal;">{{ Str::limit($sessAssignments->first()->title, 16) }}</div>
                                    @endif
                                </th>
                            @endforeach
                            <!-- Quiz Columns -->
                            @foreach($quizzes as $quiz)
                                <th style="min-width: 120px; text-align: center;">Kuis: {{ Str::limit($quiz->title, 15) }}</th>
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
                                <!-- Render Assignment scores per Session -->
                                @foreach($assignmentsBySession as $sessNum => $sessAssignments)
                                    @php
                                        $sessDetails = [];
                                        $scores = [];
                                        $hasSubmitted = false;

                                        foreach($sessAssignments as $assign) {
                                            $sub = isset($submissions[$studentId]) 
                                                ? $submissions[$studentId]->where('assignment_id', $assign->id)->first() 
                                                : null;
                                            
                                            $gradeObj = \App\Models\StudentGrade::where('enrollment_id', $enroll->id)
                                                ->where('rps_assessment_id', $assign->rps_assessment_id)
                                                ->first();

                                            $scoreVal = null;
                                            if ($gradeObj && is_numeric($gradeObj->score)) {
                                                $scoreVal = (float) $gradeObj->score;
                                            } elseif ($sub && is_numeric($sub->score)) {
                                                $scoreVal = (float) $sub->score;
                                            }

                                            if ($scoreVal !== null) {
                                                $scores[] = $scoreVal;
                                            }

                                            if ($sub) {
                                                $hasSubmitted = true;
                                            }

                                            $sessDetails[] = [
                                                'title'  => $assign->title,
                                                'score'  => $scoreVal !== null ? number_format($scoreVal, 1) : '-',
                                                'status' => $gradeObj ? 'Graded' : ($sub ? ($sub->status ?? 'Submitted') : 'Belum Dikumpulkan'),
                                            ];
                                        }

                                        $avgScore = count($scores) > 0 ? number_format(array_sum($scores) / count($scores), 1) : null;
                                        $isMultiple = $sessAssignments->count() > 1;
                                        $studentName = optional($enroll->student)->nama_student ?? 'Mahasiswa';
                                    @endphp
                                    <td style="text-align: center;">
                                        @if(optional($enroll->student)->is_frozen)
                                            <span class="badge-score" style="background: #fee2e2; color: #991b1b; border: 1px solid #f87171;" title="Mahasiswa belum eligible (administrasi)">Belum Eligible</span>
                                        @elseif($isMultiple)
                                            @if($avgScore !== null)
                                                <button type="button" class="badge-score passed" 
                                                        style="cursor: pointer; border: 1px dashed #4f46e5; background: #eef2ff; color: #4f46e5; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.35rem 0.65rem;"
                                                        onclick='openSessionGradeBreakdown("{{ addslashes($studentName) }}", "{{ $sessNum }}", {{ json_encode($sessDetails) }}, "{{ $avgScore }}")'
                                                        title="Klik untuk melihat rincian {{ $sessAssignments->count() }} tugas Sesi {{ $sessNum }}">
                                                    <span>{{ $avgScore }}</span>
                                                    <span style="font-size: 0.65rem; background: #4f46e5; color: white; border-radius: 9999px; padding: 1px 5px; font-weight: 600;">Avg 🔍</span>
                                                </button>
                                            @elseif($hasSubmitted)
                                                <button type="button" class="badge-score pending" 
                                                        style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.3rem;"
                                                        onclick='openSessionGradeBreakdown("{{ addslashes($studentName) }}", "{{ $sessNum }}", {{ json_encode($sessDetails) }}, null)'
                                                        title="Klik untuk melihat rincian {{ $sessAssignments->count() }} tugas Sesi {{ $sessNum }}">
                                                    <span>Submitted</span>
                                                    <span style="font-size: 0.7rem;">🔍</span>
                                                </button>
                                            @else
                                                <button type="button" class="badge-score empty" 
                                                        style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.2rem;"
                                                        onclick='openSessionGradeBreakdown("{{ addslashes($studentName) }}", "{{ $sessNum }}", {{ json_encode($sessDetails) }}, null)'
                                                        title="Klik untuk melihat rincian {{ $sessAssignments->count() }} tugas Sesi {{ $sessNum }}">
                                                    <span>-</span>
                                                    <span style="font-size: 0.65rem; color: #94a3b8;">🔍</span>
                                                </button>
                                            @endif
                                        @else
                                            {{-- Single Assignment in Session --}}
                                            @php
                                                $singleAssign = $sessAssignments->first();
                                                $singleSub = isset($submissions[$studentId]) 
                                                    ? $submissions[$studentId]->where('assignment_id', $singleAssign->id)->first() 
                                                    : null;
                                                $singleGrade = \App\Models\StudentGrade::where('enrollment_id', $enroll->id)
                                                    ->where('rps_assessment_id', $singleAssign->rps_assessment_id)
                                                    ->first();
                                            @endphp
                                            @if($singleGrade)
                                                <span class="badge-score passed">{{ $singleGrade->score }}</span>
                                            @elseif($singleSub)
                                                <a href="{{ route('assignments.show', $singleAssign) }}" class="badge-score pending" style="text-decoration: none; display: inline-block;">
                                                    {{ $singleSub->status ?? 'Submitted' }} (Grade)
                                                </a>
                                            @else
                                                <span class="badge-score empty">-</span>
                                            @endif
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
                                    <td style="text-align: center;">
                                        @if(optional($enroll->student)->is_frozen)
                                            <span class="badge-score" style="background: #fee2e2; color: #991b1b; border: 1px solid #f87171;" title="Mahasiswa belum eligible (administrasi)">Belum Eligible</span>
                                        @elseif($attempt)
                                            <span class="badge-score passed">{{ $attempt->score }}</span>
                                        @else
                                            <span class="badge-score empty">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 2 + $assignmentsBySession->count() + $quizzes->count() }}" style="padding: 2rem; text-align: center; color: var(--text-muted);">Belum ada data nilai di kelas ini.</td>
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
<!-- TAB 4: LEADERBOARD                           -->
<!-- ============================================ -->
<div id="tab-leaderboard" class="tab-content">
    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); color: white; border-radius: var(--radius-lg); padding: 2rem; margin-bottom: 2rem; box-shadow: var(--shadow-md); position: relative; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; position: relative; z-index: 1;">
            <div>
                <span style="background: rgba(255,255,255,0.15); color: #fbbf24; font-size: 0.8rem; font-weight: 700; padding: 0.25rem 0.75rem; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em;">
                    ⚡ Live Gamification Ranking
                </span>
                <h2 style="margin: 0.5rem 0 0.25rem 0; font-size: 1.85rem; font-weight: 800; color: white;">Leaderboard Kelas</h2>
                <p style="margin: 0; opacity: 0.85; font-size: 0.95rem;">
                    Peringkat mahasiswa berdasarkan akumulasi Nilai Tugas, Kuis & Kecepatan Pengumpulan (Tie-Breaker).
                </p>
            </div>
            <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(8px); padding: 0.85rem 1.25rem; border-radius: var(--radius-md); border: 1px solid rgba(255,255,255,0.15); text-align: center;">
                <div style="font-size: 1.5rem; font-weight: 800; color: #fbbf24;">{{ count($leaderboard) }}</div>
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.8;">Total Peserta</div>
            </div>
        </div>
    </div>

    <!-- Tie-breaker Information Alert -->
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: var(--radius-md); padding: 1rem 1.25rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
        <span style="font-size: 1.5rem;">ℹ️</span>
        <div style="font-size: 0.85rem; color: #1e40af; line-height: 1.5;">
            <strong>Ketentuan Penentuan Peringkat:</strong>
            <ol style="margin: 0.25rem 0 0 1.25rem; padding: 0;">
                <li><strong>Prioritas Utama:</strong> Total Akumulasi Nilai (Tugas + Kuis).</li>
                <li><strong>Tie-Breaker 1 (Kecepatan Kuis):</strong> Jika total nilai sama, mahasiswa dengan total durasi pengerjaan kuis tercepat akan lebih unggul.</li>
                <li><strong>Tie-Breaker 2 (Waktu Kumpul):</strong> Jika durasi kuis juga sama, mahasiswa dengan timestamp pengumpulan tugas terawal menempati peringkat lebih tinggi.</li>
            </ol>
        </div>
    </div>

    @if(count($leaderboard) > 0)
        <!-- Podium Top 3 -->
        @php
            $top1 = $leaderboard->get(0);
            $top2 = $leaderboard->get(1);
            $top3 = $leaderboard->get(2);
        @endphp
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem; align-items: end;">
            
            <!-- Rank 2: Silver -->
            @if($top2)
            <div class="card" style="border: 2px solid #cbd5e1; background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%); text-align: center; padding: 1.5rem; position: relative; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
                <div style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); background: #94a3b8; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; border: 3px solid white; box-shadow: var(--shadow-sm);">
                    2
                </div>
                <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #94a3b8, #64748b); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.5rem; margin: 0.75rem auto 0.75rem auto; border: 3px solid #e2e8f0;">
                    {{ strtoupper(substr($top2['name'], 0, 1)) }}
                </div>
                <h4 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: var(--text-primary);">{{ $top2['name'] }}</h4>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.75rem;">NIM: {{ $top2['nim'] }}</div>
                <div style="background: #f1f5f9; padding: 0.5rem; border-radius: var(--radius-md);">
                    <div style="font-size: 1.35rem; font-weight: 800; color: #475569;">{{ number_format($top2['total_score'], 1) }} <span style="font-size: 0.75rem; font-weight: 600;">pts</span></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">⏱️ Durasi Quiz: {{ floor($top2['total_quiz_duration'] / 60) }}m {{ $top2['total_quiz_duration'] % 60 }}s</div>
                </div>
            </div>
            @endif

            <!-- Rank 1: Gold (Center & Bigger) -->
            @if($top1)
            <div class="card" style="border: 2px solid #f59e0b; background: linear-gradient(180deg, #fffbeb 0%, #ffffff 100%); text-align: center; padding: 1.75rem 1.5rem; position: relative; border-radius: var(--radius-lg); box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.25); transform: translateY(-8px);">
                <div style="position: absolute; top: -18px; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg, #f59e0b, #d97706); color: white; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.25rem; border: 3px solid white; box-shadow: var(--shadow-md);">
                    👑
                </div>
                <div style="width: 76px; height: 76px; border-radius: 50%; background: linear-gradient(135deg, #fbbf24, #d97706); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.85rem; margin: 0.75rem auto 0.75rem auto; border: 4px solid #fef3c7; box-shadow: 0 0 15px rgba(245, 158, 11, 0.4);">
                    {{ strtoupper(substr($top1['name'], 0, 1)) }}
                </div>
                <span style="background: #fef3c7; color: #b45309; font-size: 0.75rem; font-weight: 800; padding: 0.2rem 0.6rem; border-radius: 9999px; text-transform: uppercase;">🥇 Juara 1</span>
                <h4 style="margin: 0.5rem 0 0 0; font-size: 1.2rem; font-weight: 800; color: var(--text-primary);">{{ $top1['name'] }}</h4>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.75rem;">NIM: {{ $top1['nim'] }}</div>
                <div style="background: #fef3c7; padding: 0.6rem; border-radius: var(--radius-md); border: 1px solid #fde68a;">
                    <div style="font-size: 1.6rem; font-weight: 900; color: #b45309;">{{ number_format($top1['total_score'], 1) }} <span style="font-size: 0.8rem; font-weight: 700;">pts</span></div>
                    <div style="font-size: 0.75rem; color: #92400e; font-weight: 600;">⏱️ Durasi Quiz: {{ floor($top1['total_quiz_duration'] / 60) }}m {{ $top1['total_quiz_duration'] % 60 }}s</div>
                </div>
            </div>
            @endif

            <!-- Rank 3: Bronze -->
            @if($top3)
            <div class="card" style="border: 2px solid #d97706; background: linear-gradient(180deg, #fff7ed 0%, #ffffff 100%); text-align: center; padding: 1.5rem; position: relative; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
                <div style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); background: #b45309; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; border: 3px solid white; box-shadow: var(--shadow-sm);">
                    3
                </div>
                <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #d97706, #b45309); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.5rem; margin: 0.75rem auto 0.75rem auto; border: 3px solid #ffedd5;">
                    {{ strtoupper(substr($top3['name'], 0, 1)) }}
                </div>
                <h4 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: var(--text-primary);">{{ $top3['name'] }}</h4>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.75rem;">NIM: {{ $top3['nim'] }}</div>
                <div style="background: #ffedd5; padding: 0.5rem; border-radius: var(--radius-md);">
                    <div style="font-size: 1.35rem; font-weight: 800; color: #9a3412;">{{ number_format($top3['total_score'], 1) }} <span style="font-size: 0.75rem; font-weight: 600;">pts</span></div>
                    <div style="font-size: 0.75rem; color: #9a3412;">⏱️ Durasi Quiz: {{ floor($top3['total_quiz_duration'] / 60) }}m {{ $top3['total_quiz_duration'] % 60 }}s</div>
                </div>
            </div>
            @endif

        </div>

        <!-- Leaderboard Full Table -->
        <div class="card">
            <div class="card-header" style="background: white; border-bottom: 1px solid var(--border-color); padding: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--text-primary);">Daftar Peringkat Lengkap</h3>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Diperbarui secara real-time saat tugas/quiz dinilai</span>
            </div>
            <div class="table-responsive">
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th style="width: 70px; text-align: center;">Rank</th>
                            <th>Mahasiswa</th>
                            <th style="text-align: right;">Nilai Tugas</th>
                            <th style="text-align: right;">Nilai Kuis</th>
                            <th style="text-align: right;">Total Skor</th>
                            <th style="text-align: center;">Durasi Kuis</th>
                            <th style="text-align: center;">Submit Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaderboard as $index => $row)
                        @php
                            $rank = $index + 1;
                            $isTop1 = $rank === 1;
                            $isTop2 = $rank === 2;
                            $isTop3 = $rank === 3;
                            $isCurrentUser = (Auth::user()->student && Auth::user()->student->id === $row['student_id']);
                        @endphp
                        <tr style="{{ $isCurrentUser ? 'background-color: #eff6ff; font-weight: 600;' : '' }}">
                            <td style="text-align: center;">
                                @if($isTop1)
                                    <span style="font-size: 1.25rem;">🥇</span>
                                @elseif($isTop2)
                                    <span style="font-size: 1.25rem;">🥈</span>
                                @elseif($isTop3)
                                    <span style="font-size: 1.25rem;">🥉</span>
                                @else
                                    <span style="font-weight: 700; color: var(--text-muted); font-size: 0.9rem;">#{{ $rank }}</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem;">
                                        {{ strtoupper(substr($row['name'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: var(--text-primary); font-size: 0.9rem;">
                                            {{ $row['name'] }}
                                            @if($isCurrentUser)
                                                <span style="background: #3b82f6; color: white; font-size: 0.65rem; padding: 0.1rem 0.4rem; border-radius: 9999px; margin-left: 0.3rem;">Anda</span>
                                            @endif
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">NIM: {{ $row['nim'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: right; font-weight: 600; color: #10b981;">
                                {{ number_format($row['assignment_score'], 1) }}
                            </td>
                            <td style="text-align: right; font-weight: 600; color: #f59e0b;">
                                {{ number_format($row['quiz_score'], 1) }}
                            </td>
                            <td style="text-align: right;">
                                <span style="font-size: 1rem; font-weight: 800; color: #4f46e5; background: #eff6ff; padding: 0.25rem 0.6rem; border-radius: var(--radius-md);">
                                    {{ number_format($row['total_score'], 1) }}
                                </span>
                            </td>
                            <td style="text-align: center; font-size: 0.85rem; color: var(--text-muted);">
                                @if($row['total_quiz_duration'] > 0)
                                    ⏱️ {{ floor($row['total_quiz_duration'] / 60) }}m {{ $row['total_quiz_duration'] % 60 }}s
                                @else
                                    <span style="color: #cbd5e1;">-</span>
                                @endif
                            </td>
                            <td style="text-align: center; font-size: 0.8rem; color: var(--text-muted);">
                                @if($row['last_submitted_at'])
                                    📅 {{ \Carbon\Carbon::parse($row['last_submitted_at'])->format('d M Y, H:i') }}
                                @else
                                    <span style="color: #cbd5e1;">Belum submit</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card" style="padding: 3rem; text-align: center; color: var(--text-muted);">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">🏆</div>
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">Leaderboard Belum Tersedia</h3>
            <p style="margin: 0; font-size: 0.9rem;">Belum ada mahasiswa yang terdaftar di kelas ini.</p>
        </div>
    @endif
</div>

<!-- ============================================ -->
<!-- TAB 5: SETTINGS                              -->
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
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Mata Kuliah</label>
                                <input type="hidden" name="subject_id" value="{{ $class->subject_id }}">
                                <input type="text" value="{{ $class->subject->nama_subject }}" disabled style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: #f1f5f9; color: var(--text-muted); cursor: not-allowed; font-weight: 500;">
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
                            <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 600; cursor: pointer; margin: 0;">
                                <input type="hidden" name="status" value="archived">
                                <input type="checkbox" name="status" value="active" {{ $class->status === 'active' ? 'checked' : '' }} style="width: 18px; height: 18px;">
                                Aktifkan kelas ini untuk pembelajaran mahasiswa (Active Classroom)
                            </label>
                            <p style="margin: 0.25rem 0 0 1.5rem; font-size: 0.8rem; color: var(--text-muted);">
                                Jika dinonaktifkan, kelas akan masuk ke folder arsip (archived) dan semua konten menjadi read-only bagi mahasiswa.
                            </p>
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

<!-- Modal Add Student (Redesigned) -->
<div id="modal-add" class="modal-backdrop">
    <div class="modal-box" style="max-width: 520px;">
        <div style="padding: 1.5rem 1.5rem 1rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h3 style="margin: 0 0 0.25rem 0; font-size: 1.2rem; font-weight: 700; color: var(--text-primary);">Enroll Mahasiswa</h3>
                <p style="margin: 0; font-size: 0.8rem; color: var(--text-muted);">Tambahkan mahasiswa dari prodi terkait ke kelas ini</p>
            </div>
            <button onclick="document.getElementById('modal-add').style.display = 'none'" style="background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; font-size: 1.1rem; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 1rem;">&times;</button>
        </div>
        <div style="padding: 1.5rem;">
            {{-- Filter Lintas Prodi Dinamis (Fakultas, Prodi, Angkatan) --}}
            <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem;">
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); display: block; margin-bottom: 0.25rem;">Fakultas:</label>
                    <select id="enroll-filter-fakultas" onchange="onFakultasChange()" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.875rem; background: white; outline: none; font-weight: 500; color: var(--text-primary);">
                        <option value="">-- Semua Fakultas --</option>
                        @foreach($allFakultas as $fak)
                            <option value="{{ $fak->id }}" {{ $selectedEnrollFakultasId == $fak->id ? 'selected' : '' }}>
                                {{ $fak->nama_fakultas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); display: block; margin-bottom: 0.25rem;">Program Studi:</label>
                        <select id="enroll-filter-prodi" onchange="onProdiChange()" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.875rem; background: white; outline: none; font-weight: 500; color: var(--text-primary);">
                            <option value="">-- Semua Prodi --</option>
                            @foreach($allProdis as $prod)
                                <option value="{{ $prod->id }}" data-fakultas="{{ $prod->id_fakultas }}" {{ $selectedEnrollProdiId == $prod->id ? 'selected' : '' }}>
                                    {{ $prod->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); display: block; margin-bottom: 0.25rem;">Angkatan:</label>
                        <select id="enroll-filter-angkatan" onchange="loadStudentsAjax()" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.875rem; background: white; outline: none; font-weight: 500; color: var(--text-primary);">
                            <option value="">-- Semua --</option>
                            @foreach($allAngkatans as $angk)
                                <option value="{{ $angk }}">{{ $angk }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <form action="{{ route('classes.enroll', $class) }}" method="POST" style="margin: 0;">
                @csrf
                {{-- Live Search Input --}}
                <div style="position: relative; margin-bottom: 1rem;">
                    <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;">🔍</span>
                    <input
                        type="text"
                        id="search-student"
                        placeholder="Cari NIM atau nama mahasiswa..."
                        oninput="filterStudentOptions()"
                        style="width: 100%; padding: 0.65rem 0.75rem 0.65rem 2.25rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.875rem; box-sizing: border-box;"
                        autocomplete="off"
                    >
                </div>
                
                {{-- Select All Checkbox --}}
                <div style="margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: space-between; padding: 0 0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin: 0;">
                        <input type="checkbox" id="select-all-students" onclick="toggleSelectAllStudents(this)" style="accent-color: var(--primary); width: 16px; height: 16px;">
                        Pilih Semua (yang tampil)
                    </label>
                    <span id="student-visible-count" style="font-size: 0.75rem; color: var(--text-muted);">{{ count($availableStudents) }} mahasiswa</span>
                </div>

                {{-- Student List --}}
                <div id="student-list" style="max-height: 280px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: var(--radius-md); margin-bottom: 1.25rem;">
                    @forelse($availableStudents as $student)
                    <label class="student-option" data-search="{{ strtolower($student->nim . ' ' . $student->nama_student) }}" data-angkatan="{{ $student->angkatan }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; cursor: pointer; border-bottom: 1px solid #f1f5f9; transition: background 0.15s; margin: 0;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                        <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" style="accent-color: var(--primary); width: 16px; height: 16px; flex-shrink: 0;" onchange="updateSelectAllState()">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0;">
                            {{ strtoupper(substr($student->nama_student, 0, 1)) }}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <strong style="display: block; font-size: 0.9rem; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $student->nama_student }}</strong>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">NIM: {{ $student->nim }} &bull; Angkatan {{ $student->angkatan ?? '-' }}</span>
                        </div>
                    </label>
                    @empty
                    <div id="no-student-result" style="padding: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">Tidak ada mahasiswa yang belum terdaftar.</div>
                    @endforelse
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-add').style.display = 'none'">Batal</button>
                    <button type="submit" class="btn">✅ Enroll Mahasiswa</button>
                </div>
            </form>
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

<!-- Modal Add Dosen (Redesigned) -->
<div id="modal-add-dosen" class="modal-backdrop">
    <div class="modal-box" style="max-width: 520px;">
        <div style="padding: 1.5rem 1.5rem 1rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h3 style="margin: 0 0 0.25rem 0; font-size: 1.2rem; font-weight: 700; color: var(--text-primary);">Tambah Dosen Pengampu</h3>
                <p style="margin: 0; font-size: 0.8rem; color: var(--text-muted);">Tambah dosen kedua, ketiga, atau Kaprodi untuk monitoring kelas</p>
            </div>
            <button onclick="document.getElementById('modal-add-dosen').style.display = 'none'" style="background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; font-size: 1.1rem; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 1rem;">&times;</button>
        </div>
        <div style="padding: 1.5rem;">
            @if($availableDosens->count() > 0)
            <form action="{{ route('classes.add_staff', $class) }}" method="POST">
                @csrf
                <input type="hidden" name="staff_type" value="dosen">
                
                {{-- Filter Lintas Prodi Dinamis (Fakultas, Prodi) untuk Dosen --}}
                <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); display: block; margin-bottom: 0.25rem;">Fakultas:</label>
                        <select id="dosen-filter-fakultas" onchange="onDosenFakultasChange()" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.875rem; background: white; outline: none; font-weight: 500; color: var(--text-primary);">
                            <option value="">-- Semua Fakultas --</option>
                            @foreach($allFakultas as $fak)
                                <option value="{{ $fak->id }}" {{ (isset($selectedEnrollFakultasId) && $selectedEnrollFakultasId == $fak->id) ? 'selected' : '' }}>
                                    {{ $fak->nama_fakultas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); display: block; margin-bottom: 0.25rem;">Program Studi:</label>
                        <select id="dosen-filter-prodi" onchange="filterDosenOptions()" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.875rem; background: white; outline: none; font-weight: 500; color: var(--text-primary);">
                            <option value="">-- Semua Prodi --</option>
                            @foreach($allProdis as $prod)
                                <option value="{{ $prod->id }}" data-fakultas="{{ $prod->id_fakultas }}" {{ (isset($selectedEnrollProdiId) && $selectedEnrollProdiId == $prod->id) ? 'selected' : '' }}>
                                    {{ $prod->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Live Search --}}
                <div style="position: relative; margin-bottom: 1rem;">
                    <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;">🔍</span>
                    <input
                        type="text"
                        id="search-dosen"
                        placeholder="Cari nama atau NIDN dosen..."
                        oninput="filterDosenOptions()"
                        style="width: 100%; padding: 0.65rem 0.75rem 0.65rem 2.25rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.875rem; box-sizing: border-box;"
                        autocomplete="off"
                    >
                </div>
                {{-- Dosen List --}}
                <div id="dosen-list" style="max-height: 280px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: var(--radius-md); margin-bottom: 1.25rem;">
                    @foreach($availableDosens as $avDosen)
                    <label class="dosen-option" data-search="{{ strtolower($avDosen->nama_dosen . ' ' . $avDosen->nidn) }}" data-fakultas="{{ optional(optional($avDosen->prodi)->fakultas)->id }}" data-prodi="{{ $avDosen->prodi_id }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; cursor: pointer; border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                        <input type="radio" name="dosen_id" value="{{ $avDosen->id }}" required style="accent-color: var(--primary); width: 16px; height: 16px; flex-shrink: 0;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem; flex-shrink: 0;">
                            {{ strtoupper(substr($avDosen->nama_dosen, 0, 1)) }}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <strong style="display: block; font-size: 0.9rem; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $avDosen->nama_dosen }}</strong>
                            <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.2rem; flex-wrap: wrap;">
                                <span style="font-size: 0.72rem; color: var(--text-muted);">NIDN: {{ $avDosen->nidn ?? '-' }}</span>
                                <span style="font-size: 0.7rem; background: #e0f2fe; color: #0369a1; padding: 0.1rem 0.4rem; border-radius: 9999px; font-weight: 600;">Dosen</span>
                            </div>
                        </div>
                    </label>
                    @endforeach
                    <div id="no-dosen-result" style="display: none; padding: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">Tidak ada dosen yang cocok.</div>
                </div>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0 0 1.25rem;">Dosen yang sudah terdaftar di kelas ini tidak ditampilkan.</p>
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-add-dosen').style.display = 'none'">Batal</button>
                    <button type="submit" class="btn">👨‍🏫 Tambahkan Dosen</button>
                </div>
            </form>
            @else
            <div style="text-align: center; padding: 2rem 1rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">👨‍🏫</div>
                <p style="margin: 0 0 0.5rem; font-weight: 600; color: var(--text-primary);">Semua Dosen Sudah Terdaftar</p>
                <p style="margin: 0 0 1.5rem; font-size: 0.85rem; color: var(--text-muted);">Tidak ada dosen lain yang tersedia untuk ditambahkan.</p>
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-add-dosen').style.display = 'none'">Tutup</button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Add BAAK -->
<div id="modal-add-baak" class="modal-backdrop">
    <div class="modal-box">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700;">Tambah Staff BAAK</h3>
            <button onclick="document.getElementById('modal-add-baak').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <div class="card-body" style="padding: 1.5rem;">
            @if($availableBaaks->count() > 0)
            <form action="{{ route('classes.add_staff', $class) }}" method="POST">
                @csrf
                <input type="hidden" name="staff_type" value="baak">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Pilih Staff BAAK <span style="color: red;">*</span></label>
                    <select name="user_id" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <option value="">-- Pilih Staff BAAK --</option>
                        @foreach($availableBaaks as $baak)
                            <option value="{{ $baak->id }}">{{ $baak->name }} ({{ $baak->email }})</option>
                        @endforeach
                    </select>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Staff BAAK yang sudah terdaftar di kelas ini tidak ditampilkan.</p>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-add-baak').style.display = 'none'">Batal</button>
                    <button type="submit" class="btn">Tambahkan Staff BAAK</button>
                </div>
            </form>
            @else
            <div style="text-align: center; padding: 1rem 0;">
                <p style="margin: 0; color: var(--text-muted);">Semua staff BAAK sudah terdaftar di kelas ini atau tidak ada staff BAAK yang tersedia.</p>
                <button type="button" class="btn btn-outline" style="margin-top: 1rem;" onclick="document.getElementById('modal-add-baak').style.display = 'none'">Tutup</button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Add Classwork Item -->
<!-- RPS Session Assessments data for JS OBE filtering -->
<script>
    // Mapping OBE assessments per sesi dari RPS
    (function() {
        var rawData = {!! json_encode($rpsSessionsWithAssessments->keyBy('session_number'), JSON_UNESCAPED_UNICODE) !!};
        // Normalize keys to string for consistent JS lookup
        var normalized = {};
        Object.keys(rawData).forEach(function(k) {
            normalized[String(k)] = rawData[k];
        });
        window._rpsSessionAssessments = normalized;
        @if($rpsSessionsWithAssessments->isEmpty())
        console.warn('[OBE] Tidak ada data RPS assessments ditemukan untuk kelas ini. Pastikan RPS sudah memiliki sesi dan penilaian.');
        @else
        console.info('[OBE] Data assessments dimuat: {{ $rpsSessionsWithAssessments->count() }} sesi dari RPS.');
        @endif
    })();

    // ── PENTING: Destroy bootstrap-select pada OBE selects ──────────────────────
    // Bootstrap-select mengkonversi <select> ke custom UI, sehingga update
    // innerHTML via JS tidak tercermin di UI-nya. Solusi: destroy selectpicker
    // pada elemen OBE agar tetap jadi native select yang bisa dikontrol JS.
    document.addEventListener('DOMContentLoaded', function() {
        var obeIds = [
            'assignment-obe-select', 'quiz-obe-select', 'edit-assignment-obe-select',
            'assignment-session-select', 'quiz-session-select', 'edit-assignment-session-select'
        ];
        obeIds.forEach(function(id) {
            var el = document.getElementById(id);
            if (!el) return;
            // Destroy bootstrap-select instance jika ada
            if (window.jQuery && typeof jQuery.fn.selectpicker === 'function') {
                try { $(el).selectpicker('destroy'); } catch(e) {}
            }
            // Pastikan elemen tidak memiliki class selectpicker agar tidak re-init
            el.classList.remove('selectpicker');
            // Reset style agar tampil normal
            el.style.display = '';
        });
    });
</script>
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
            <form id="form-materi" action="{{ route('classes.store_material', $class) }}" method="POST" enctype="multipart/form-data" class="classwork-form">
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
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Upload File (Bisa lebih dari 1)</label>
                        <input type="file" name="files[]" multiple accept=".pdf,.doc,.docx,.ppt,.pptx" onchange="displaySelectedFiles(this, 'selected-files-list')" style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.85rem;">
                        <div id="selected-files-list" style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--text-primary);"></div>
                        <span style="font-size: 0.7rem; color: var(--text-muted); display: block; margin-top: 0.25rem;">Max 2MB per file (PDF, Word, PPT)</span>
                    </div>
                    <div id="add-links-wrapper">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.85rem; font-weight: 600;">Link Eksternal (Opsional)</span>
                            <button type="button" onclick="addNewLinkField('add-links-container')" style="background: rgba(79, 70, 229, 0.1); border: none; color: #4f46e5; cursor: pointer; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 4px; display: inline-flex; align-items: center; gap: 0.25rem;">+ Tambah Link</button>
                        </div>
                        <div id="add-links-container" style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <input type="url" name="links[]" placeholder="https://youtube.com/ atau drive link" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        </div>
                    </div>
                </div>
                <div style="margin-bottom: 1.5rem; padding: 0.75rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: var(--radius-md);">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; cursor: pointer; color: #166534; font-weight: 600;">
                        <input type="checkbox" name="share_to_rps" value="1" style="accent-color: #16a34a; width: 16px; height: 16px;">
                        Bagikan sebagai Modul Resmi RPS (Akan tersedia untuk kelas lain yang mengampu mata kuliah ini)
                    </label>
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
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Sesi <span style="color: red;">*</span></label>
                        <select name="session_number" id="assignment-session-select" required onchange="onAssignmentSessionChange(this.value)"
                            style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                            <option value="">-- Pilih Sesi --</option>
                            @for($s = 1; $s <= 14; $s++)
                                <option value="{{ $s }}">Sesi {{ $s }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Judul Tugas <span style="color: red;">*</span></label>
                        <input type="text" name="title" id="assignment-title-input" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Instruksi Tugas <span style="color: red;">*</span></label>
                    <textarea name="instruction" id="assignment-instruction-input" rows="3" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Batas Pengumpulan <span style="color: red;">*</span></label>
                        <input type="datetime-local" name="deadline" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Penilaian OBE (RPS) <span style="color: red;">*</span>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400;"> — wajib, sesuai sesi</span>
                        </label>
                        <select name="rps_assessment_id" id="assignment-obe-select"
                            style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"
                            required
                            onchange="onAssessmentChange(this.value)">
                            <option value="">-- Pilih Sesi terlebih dahulu --</option>
                        </select>
                        <p style="margin: 0.35rem 0 0; font-size: 0.75rem; color: #dc2626; display: none;" id="obe-required-hint">⚠️ Pilih Sesi dulu agar daftar penilaian OBE muncul.</p>
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
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Sesi <span style="color: red;">*</span></label>
                        <select name="session_number" id="quiz-session-select" required onchange="onQuizSessionChange(this.value)"
                            style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                            <option value="">-- Pilih Sesi --</option>
                            @for($s = 1; $s <= 14; $s++)
                                <option value="{{ $s }}">Sesi {{ $s }}</option>
                            @endfor
                        </select>
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
                            Sangkutkan Penilaian OBE (RPS)
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400;"> — sesuai sesi</span>
                        </label>
                        <select name="rps_assessment_id" id="quiz-obe-select"
                            style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                            <option value="">-- Pilih Sesi terlebih dahulu --</option>
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

<!-- Modal Edit Material -->
<div id="modal-edit-material" class="modal-backdrop">
    <div class="modal-box" style="max-width: 600px;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700;">Edit Materi</h3>
            <button onclick="closeEditMaterialModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <div class="card-body" style="padding: 1.5rem;">
            <form id="form-edit-materi" method="POST" enctype="multipart/form-data" class="classwork-form">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Judul Materi <span style="color: red;">*</span></label>
                    <input type="text" id="edit-material-title" name="title" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Deskripsi / Ringkasan</label>
                    <textarea id="edit-material-desc" name="description" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">File Terlampir Saat Ini</label>
                        <div style="margin-bottom: 0.5rem; font-size: 0.8rem; background: #f8fafc; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--border-color);">
                            <div id="edit-current-files-container" style="display: flex; flex-direction: column; gap: 0.4rem;">
                                <!-- Populate via JavaScript -->
                            </div>
                        </div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; margin-top: 1rem;">Tambah File (Bisa lebih dari 1)</label>
                        <input type="file" name="files[]" multiple accept=".pdf,.doc,.docx,.ppt,.pptx" onchange="displaySelectedFiles(this, 'edit-selected-files-list')" style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.85rem;">
                        <div id="edit-selected-files-list" style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--text-primary);"></div>
                        <span style="font-size: 0.7rem; color: var(--text-muted); display: block; margin-top: 0.25rem;">Max 2MB per file (PDF, Word, PPT). Mengunggah file baru akan menambahkan file ke daftar materi.</span>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Link Eksternal Terlampir</label>
                        <div style="margin-bottom: 0.5rem; font-size: 0.8rem; background: #f8fafc; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--border-color);">
                            <div id="edit-current-links-container" style="display: flex; flex-direction: column; gap: 0.4rem;">
                                <!-- Populate via JavaScript -->
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; margin-top: 1rem;">
                            <span style="font-size: 0.85rem; font-weight: 600;">Tambah Link Baru</span>
                            <button type="button" onclick="addNewLinkField('edit-new-links-container')" style="background: rgba(79, 70, 229, 0.1); border: none; color: #4f46e5; cursor: pointer; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 4px; display: inline-flex; align-items: center; gap: 0.25rem;">+ Tambah Link</button>
                        </div>
                        <div id="edit-new-links-container" style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <!-- Dynamically added link inputs go here -->
                        </div>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-outline" onclick="closeEditMaterialModal()">Cancel</button>
                    <button type="submit" class="btn">Update Material</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Assignment (Sesi, Judul, Instruksi, Deadline, OBE) -->
<div id="modal-edit-assignment" class="modal-backdrop" style="display: none;">
    <div class="modal-box" style="max-width: 600px;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700;">✏️ Edit Tugas</h3>
            <button onclick="closeEditAssignmentModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <div class="card-body" style="padding: 1.5rem;">
            <form id="form-edit-assignment" method="POST" class="classwork-form">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Sesi (1-14) <span style="color: red;">*</span></label>
                        <select name="session_number" id="edit-assignment-session-select" required
                            onchange="onEditAssignmentSessionChange(this.value)"
                            style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                            @for($s = 1; $s <= 14; $s++)
                                <option value="{{ $s }}">Sesi {{ $s }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Judul Tugas <span style="color: red;">*</span></label>
                        <input type="text" name="title" id="edit-assignment-title" required
                            style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Instruksi Tugas <span style="color: red;">*</span></label>
                    <textarea name="instruction" id="edit-assignment-instruction" rows="4" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Batas Pengumpulan (Deadline) <span style="color: red;">*</span></label>
                        <input type="datetime-local" name="deadline" id="edit-assignment-deadline" required
                            style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Penilaian OBE (RPS) <span style="color: red;">*</span>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400;"> — wajib</span>
                        </label>
                        <select name="rps_assessment_id" id="edit-assignment-obe-select"
                            style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"
                            required>
                            <option value="">-- Pilih Sesi terlebih dahulu --</option>
                        </select>
                        <p style="margin: 0.35rem 0 0; font-size: 0.75rem; color: #dc2626; display: none;" id="edit-obe-required-hint">⚠️ Pilih Sesi dulu agar daftar penilaian OBE muncul.</p>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-outline" onclick="closeEditAssignmentModal()">Batal</button>
                    <button type="submit" class="btn">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Forum -->
<div id="modal-edit-forum" class="modal-backdrop" style="display: none;">
    <div class="modal-box" style="max-width: 560px;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700;">💬 Edit Forum Diskusi</h3>
            <button onclick="closeEditForumModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <div class="card-body" style="padding: 1.5rem;">
            <form id="form-edit-forum" method="POST" class="classwork-form">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Judul Forum <span style="color: red;">*</span></label>
                    <input type="text" name="title" id="edit-forum-title" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Topik Bahasan Forum</label>
                    <textarea name="description" id="edit-forum-desc" rows="4"
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-outline" onclick="closeEditForumModal()">Batal</button>
                    <button type="submit" class="btn">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const unenrollForms = document.querySelectorAll('.form-unenroll');
        unenrollForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Keluarkan Mahasiswa?',
                    html: 'Apakah Anda yakin ingin mengeluarkan mahasiswa ini dari kelas?<br><br><span style="color: #dc2626; font-size: 0.9em;"><strong>⚠️ Peringatan:</strong> Mahasiswa mungkin sudah menjalankan aktivitas perkuliahan. Jika dihapus, maka semua data aktivitas (seperti tugas yang sudah di-upload) juga akan ikut terhapus!</span>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Keluarkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
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
        // Reset type selector ke default (materi)
        var typeSelect = document.getElementById('classwork-type');
        if (typeSelect) typeSelect.value = 'materi';
        // Initialize form fields visibility
        toggleFormFields('materi');

        // Reset OBE selects ke state awal
        var obeAssign = document.getElementById('assignment-obe-select');
        if (obeAssign) {
            obeAssign.innerHTML = '<option value="">-- Pilih Sesi terlebih dahulu --</option>';
            _refreshSelectpicker(obeAssign);
        }
        var obeQuiz = document.getElementById('quiz-obe-select');
        if (obeQuiz) {
            obeQuiz.innerHTML = '<option value="">-- Pilih Sesi terlebih dahulu --</option>';
            _refreshSelectpicker(obeQuiz);
        }
        // Reset session selects
        var sessAssign = document.getElementById('assignment-session-select');
        if (sessAssign) {
            sessAssign.value = '';
        }
        var sessQuiz = document.getElementById('quiz-session-select');
        if (sessQuiz) {
            sessQuiz.value = '';
        }
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
            // Re-trigger OBE dropdown if session is already selected
            const sessionSel = document.getElementById('assignment-session-select');
            if (sessionSel && sessionSel.value) onAssignmentSessionChange(sessionSel.value);
        } else if (type === 'forum') {
            document.getElementById('form-forum').style.display = 'block';
        } else if (type === 'quiz') {
            document.getElementById('form-quiz').style.display = 'block';
            // Re-trigger OBE dropdown if session is already selected
            const sessionSel = document.getElementById('quiz-session-select');
            if (sessionSel && sessionSel.value) onQuizSessionChange(sessionSel.value);
        }
    }

    // ─── Helper: refresh selectpicker jika ada, untuk sync UI dengan native select ───
    function _refreshSelectpicker(selectEl) {
        if (window.jQuery && typeof jQuery.fn.selectpicker === 'function') {
            try { $(selectEl).selectpicker('refresh'); } catch(e) {}
        }
    }

    // ─── Session-based OBE filtering for Assignment form ─────────────────────────
    function onAssignmentSessionChange(sessionNumber) {
        const obeSelect = document.getElementById('assignment-obe-select');
        const obeHint = document.getElementById('obe-required-hint');
        if (!obeSelect) return;

        if (!sessionNumber) {
            obeSelect.innerHTML = '<option value="">-- Pilih Sesi terlebih dahulu --</option>';
            if (obeHint) obeHint.style.display = 'block';
            _refreshSelectpicker(obeSelect);
            onAssessmentChange('');
            return;
        }

        const data = window._rpsSessionAssessments || {};
        const key = String(sessionNumber);
        const sessionData = data[key];

        if (!sessionData || !sessionData.assessments || sessionData.assessments.length === 0) {
            // Sesi ini tidak punya assessment di RPS
            obeSelect.innerHTML = '<option value="">-- Tidak ada penilaian OBE di sesi ini --</option>';
            if (obeHint) { obeHint.textContent = '⚠️ Sesi ' + sessionNumber + ' belum memiliki rencana penilaian di RPS. Hubungi Kaprodi.'; obeHint.style.display = 'block'; }
            _refreshSelectpicker(obeSelect);
            onAssessmentChange('');
            return;
        }

        // Ada assessment — sembunyikan hint
        if (obeHint) obeHint.style.display = 'none';

        // Reset lalu isi opsi
        obeSelect.innerHTML = '<option value="">-- Pilih Penilaian OBE --</option>';

        sessionData.assessments.forEach(function(assessment) {
            const opt = document.createElement('option');
            opt.value = assessment.id;
            opt.textContent = assessment.label;
            opt.dataset.instruction = assessment.instruction || '';
            opt.dataset.assessmentType = assessment.assessment_type || '';
            obeSelect.appendChild(opt);
        });

        // Auto-select jika hanya 1 assessment
        if (sessionData.assessments.length === 1) {
            obeSelect.value = sessionData.assessments[0].id;
            onAssessmentChange(sessionData.assessments[0].id);
        } else {
            onAssessmentChange('');
        }

        // Refresh selectpicker jika masih aktif (fallback safety)
        _refreshSelectpicker(obeSelect);
    }

    function onQuizSessionChange(sessionNumber) {
        const obeSelect = document.getElementById('quiz-obe-select');
        if (!obeSelect) return;

        if (!sessionNumber) {
            obeSelect.innerHTML = '<option value="">-- Pilih Sesi terlebih dahulu --</option>';
            _refreshSelectpicker(obeSelect);
            return;
        }

        const data = window._rpsSessionAssessments || {};
        const key = String(sessionNumber);
        const sessionData = data[key];

        // Reset
        obeSelect.innerHTML = '<option value="">-- Tidak terhubung RPS (Bebas) --</option>';

        if (sessionData && sessionData.assessments && sessionData.assessments.length > 0) {
            sessionData.assessments.forEach(function(assessment) {
                const opt = document.createElement('option');
                opt.value = assessment.id;
                opt.textContent = assessment.label;
                opt.dataset.instruction = assessment.instruction || '';
                opt.dataset.assessmentType = assessment.assessment_type || '';
                obeSelect.appendChild(opt);
            });
        }

        // Refresh selectpicker jika masih aktif (fallback safety)
        _refreshSelectpicker(obeSelect);
    }

    function onAssessmentChange(assessmentId) {
        const obeSelect = document.getElementById('assignment-obe-select');
        const instrField = document.getElementById('assignment-instruction-input');
        
        if (!obeSelect || !instrField) return;

        if (!assessmentId) {
            instrField.value = '';
            return;
        }

        const selectedOpt = obeSelect.querySelector('option[value="' + assessmentId + '"]');
        if (selectedOpt && selectedOpt.dataset.instruction) {
            instrField.value = selectedOpt.dataset.instruction;
        } else {
            instrField.value = '';
        }
    }

    // ─── Session-based OBE filtering for Edit Assignment form ────────────────────
    function onEditAssignmentSessionChange(sessionNumber, selectedObeId) {
        const obeSelect = document.getElementById('edit-assignment-obe-select');
        const obeHint = document.getElementById('edit-obe-required-hint');
        if (!obeSelect) return;

        if (!sessionNumber) {
            obeSelect.innerHTML = '<option value="">-- Pilih Sesi terlebih dahulu --</option>';
            if (obeHint) obeHint.style.display = 'block';
            _refreshSelectpicker(obeSelect);
            return;
        }

        const data = window._rpsSessionAssessments || {};
        const key = String(sessionNumber);
        const sessionData = data[key];

        if (!sessionData || !sessionData.assessments || sessionData.assessments.length === 0) {
            obeSelect.innerHTML = '<option value="">-- Tidak ada penilaian OBE di sesi ini --</option>';
            if (obeHint) {
                obeHint.textContent = '⚠️ Sesi ' + sessionNumber + ' belum memiliki rencana penilaian di RPS. Hubungi Kaprodi.';
                obeHint.style.display = 'block';
            }
            _refreshSelectpicker(obeSelect);
            return;
        }

        if (obeHint) obeHint.style.display = 'none';

        obeSelect.innerHTML = '<option value="">-- Pilih Penilaian OBE --</option>';

        sessionData.assessments.forEach(function(assessment) {
            const opt = document.createElement('option');
            opt.value = assessment.id;
            opt.textContent = assessment.label;
            opt.dataset.instruction = assessment.instruction || '';
            opt.dataset.assessmentType = assessment.assessment_type || '';
            if (selectedObeId && String(assessment.id) === String(selectedObeId)) {
                opt.selected = true;
            }
            obeSelect.appendChild(opt);
        });

        if (selectedObeId) {
            obeSelect.value = selectedObeId;
        } else if (sessionData.assessments.length === 1) {
            obeSelect.value = sessionData.assessments[0].id;
        }

        _refreshSelectpicker(obeSelect);
    }

    // ─── Edit Assignment Modal ────────────────────────────────────────────────────
    function openEditAssignmentModal(button) {
        let assignmentId, title, instruction, deadline, sessionNum, obeId;
        if (typeof button === 'object' && button !== null && button.getAttribute) {
            assignmentId = button.getAttribute('data-id');
            title = button.getAttribute('data-title') || '';
            instruction = button.getAttribute('data-instruction') || '';
            deadline = button.getAttribute('data-deadline') || '';
            sessionNum = button.getAttribute('data-session') || '';
            obeId = button.getAttribute('data-obe-id') || '';
        } else {
            assignmentId = arguments[0];
            title = arguments[1] || '';
            instruction = arguments[2] || '';
            deadline = arguments[3] || '';
            sessionNum = arguments[4] || '';
            obeId = arguments[5] || '';
        }

        const url = "{{ url('/classes/' . $class->id . '/assignment') }}/" + assignmentId;
        const form = document.getElementById('form-edit-assignment');
        if (form) form.action = url;

        const titleEl = document.getElementById('edit-assignment-title');
        const instructionEl = document.getElementById('edit-assignment-instruction');
        const deadlineEl = document.getElementById('edit-assignment-deadline');
        const sessionEl = document.getElementById('edit-assignment-session-select');

        if (titleEl) titleEl.value = title;
        if (instructionEl) instructionEl.value = instruction;
        if (deadlineEl) deadlineEl.value = deadline;

        if (sessionEl && sessionNum) {
            sessionEl.value = sessionNum;
        }

        onEditAssignmentSessionChange(sessionNum, obeId);

        const modal = document.getElementById('modal-edit-assignment');
        if (modal) modal.style.display = 'flex';
    }

    function closeEditAssignmentModal() {
        const modal = document.getElementById('modal-edit-assignment');
        if (modal) modal.style.display = 'none';
    }

    // ─── Edit Forum Modal ─────────────────────────────────────────────────────────
    function openEditForumModal(button) {
        const forumId = button.getAttribute('data-id');
        const title = button.getAttribute('data-title') || '';
        const desc = button.getAttribute('data-desc') || '';

        const url = "{{ url('/classes/' . $class->id . '/forum') }}/" + forumId;
        const form = document.getElementById('form-edit-forum');
        if (form) form.action = url;

        const titleEl = document.getElementById('edit-forum-title');
        const descEl = document.getElementById('edit-forum-desc');

        if (titleEl) titleEl.value = title;
        if (descEl) descEl.value = desc;

        const modal = document.getElementById('modal-edit-forum');
        if (modal) modal.style.display = 'flex';
    }

    function closeEditForumModal() {
        const modal = document.getElementById('modal-edit-forum');
        if (modal) modal.style.display = 'none';
    }


    // 4. Live search: Mahasiswa
    function filterStudentOptions() {
        const queryInput = document.getElementById('search-student');
        const angkatanInput = document.getElementById('filter-angkatan');
        const q = queryInput ? queryInput.value.toLowerCase().trim() : '';
        const angkatan = angkatanInput ? angkatanInput.value : '';
        
        const items = document.querySelectorAll('.student-option');
        let visibleCount = 0;
        items.forEach(item => {
            const text = item.getAttribute('data-search') || '';
            const itemAngkatan = item.getAttribute('data-angkatan') || '';
            const matchSearch = !q || text.includes(q);
            const matchAngkatan = !angkatan || itemAngkatan === angkatan;
            
            if (matchSearch && matchAngkatan) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        const noResult = document.getElementById('no-student-result');
        if (noResult) noResult.style.display = visibleCount === 0 ? 'block' : 'none';
        
        const countDisplay = document.getElementById('student-visible-count');
        if (countDisplay) countDisplay.innerText = visibleCount + ' mahasiswa';

        // Uncheck "Select All" when filtering
        const selectAllCheckbox = document.getElementById('select-all-students');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
        }
        updateSelectAllState();
    }

    function toggleSelectAllStudents(checkbox) {
        const isChecked = checkbox.checked;
        const items = document.querySelectorAll('.student-option');
        items.forEach(item => {
            if (item.style.display !== 'none') {
                const cb = item.querySelector('input[type="checkbox"]');
                if (cb) cb.checked = isChecked;
            }
        });
    }

    function updateSelectAllState() {
        const items = document.querySelectorAll('.student-option');
        let allVisibleChecked = true;
        let hasVisible = false;

        items.forEach(item => {
            if (item.style.display !== 'none') {
                hasVisible = true;
                const cb = item.querySelector('input[type="checkbox"]');
                if (cb && !cb.checked) {
                    allVisibleChecked = false;
                }
            }
        });

        const selectAllCheckbox = document.getElementById('select-all-students');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = hasVisible && allVisibleChecked;
        }
    }

    // 5. Live search: Dosen
    function filterDosenOptions() {
        const queryInput = document.getElementById('search-dosen');
        const q = queryInput ? queryInput.value.toLowerCase().trim() : '';
        const fakultasSelect = document.getElementById('dosen-filter-fakultas');
        const prodiSelect = document.getElementById('dosen-filter-prodi');
        const fakultasId = fakultasSelect ? fakultasSelect.value : '';
        const prodiId = prodiSelect ? prodiSelect.value : '';

        const items = document.querySelectorAll('.dosen-option');
        let visibleCount = 0;
        items.forEach(item => {
            const text = item.getAttribute('data-search') || '';
            const itemFakultas = item.getAttribute('data-fakultas') || '';
            const itemProdi = item.getAttribute('data-prodi') || '';
            
            const matchSearch = !q || text.includes(q);
            const matchFakultas = !fakultasId || itemFakultas === fakultasId;
            const matchProdi = !prodiId || itemProdi === prodiId;

            const match = matchSearch && matchFakultas && matchProdi;
            item.style.display = match ? 'flex' : 'none';
            if (match) visibleCount++;
        });
        const noResult = document.getElementById('no-dosen-result');
        if (noResult) noResult.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    // 6. Click-outside to close redesigned modals
    ['modal-add', 'modal-add-dosen', 'modal-add-baak', 'modal-edit-material', 'modal-edit-assignment', 'modal-edit-forum'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('click', function(e) {
                if (e.target === el) {
                    el.style.display = 'none';
                    // Clear search inputs when closing
                    const searchStudent = document.getElementById('search-student');
                    const searchDosen = document.getElementById('search-dosen');
                    const filterAngkatan = document.getElementById('filter-angkatan');
                    if (searchStudent) { searchStudent.value = ''; }
                    if (filterAngkatan) { filterAngkatan.value = ''; }
                    if (searchStudent || filterAngkatan) { filterStudentOptions(); }
                    if (searchDosen) { searchDosen.value = ''; filterDosenOptions(); }
                }
            });
        }
    });

    // 7. Display Selected Files
    function displaySelectedFiles(input, listId) {
        const list = document.getElementById(listId);
        list.innerHTML = '';
        if (input.files.length > 0) {
            let html = '<ul style="margin: 0; padding-left: 1.25rem;">';
            for (let i = 0; i < input.files.length; i++) {
                html += '<li>' + input.files[i].name + '</li>';
            }
            html += '</ul>';
            list.innerHTML = html;
        }
    }

    // 8. Edit Material Modal
    function openEditMaterialModal(button) {
        const materialId = button.getAttribute('data-id');
        const title = button.getAttribute('data-title') || '';
        const desc = button.getAttribute('data-desc') || '';
        const filesJson = button.getAttribute('data-files') || '[]';
        const pathsJson = button.getAttribute('data-paths') || '[]';
        const linksJson = button.getAttribute('data-links') || '[]';

        document.getElementById('edit-material-title').value = title;
        document.getElementById('edit-material-desc').value = desc;
        
        const files = JSON.parse(filesJson);
        const paths = JSON.parse(pathsJson);
        const container = document.getElementById('edit-current-files-container');
        container.innerHTML = '';
        
        // Remove any previously added hidden inputs for deleted files
        const form = document.getElementById('form-edit-materi');
        form.querySelectorAll('.deleted-file-input').forEach(el => el.remove());

        if (files.length > 0) {
            files.forEach((name, index) => {
                const path = paths[index];
                const fileDiv = document.createElement('div');
                fileDiv.className = 'edit-file-item';
                fileDiv.style = 'display: flex; justify-content: space-between; align-items: center; background: white; padding: 0.35rem 0.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 0.25rem;';
                fileDiv.innerHTML = `
                    <span style="font-size: 0.8rem; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 80%;">📄 ${name}</span>
                    <button type="button" onclick="deleteExistingFile(this, '${path}', '${name}')" style="background: none; border: none; color: #dc2626; cursor: pointer; font-size: 0.9rem; padding: 0;">🗑️</button>
                `;
                container.appendChild(fileDiv);
            });
        } else {
            container.innerHTML = '<span style="color: var(--text-muted); font-style: italic;">Tidak ada file terlampir</span>';
        }
        
        document.getElementById('edit-selected-files-list').innerHTML = ''; // Reset new file selection text
        
        // Populate current links
        const links = JSON.parse(linksJson);
        const linksContainer = document.getElementById('edit-current-links-container');
        linksContainer.innerHTML = '';
        
        // Remove any previously added hidden inputs for deleted links
        form.querySelectorAll('.deleted-link-input').forEach(el => el.remove());
        // Reset new link fields
        document.getElementById('edit-new-links-container').innerHTML = '';

        if (links.length > 0) {
            links.forEach((linkUrl, index) => {
                const linkDiv = document.createElement('div');
                linkDiv.className = 'edit-link-item';
                linkDiv.style = 'display: flex; justify-content: space-between; align-items: center; background: white; padding: 0.35rem 0.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 0.25rem;';
                linkDiv.innerHTML = `
                    <span style="font-size: 0.8rem; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 80%;"><a href="${linkUrl}" target="_blank" style="color: var(--primary); text-decoration: none;">🔗 ${linkUrl}</a></span>
                    <button type="button" onclick="deleteExistingLink(this, '${linkUrl}')" style="background: none; border: none; color: #dc2626; cursor: pointer; font-size: 0.9rem; padding: 0;">🗑️</button>
                `;
                linksContainer.appendChild(linkDiv);
            });
        } else {
            linksContainer.innerHTML = '<span style="color: var(--text-muted); font-style: italic;">Tidak ada link terlampir</span>';
        }

        form.action = "{{ url('/classes/' . $class->id . '/material') }}/" + materialId;
        
        document.getElementById('modal-edit-material').style.display = 'flex';
    }
    
    function deleteExistingFile(button, path, name) {
        Swal.fire({
            title: 'Hapus File?',
            text: `Apakah Anda yakin ingin menghapus file "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('form-edit-materi');
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.className = 'deleted-file-input';
                hiddenInput.name = 'deleted_files[]';
                hiddenInput.value = path;
                form.appendChild(hiddenInput);

                const item = button.closest('.edit-file-item');
                if (item) item.remove();

                const container = document.getElementById('edit-current-files-container');
                if (container.children.length === 0) {
                    container.innerHTML = '<span style="color: var(--text-muted); font-style: italic;">Tidak ada file terlampir</span>';
                }
            }
        });
    }

    function addNewLinkField(containerId) {
        const container = document.getElementById(containerId);
        const div = document.createElement('div');
        div.style = 'display: flex; gap: 0.5rem; align-items: center; margin-top: 0.25rem;';
        div.innerHTML = `
            <input type="url" name="links[]" placeholder="https://youtube.com/ atau drive link" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
            <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #dc2626; cursor: pointer; font-size: 1.1rem; padding: 0;">🗑️</button>
        `;
        container.appendChild(div);
    }

    function deleteExistingLink(button, url) {
        Swal.fire({
            title: 'Hapus Link?',
            text: `Apakah Anda yakin ingin menghapus link "${url}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('form-edit-materi');
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.className = 'deleted-link-input';
                hiddenInput.name = 'deleted_links[]';
                hiddenInput.value = url;
                form.appendChild(hiddenInput);

                const item = button.closest('.edit-link-item');
                if (item) item.remove();

                const container = document.getElementById('edit-current-links-container');
                if (container.children.length === 0) {
                    container.innerHTML = '<span style="color: var(--text-muted); font-style: italic;">Tidak ada link terlampir</span>';
                }
            }
        });
    }
    
    function closeEditMaterialModal() {
        document.getElementById('modal-edit-material').style.display = 'none';
    }

    let allProdiOptions = [];
    let allDosenProdiOptions = [];
    
    document.addEventListener('DOMContentLoaded', function() {
        const prodiSelect = document.getElementById('enroll-filter-prodi');
        if (prodiSelect) {
            allProdiOptions = Array.from(prodiSelect.querySelectorAll('option'));
            filterProdis();
        }

        // Auto-open enroll modal and switch to people tab if open_enroll_modal parameter is present
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('open_enroll_modal') === '1') {
            const modal = document.getElementById('modal-add');
            if (modal) modal.style.display = 'flex';
            
            // Switch active tab to tab-people
            document.querySelectorAll('.tab-trigger').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            const peopleTabTrigger = document.querySelector('.tab-trigger[data-tab="tab-people"]');
            if (peopleTabTrigger) peopleTabTrigger.classList.add('active');
            
            const peopleTabContent = document.getElementById('tab-people');
            if (peopleTabContent) peopleTabContent.classList.add('active');
        }

        // Initialize Dosen Filters
        const dosenProdiSelect = document.getElementById('dosen-filter-prodi');
        if (dosenProdiSelect) {
            allDosenProdiOptions = Array.from(dosenProdiSelect.querySelectorAll('option'));
            filterDosenProdis();
            // Call filterDosenOptions to apply initial filters (default active prodi)
            filterDosenOptions();
        }
    });

    function filterDosenProdis() {
        const fakultasId = document.getElementById('dosen-filter-fakultas').value;
        const prodiSelect = document.getElementById('dosen-filter-prodi');
        if (!prodiSelect) return;
        
        const currentValue = prodiSelect.value;
        prodiSelect.innerHTML = '';
        
        allDosenProdiOptions.forEach(opt => {
            const optFakultasId = opt.dataset.fakultas;
            if (opt.value === "" || !fakultasId || optFakultasId === fakultasId) {
                prodiSelect.appendChild(opt.cloneNode(true));
            }
        });
        
        const optionExists = Array.from(prodiSelect.options).some(opt => opt.value === currentValue);
        if (optionExists) {
            prodiSelect.value = currentValue;
        } else {
            prodiSelect.value = "";
        }
    }

    function onDosenFakultasChange() {
        filterDosenProdis();
        filterDosenOptions();
    }

    function filterProdis() {
        const fakultasId = document.getElementById('enroll-filter-fakultas').value;
        const prodiSelect = document.getElementById('enroll-filter-prodi');
        if (!prodiSelect) return;
        
        const currentValue = prodiSelect.value;
        
        // Clear options
        prodiSelect.innerHTML = '';
        
        allProdiOptions.forEach(opt => {
            const optFakultasId = opt.dataset.fakultas;
            if (opt.value === "" || !fakultasId || optFakultasId === fakultasId) {
                prodiSelect.appendChild(opt.cloneNode(true));
            }
        });
        
        // Preserve selected value if it's still an option, otherwise select empty
        const optionExists = Array.from(prodiSelect.options).some(opt => opt.value === currentValue);
        if (optionExists) {
            prodiSelect.value = currentValue;
        } else {
            prodiSelect.value = "";
        }
    }

    function onFakultasChange() {
        filterProdis();
        loadStudentsAjax();
    }

    function onProdiChange() {
        loadStudentsAjax();
    }

    function loadStudentsAjax() {
        const fakultasId = document.getElementById('enroll-filter-fakultas').value;
        const prodiId = document.getElementById('enroll-filter-prodi').value;
        const angkatan = document.getElementById('enroll-filter-angkatan').value;
        const studentListContainer = document.getElementById('student-list');
        const selectAllCheckbox = document.getElementById('select-all-students');
        const visibleCountSpan = document.getElementById('student-visible-count');

        if (!studentListContainer) return;

        // Show loading spinner
        studentListContainer.innerHTML = `
            <div style="padding: 2.5rem; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                <span style="display: inline-block; width: 1.5rem; height: 1.5rem; border: 2px solid var(--border-color); border-top-color: var(--primary); border-radius: 50%; animation: spin 1s linear infinite; margin-right: 0.5rem; vertical-align: middle;"></span>
                Memuat data mahasiswa...
            </div>
            <style>
                @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            </style>
        `;
        
        if (selectAllCheckbox) selectAllCheckbox.checked = false;

        const url = "{{ url('/classes/' . $class->id . '/available-students') }}?fakultas_id=" + fakultasId + "&prodi_id=" + prodiId + "&angkatan=" + angkatan;
        
        fetch(url)
            .then(response => response.json())
            .then(students => {
                studentListContainer.innerHTML = '';
                
                if (students.length === 0) {
                    studentListContainer.innerHTML = '<div id="no-student-result" style="padding: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">Tidak ada mahasiswa yang belum terdaftar.</div>';
                    if (visibleCountSpan) visibleCountSpan.textContent = '0 mahasiswa';
                    return;
                }

                if (visibleCountSpan) visibleCountSpan.textContent = `${students.length} mahasiswa`;

                students.forEach(student => {
                    const label = document.createElement('label');
                    label.className = 'student-option';
                    label.dataset.search = `${student.nim} ${student.nama_student}`.toLowerCase();
                    label.dataset.angkatan = student.angkatan;
                    label.style.display = 'flex';
                    label.style.alignItems = 'center';
                    label.style.gap = '0.75rem';
                    label.style.padding = '0.75rem 1rem';
                    label.style.cursor = 'pointer';
                    label.style.borderBottom = '1px solid #f1f5f9';
                    label.style.transition = 'background 0.15s';
                    label.style.margin = '0';
                    
                    label.onmouseover = () => { label.style.background = '#f8fafc'; };
                    label.onmouseout = () => { label.style.background = 'white'; };

                    const initials = student.nama_student.substring(0, 1).toUpperCase();

                    label.innerHTML = `
                        <input type="checkbox" name="student_ids[]" value="${student.id}" style="accent-color: var(--primary); width: 16px; height: 16px; flex-shrink: 0;" onchange="updateSelectAllState()">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0;">
                            ${initials}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <strong style="display: block; font-size: 0.9rem; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${student.nama_student}</strong>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">NIM: ${student.nim} &bull; Angkatan ${student.angkatan || '-'}</span>
                        </div>
                    `;
                    studentListContainer.appendChild(label);
                });
            })
            .catch(err => {
                console.error(err);
                studentListContainer.innerHTML = '<div style="padding: 1.5rem; text-align: center; color: #ef4444; font-size: 0.875rem;">Gagal memuat data mahasiswa. Silakan coba lagi.</div>';
            });
    }

    function openSessionGradeBreakdown(studentName, sessionNum, details, avgScore) {
        let rowsHtml = '';
        details.forEach((item, index) => {
            let statusBadge = (item.status === 'Graded' || item.status === 'Lulus / Graded')
                ? `<span style="background: #dcfce7; color: #15803d; padding: 3px 9px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Graded</span>`
                : (item.status === 'Belum Dikumpulkan' 
                    ? `<span style="background: #f1f5f9; color: #64748b; padding: 3px 9px; border-radius: 4px; font-size: 0.75rem;">${item.status}</span>`
                    : `<span style="background: #fef3c7; color: #b45309; padding: 3px 9px; border-radius: 4px; font-size: 0.75rem;">${item.status}</span>`);

            rowsHtml += `
                <tr>
                    <td style="padding: 0.65rem 0.5rem; border-bottom: 1px solid #e2e8f0; text-align: center; color: #64748b;">${index + 1}</td>
                    <td style="padding: 0.65rem 0.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 600; text-align: left; color: #1e293b;">${item.title}</td>
                    <td style="padding: 0.65rem 0.5rem; border-bottom: 1px solid #e2e8f0; text-align: center;">${statusBadge}</td>
                    <td style="padding: 0.65rem 0.5rem; border-bottom: 1px solid #e2e8f0; text-align: center; font-weight: 700; font-size: 0.95rem; color: #0f172a;">${item.score}</td>
                </tr>
            `;
        });

        let avgHtml = (avgScore !== null && avgScore !== undefined)
            ? `<div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #eef2ff; border-radius: 8px; border: 1px solid #c7d2fe; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600; color: #3730a3; font-size: 0.85rem;">📊 Rata-Rata Nilai Sesi ${sessionNum}:</span>
                <span style="font-size: 1.15rem; font-weight: 800; color: #4338ca; background: white; padding: 0.25rem 0.75rem; border-radius: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">${avgScore}</span>
               </div>`
            : '';

        Swal.fire({
            title: `<div style="font-size: 1.1rem; font-weight: 700; color: #0f172a;">Rincian Tugas Sesi ${sessionNum}</div>`,
            html: `
                <div style="text-align: left; margin-bottom: 0.85rem; font-size: 0.85rem; color: #64748b; background: #f8fafc; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                    Mahasiswa: <strong style="color: #0f172a;">${studentName}</strong>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <tr style="background: #f1f5f9; color: #334155;">
                            <th style="padding: 0.5rem; border-bottom: 2px solid #cbd5e1; text-align: center; width: 35px;">#</th>
                            <th style="padding: 0.5rem; border-bottom: 2px solid #cbd5e1; text-align: left;">Nama Tugas</th>
                            <th style="padding: 0.5rem; border-bottom: 2px solid #cbd5e1; text-align: center;">Status</th>
                            <th style="padding: 0.5rem; border-bottom: 2px solid #cbd5e1; text-align: center;">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rowsHtml}
                    </tbody>
                </table>
                ${avgHtml}
            `,
            showCloseButton: true,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#4f46e5',
            width: '560px'
        });
    }
</script>
@endsection
