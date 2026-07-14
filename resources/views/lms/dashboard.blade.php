@extends('layouts.lms')

@section('header_title', Auth::user()->hasRole(['admin','rektor','dekan']) ? __('Pilih Program Studi') : (__('Dashboard') . ' - ' . ucfirst($data['view_type'] ?? 'user')))

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════════
     ADMIN / REKTOR / DEKAN → Prodi Picker
═══════════════════════════════════════════════════════════════════════════════ --}}
@if(Auth::user()->hasRole(['admin', 'rektor', 'dekan']))

<div class="row page-titles mx-0">
    <div class="col-sm-12 p-md-0">
        <div class="welcome-text">
            <h4>{{ __('Program Studi') }}</h4>
            <p class="mb-0 text-muted">{{ __('Pilih program studi untuk mengelola kelas, dosen, dan mahasiswa.') }}</p>
        </div>
    </div>
</div>

@if($prodis->count() === 0)
    <div class="row">
        <div class="col-12">
            <div class="card text-center p-5">
                <div class="card-body">
                    <p style="font-size: 2.5rem; margin-bottom: 1rem;">🏫</p>
                    <h4 class="text-muted">{{ __('Belum ada data Program Studi.') }}</h4>
                    <a href="{{ route('prodi.index') }}" class="btn btn-primary btn-sm mt-3">{{ __('Tambahkan melalui halaman Prodi') }}</a>
                </div>
            </div>
        </div>
    </div>
@else
    @php
        $cardColors = [
            [
                'bg' => 'bg-primary',
                'badge' => 'badge-light text-primary',
                'title_color' => 'text-white',
                'icon_color' => 'text-white',
                'meta_label_color' => 'text-white-50',
                'meta_value_color' => 'text-white',
                'border_color' => 'rgba(255, 255, 255, 0.15)',
                'btn_kelas' => 'btn-light text-primary',
                'btn_others' => 'btn-outline-light text-white'
            ],
            [
                'bg' => 'bg-secondary',
                'badge' => 'badge-light text-secondary',
                'title_color' => 'text-white',
                'icon_color' => 'text-white',
                'meta_label_color' => 'text-white-50',
                'meta_value_color' => 'text-white',
                'border_color' => 'rgba(255, 255, 255, 0.15)',
                'btn_kelas' => 'btn-light text-secondary',
                'btn_others' => 'btn-outline-light text-white'
            ],
            [
                'bg' => 'bg-success',
                'badge' => 'badge-light text-success',
                'title_color' => 'text-white',
                'icon_color' => 'text-white',
                'meta_label_color' => 'text-white-50',
                'meta_value_color' => 'text-white',
                'border_color' => 'rgba(255, 255, 255, 0.15)',
                'btn_kelas' => 'btn-light text-success',
                'btn_others' => 'btn-outline-light text-white'
            ],
            [
                'bg' => 'bg-warning',
                'badge' => 'badge-light text-warning',
                'title_color' => 'text-white',
                'icon_color' => 'text-white',
                'meta_label_color' => 'text-white-50',
                'meta_value_color' => 'text-white',
                'border_color' => 'rgba(255, 255, 255, 0.15)',
                'btn_kelas' => 'btn-light text-warning',
                'btn_others' => 'btn-outline-light text-white'
            ],
            [
                'bg' => 'bg-danger',
                'badge' => 'badge-light text-danger',
                'title_color' => 'text-white',
                'icon_color' => 'text-white',
                'meta_label_color' => 'text-white-50',
                'meta_value_color' => 'text-white',
                'border_color' => 'rgba(255, 255, 255, 0.15)',
                'btn_kelas' => 'btn-light text-danger',
                'btn_others' => 'btn-outline-light text-white'
            ],
            [
                'bg' => 'bg-info',
                'badge' => 'badge-light text-info',
                'title_color' => 'text-white',
                'icon_color' => 'text-white',
                'meta_label_color' => 'text-white-50',
                'meta_value_color' => 'text-white',
                'border_color' => 'rgba(255, 255, 255, 0.15)',
                'btn_kelas' => 'btn-light text-info',
                'btn_others' => 'btn-outline-light text-white'
            ]
        ];
    @endphp
    <div class="row">
        @foreach($prodis as $index => $prodi)
        @php
            $color = $cardColors[$index % count($cardColors)];
        @endphp
        <div class="col-xl-4 col-xxl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
            <div class="card h-100 mb-0 shadow-sm {{ $color['bg'] }}">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center" style="background: transparent;">
                    <span class="badge badge-xs {{ $color['badge'] }} font-w600">{{ $prodi->kode_prodi ?? 'PRD' }}</span>
                </div>
                <div class="card-body pt-2 d-flex flex-column justify-content-between">
                    <div>
                        <h4 class="card-title {{ $color['title_color'] }} mb-3" style="font-size: 1.15rem; font-weight: 700;">{{ $prodi->nama_prodi }}</h4>
                        <ul class="list-group mb-4 list-group-flush" style="background: transparent;">
                            <li class="list-group-item px-0 border-top-0 d-flex justify-content-between align-items-start" style="font-size: 0.85rem; background: transparent; border-color: {{ $color['border_color'] }};">
                                <span class="{{ $color['meta_label_color'] }}"><i class="fa fa-map-marker {{ $color['icon_color'] }} me-2"></i>{{ __('Fakultas') }} :</span>
                                <span class="{{ $color['meta_value_color'] }} font-w600 text-end" style="max-width: 60%;">{{ $prodi->fakultas->nama_fakultas ?? '-' }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center" style="font-size: 0.85rem; background: transparent; border-color: {{ $color['border_color'] }};">
                                <span class="{{ $color['meta_label_color'] }}"><i class="fa fa-user {{ $color['icon_color'] }} me-2"></i>{{ __('Kaprodi') }} :</span>
                                <span class="{{ $color['meta_value_color'] }} font-w600">{!! $prodi->kaprodi->name ?? '<span class="text-white-50 italic">' . __('Belum ditentukan') . '</span>' !!}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex gap-2 flex-wrap mt-auto">
                        <a href="{{ route('classes.index', ['prodi_id' => $prodi->id]) }}" class="btn {{ $color['btn_kelas'] }} btn-xs flex-fill py-2 font-w600">📚 {{ __('Kelas') }}</a>
                        <a href="{{ route('classes.archived', ['prodi_id' => $prodi->id]) }}" class="btn {{ $color['btn_others'] }} btn-xs flex-fill py-2">🗃 {{ __('Arsip') }}</a>
                        <a href="{{ route('dosen.index', ['prodi_id' => $prodi->id]) }}" class="btn {{ $color['btn_others'] }} btn-xs flex-fill py-2">👨‍🏫 {{ __('Dosen') }}</a>
                        <a href="{{ route('mahasiswa.index', ['prodi_id' => $prodi->id]) }}" class="btn {{ $color['btn_others'] }} btn-xs flex-fill py-2">🎓 {{ __('Mhs') }}</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

{{-- Link ke OBE Dashboard --}}
<div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-info alert-dismissible fade show p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center shadow-sm" style="border-left: 5px solid #3b82f6 !important; background-color: #eff6ff;">
            <div class="mb-3 mb-md-0">
                <h4 class="alert-heading text-primary mb-1" style="font-weight: 700; font-size: 1.1rem;">{{ __('Sistem OBE') }}</h4>
                <p class="mb-0 text-muted" style="font-size: 0.875rem;">{{ __('Kelola kurikulum, PLO, RPS, dan analitik OBE.') }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm px-4 py-2 text-nowrap">{!! __('Masuk ke OBE &rarr;') !!}</a>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     DOSEN / MAHASISWA → Personal Dashboard
═══════════════════════════════════════════════════════════════════════════════ --}}
@else

