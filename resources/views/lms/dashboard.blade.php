@extends('layouts.lms')

@section('header_title', Auth::user()->hasRole(['admin','rektor','dekan']) ? __('Pilih Program Studi') : (__('Dashboard') . ' - ' . ucfirst($data['view_type'] ?? 'user')))

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════════
     ADMIN / REKTOR / DEKAN → Prodi Picker
═══════════════════════════════════════════════════════════════════════════════ --}}
@if(Auth::user()->hasRole(['admin', 'rektor', 'dekan']))

<style>
    .prodi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
        gap: 1.25rem;
        margin-top: 0.5rem;
    }
    .prodi-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        padding: 1.4rem 1.5rem 1.25rem;
        border-top: 4px solid var(--primary);
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        transition: transform 0.18s, box-shadow 0.18s;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }
    .prodi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 28px rgba(79,70,229,0.13);
    }
    .prodi-card-badge {
        display: inline-block;
        background: rgba(79,70,229,0.08);
        color: var(--primary);
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        padding: 2px 8px;
        margin-bottom: 0.4rem;
        text-transform: uppercase;
    }
    .prodi-card h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
        line-height: 1.35;
    }
    .prodi-card .prodi-meta {
        font-size: 0.8rem;
        color: #6b7280;
        margin: 0;
    }
    .prodi-card .prodi-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 0.85rem;
        padding-top: 0.85rem;
        border-top: 1px solid #f3f4f6;
    }
    .prodi-action-btn {
        flex: 1;
        text-align: center;
        padding: 0.4rem 0.6rem;
        border-radius: 7px;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.15s;
        white-space: nowrap;
    }
    .prodi-action-btn.btn-primary { background: var(--primary); color: #fff; }
    .prodi-action-btn.btn-primary:hover { background: #4338ca; }
    .prodi-action-btn.btn-outline { background: #f3f4f6; color: #374151; }
    .prodi-action-btn.btn-outline:hover { background: #e5e7eb; }
    .prodi-action-btn.btn-archive { background: #fff7ed; color: #c2410c; }
    .prodi-action-btn.btn-archive:hover { background: #ffedd5; }
</style>

<div style="margin-bottom: 1.5rem;">
    <h2 style="margin: 0 0 0.25rem; font-size: 1.35rem; font-weight: 700; color: #111827;">{{ __('Program Studi') }}</h2>
    <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">{{ __('Pilih program studi untuk mengelola kelas, dosen, dan mahasiswa.') }}</p>
</div>

@if($prodis->count() === 0)
    <div style="text-align:center; padding: 3rem; color: #9ca3af;">
        <p style="font-size: 2rem;">🏫</p>
        <p>{{ __('Belum ada data Program Studi. Tambahkan melalui') }} <a href="{{ route('prodi.index') }}">{{ __('halaman Prodi') }}</a>.</p>
    </div>
@else
<div class="prodi-grid">
    @foreach($prodis as $prodi)
    <div class="prodi-card">
        <span class="prodi-card-badge">{{ $prodi->kode_prodi ?? 'PRD' }}</span>
        <h3>{{ $prodi->nama_prodi }}</h3>
        <p class="prodi-meta">
            📍 {{ $prodi->fakultas->nama_fakultas ?? '-' }}<br>
            👤 Kaprodi: {!! $prodi->kaprodi->name ?? '<em>' . __('Belum ditentukan') . '</em>' !!}
        </p>
        <div class="prodi-actions">
            <a href="{{ route('classes.index', ['prodi_id' => $prodi->id]) }}" class="prodi-action-btn btn-primary">📚 {{ __('Kelas') }}</a>
            <a href="{{ route('classes.archived', ['prodi_id' => $prodi->id]) }}" class="prodi-action-btn btn-archive">🗃 {{ __('Arsip') }}</a>
            <a href="{{ route('dosen.index', ['prodi_id' => $prodi->id]) }}" class="prodi-action-btn btn-outline">👨‍🏫 {{ __('Dosen') }}</a>
            <a href="{{ route('mahasiswa.index', ['prodi_id' => $prodi->id]) }}" class="prodi-action-btn btn-outline">🎓 {{ __('Mhs') }}</a>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Link ke OBE Dashboard --}}
<div style="margin-top: 2rem; background: #eff6ff; border-left: 4px solid #3b82f6; padding: 1rem 1.5rem; border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h4 style="margin: 0 0 0.2rem; color: #1e3a8a;">{{ __('Sistem OBE') }}</h4>
        <p style="margin: 0; font-size: 0.85rem; color: #3b82f6;">{{ __('Kelola kurikulum, PLO, RPS, dan analitik OBE.') }}</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn" style="background:#3b82f6; white-space:nowrap;">{!! __('Masuk ke OBE &rarr;') !!}</a>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     DOSEN / MAHASISWA → Personal Dashboard
═══════════════════════════════════════════════════════════════════════════════ --}}
@else

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 4px solid var(--primary);">
        <h3 style="margin: 0 0 0.5rem 0; font-size: 0.875rem; color: var(--text-muted); text-transform: uppercase;">{{ __('Total Classes') }}</h3>
        <p style="margin: 0; font-size: 2rem; font-weight: 700; color: var(--text-main);">{{ $data['total_classes'] }}</p>
    </div>
    <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 4px solid #f59e0b;">
        <h3 style="margin: 0 0 0.5rem 0; font-size: 0.875rem; color: var(--text-muted); text-transform: uppercase;">{{ __('Pending Assignments') }}</h3>
        <p style="margin: 0; font-size: 2rem; font-weight: 700; color: var(--text-main);">{{ $data['total_assignments'] }}</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <div class="card">
        <div class="card-header">
            {{ ($data['view_type'] ?? '') == 'dosen' ? __('Classes I Teach') : __('My Enrolled Classes') }}
        </div>
        <div class="card-body" style="padding: 0;">
            @if(count($data['classes']) > 0)
                <div style="display: grid; grid-template-columns: 1fr; gap: 1px; background: var(--border);">
                    @foreach($data['classes'] as $class)
                        <div style="background: white; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h4 style="margin: 0 0 0.25rem 0; color: var(--primary);">{{ $class->nama_kelas ?? __('Class Name') }}</h4>
                                <p style="margin: 0; font-size: 0.875rem; color: var(--text-muted);">
                                    {{ $class->subject->nama_subject ?? __('Subject') }} ({{ $class->subject->kode_subject ?? '' }})<br>
                                    {{ __('Semester') }}: {{ $class->semester ?? '-' }} {{ $class->tahun_akademik ?? '-' }}
                                </p>
                            </div>
                            <a href="{{ route('classes.show', $class) }}" class="btn">{{ __('Masuk Kelas') }}</a>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
                    <p>📚</p>
                    <p>{{ __('No active classes found.') }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="background-color: #fffbeb;">{{ __('Upcoming Assignments') }}</div>
        <div class="card-body">
            @if(count($data['assignments']) > 0)
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($data['assignments'] as $assignment)
                        <div style="border-left: 3px solid #f59e0b; padding-left: 1rem;">
                            <h4 style="margin: 0 0 0.25rem 0; font-size: 0.875rem;">{{ $assignment->title }}</h4>
                            <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted);">
                                {{ __('Due:') }} {{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y, H:i') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; color: var(--text-muted); padding: 1rem 0;">
                    <p>{{ __('No upcoming assignments.') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endif

@endsection
