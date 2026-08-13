@extends('layouts.lms')

@section('header_title', __('Dashboard') . ' LMS')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 1: PERSONAL TEACHING / STUDENT DASHBOARD (if user has classes)
═══════════════════════════════════════════════════════════════════════════════ --}}
@if(($data['total_classes'] ?? 0) > 0 || Auth::user()->hasRole(['dosen', 'kaprodi']) || Auth::user()->student)
<div class="row mb-4">
    <!-- Card 1: Total Kelas -->
    <div class="col-xl-6 col-xxl-6 col-sm-6 mb-3 mb-sm-0">
        <div class="widget-stat card bg-primary" style="margin-bottom: 0; height: auto !important; min-height: 145px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.08);">
            <div class="card-body" style="padding: 1.25rem 1.25rem 1.5rem 1.25rem;">
                <div class="media">
                    <span class="me-3" style="width: 50px; height: 50px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; flex-shrink: 0;">
                        <i class="la la-graduation-cap"></i>
                    </span>
                    <div class="media-body text-white">
                        <p class="mb-1 text-white" style="font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px;">{{ __('Total Kelas Aktif') }}</p>
                        <h3 class="text-white" style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.35rem;">{{ $data['total_classes'] }}</h3>
                        <div class="progress mb-2 bg-white" style="height: 4px; opacity: 0.8;">
                            <div class="progress-bar progress-animated bg-white" style="width: 100%; height: 4px;"></div>
                        </div>
                        <small style="opacity: 0.9; font-size: 0.75rem; display: block; line-height: 1.2;">
                            {{ ($data['view_type'] ?? '') == 'dosen' || Auth::user()->hasRole(['dosen', 'kaprodi']) ? __('Kelas aktif yang Anda ampu sebagai dosen') : __('Kelas aktif yang Anda ikuti') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card 2: Tugas Tertunda / Perlu Dinilai -->
    <div class="col-xl-6 col-xxl-6 col-sm-6">
        @php
            $isDosen = ($data['view_type'] ?? '') == 'dosen' || Auth::user()->hasRole(['dosen', 'kaprodi']);
        @endphp
        <div class="widget-stat card {{ $isDosen ? 'bg-danger' : 'bg-warning' }}" style="margin-bottom: 0; height: auto !important; min-height: 145px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.08);">
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
                            {{ $isDosen ? __('Tugas mahasiswa yang perlu dinilai') : __('Tugas belum dikumpulkan') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm" style="border-radius: 12px; height: 100%;">
            <div class="card-header border-bottom bg-light py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-w700 text-dark">
                    👨‍🏫 {{ ($data['view_type'] ?? '') == 'dosen' || Auth::user()->hasRole(['dosen', 'kaprodi']) ? __('Kelas yang Saya Ampu') : __('Kelas yang Saya Ikuti') }}
                </h5>
                <a href="{{ route('classes.index', ['tab' => 'my_classes']) }}" class="btn btn-outline-primary btn-xs font-w600">
                    {{ __('Lihat Semua') }} →
                </a>
            </div>
            <div class="card-body p-0">
                @if(count($data['classes']) > 0)
                    <div style="display: flex; flex-direction: column; gap: 1px; background: #f1f5f9;">
                        @foreach($data['classes'] as $class)
                            <div style="background: white; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                                <div>
                                    <div class="d-flex align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                        <h5 style="margin: 0; color: var(--primary); font-weight: 700;">{{ $class->nama_kelas }}</h5>
                                        @if(optional(optional($class->subject)->prodi)->nama_prodi)
                                            <span style="font-size: 0.7rem; background: #e0e7ff; color: #3730a3; padding: 0.15rem 0.5rem; border-radius: 4px; font-weight: 600;">
                                                {{ $class->subject->prodi->nama_prodi }}
                                            </span>
                                        @endif
                                    </div>
                                    <p style="margin: 0; font-size: 0.875rem; color: #64748b;">
                                        {{ $class->subject->nama_subject ?? __('Subject') }} ({{ $class->subject->kode_subject ?? '-' }}) • 
                                        {{ $class->semester ?? '-' }} {{ $class->tahun_akademik ?? '-' }}
                                    </p>
                                </div>
                                <a href="{{ route('classes.show', $class) }}" class="btn btn-primary btn-sm px-4 font-w600" style="border-radius: 6px;">
                                    {{ __('Masuk Kelas') }} →
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="padding: 3rem; text-align: center; color: var(--text-muted);">
                        <p style="font-size: 2rem; margin-bottom: 0.5rem;">📚</p>
                        <p class="mb-0">{{ __('Belum ada kelas aktif yang Anda ampu.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow-sm" style="border-radius: 12px; height: 100%;">
            <div class="card-header border-bottom py-3" style="background-color: #fffbeb;">
                <h5 class="mb-0 font-w700 text-dark">📋 {{ __('Upcoming Assignments') }}</h5>
            </div>
            <div class="card-body p-3">
                @if(count($data['assignments']) > 0)
                    <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                        @foreach($data['assignments'] as $assignment)
                            <div style="border-left: 3px solid #f59e0b; padding-left: 0.85rem; background: #fefce8; padding: 0.6rem 0.85rem; border-radius: 0 6px 6px 0;">
                                <h6 style="margin: 0 0 0.2rem 0; font-size: 0.875rem; font-weight: 700; color: #92400e;">{{ $assignment->title }}</h6>
                                <p style="margin: 0; font-size: 0.75rem; color: #78350f;">
                                    {{ __('Batas Waktu:') }} {{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y, H:i') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; color: var(--text-muted); padding: 2rem 0;">
                        <p style="font-size: 1.5rem; margin-bottom: 0.25rem;">✅</p>
                        <p class="mb-0" style="font-size: 0.85rem;">{{ __('Tidak ada tugas yang tertunda.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 2: PROGRAM STUDI SELECTOR (For Admin, Kaprodi, Dekan, Staff)
═══════════════════════════════════════════════════════════════════════════════ --}}
@if(Auth::user()->hasRole(['admin', 'rektor', 'dekan', 'kaprodi', 'baak', 'finance', 'kemahasiswaan']) || Auth::user()->can('view-institusi'))

<div class="row page-titles mx-0 mt-2">
    <div class="col-sm-12 p-md-0">
        <div class="welcome-text">
            <h4>{{ __('Pilih Program Studi') }}</h4>
            <p class="mb-0 text-muted">{{ __('Akses kelas aktif, arsip, data dosen, dan mahasiswa berdasarkan program studi.') }}</p>
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
            $isUserKaprodiOfThis = $prodi->kaprodi_id === Auth::id();
        @endphp
        <div class="col-xl-4 col-xxl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
            <div class="card h-100 mb-0 shadow-sm {{ $color['bg'] }}" style="border-radius: 12px; position: relative;">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center" style="background: transparent;">
                    <span class="badge badge-xs {{ $color['badge'] }} font-w600">{{ $prodi->kode_prodi ?? 'PRD' }}</span>
                    @if($isUserKaprodiOfThis)
                        <span class="badge badge-warning text-dark font-w700" style="font-size: 0.7rem; border-radius: 9999px;">
                            ⭐ {{ __('Prodi Anda (Kaprodi)') }}
                        </span>
                    @endif
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
        <div class="alert alert-info alert-dismissible fade show p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center shadow-sm" style="border-left: 5px solid #3b82f6 !important; background-color: #eff6ff; border-radius: 12px;">
            <div class="mb-3 mb-md-0">
                <h4 class="alert-heading text-primary mb-1" style="font-weight: 700; font-size: 1.1rem;">{{ __('Sistem OBE') }}</h4>
                <p class="mb-0 text-muted" style="font-size: 0.875rem;">{{ __('Kelola kurikulum, PLO, RPS, dan analitik OBE.') }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm px-4 py-2 text-nowrap" style="border-radius: 6px; font-weight: 600;">{!! __('Masuk ke OBE &rarr;') !!}</a>
        </div>
    </div>
</div>

@endif

@endsection