<div class="row mb-4">
    <!-- Card 1: Total Kelas -->
    <div class="col-xl-6 col-xxl-6 col-sm-6 mb-3 mb-sm-0">
        <div class="widget-stat card bg-primary" style="margin-bottom: 0; height: auto !important; min-height: 145px;">
            <div class="card-body" style="padding: 1.25rem 1.25rem 1.5rem 1.25rem;">
                <div class="media">
                    <span class="me-3" style="width: 50px; height: 50px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; flex-shrink: 0;">
                        <i class="la la-graduation-cap"></i>
                    </span>
                    <div class="media-body text-white">
                        <p class="mb-1 text-white" style="font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px;">{{ __('Total Kelas') }}</p>
                        <h3 class="text-white" style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.35rem;">{{ $data['total_classes'] }}</h3>
                        <div class="progress mb-2 bg-white" style="height: 4px; opacity: 0.8;">
                            <div class="progress-bar progress-animated bg-white" style="width: 100%; height: 4px;"></div>
                        </div>
                        <small style="opacity: 0.9; font-size: 0.75rem; display: block; line-height: 1.2;">{{ ($data['view_type'] ?? '') == 'dosen' ? __('Kelas aktif yang Anda ampu') : __('Kelas aktif yang Anda ikuti') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card 2: Tugas Tertunda / Perlu Dinilai -->
    <div class="col-xl-6 col-xxl-6 col-sm-6">
        @php
            $isDosen = ($data['view_type'] ?? '') == 'dosen';
        @endphp
        <div class="widget-stat card {{ $isDosen ? 'bg-danger' : 'bg-warning' }}" style="margin-bottom: 0; height: auto !important; min-height: 145px;">
            <div class="card-body" style="padding: 1.25rem 1.25rem 1.5rem 1.25rem;">
                <div class="media">
                    <span class="me-3" style="width: 50px; height: 50px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; flex-shrink: 0;">
                        <i class="la la-tasks"></i>
                    </span>
                    <div class="media-body text-white">
                        <p class="mb-1 text-white" style="font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px;">
                            {{ $isDosen ? __('Tugas Perlu Dinilai') : __('Tugas Tertunda') }}
                        </p>
                        <h3 class="text-white" style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.35rem;">{{ $data['total_assignments'] }}</h3>
                        <div class="progress mb-2 bg-white" style="height: 4px; opacity: 0.8;">
                            <div class="progress-bar progress-animated bg-white" style="width: 100%; height: 4px;"></div>
                        </div>
                        <small style="opacity: 0.9; font-size: 0.75rem; display: block; line-height: 1.2;">
                            {{ $isDosen ? __('Tugas mahasiswa belum dinilai') : __('Tugas belum dikumpulkan') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
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
                            <a href="{{ route('classes.show', $class) }}" class="btn btn-primary btn-sm text-white px-4" style="border-radius: var(--radius-md);">{{ __('Masuk Kelas') }}</a>
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
