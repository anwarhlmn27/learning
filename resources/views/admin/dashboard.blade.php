@extends('layouts.admin')

@section('title', __('Dashboard'))

@section('content')
<div class="row">
    <!-- Welcome section -->
    <div class="col-xl-12 col-xxl-12">
        <div class="welcome-section mb-4">
            <h1 style="font-size: 1.875rem; font-weight: 700; margin: 0 0 0.5rem 0;">{{ __('Selamat datang, Admin') }}</h1>
            <p style="color: var(--text-muted); margin: 0;">{{ __('Berikut adalah ringkasan sistem Outcome-Based Education (OBE) Anda hari ini.') }}</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Card 1: Fakultas -->
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-primary">
            <div class="card-body">
                <div class="media">
                    <span class="me-3">
                        <i class="la la-building"></i>
                    </span>
                    <div class="media-body text-white">
                        <p class="mb-1">{{ __('Fakultas') }}</p>
                        <h3 class="text-white">{{ $count['fakultas'] }}</h3>
                        <div class="progress mb-2 bg-white">
                            <div class="progress-bar progress-animated bg-white" style="width: {{ $count['fakultas'] > 0 ? '100%' : '10%' }}"></div>
                        </div>
                        <small>{{ __('Fakultas Terdaftar') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card 2: Program Studi -->
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-warning">
            <div class="card-body">
                <div class="media">
                    <span class="me-3">
                        <i class="la la-graduation-cap"></i>
                    </span>
                    <div class="media-body text-white">
                        <p class="mb-1">{{ __('Program Studi') }}</p>
                        <h3 class="text-white">{{ $count['prodi'] }}</h3>
                        <div class="progress mb-2 bg-white">
                            <div class="progress-bar progress-animated bg-white" style="width: {{ $count['prodi'] > 0 ? '100%' : '10%' }}"></div>
                        </div>
                        <small>{{ __('Program Studi Aktif') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Kurikulum -->
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-secondary">
            <div class="card-body">
                <div class="media">
                    <span class="me-3">
                        <i class="la la-book"></i>
                    </span>
                    <div class="media-body text-white">
                        <p class="mb-1">{{ __('Kurikulum') }}</p>
                        <h3 class="text-white">{{ $count['kurikulum'] }}</h3>
                        <div class="progress mb-2 bg-white">
                            <div class="progress-bar progress-animated bg-white" style="width: {{ $count['kurikulum'] > 0 ? '100%' : '10%' }}"></div>
                        </div>
                        <small>{{ __('Kurikulum Terbit') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Mata Kuliah -->
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-danger">
            <div class="card-body">
                <div class="media">
                    <span class="me-3">
                        <i class="la la-file-text"></i>
                    </span>
                    <div class="media-body text-white">
                        <p class="mb-1">{{ __('Mata Kuliah') }}</p>
                        <h3 class="text-white">{{ $count['subject'] }}</h3>
                        <div class="progress mb-2 bg-white">
                            <div class="progress-bar progress-animated bg-white" style="width: {{ $count['subject'] > 0 ? '100%' : '10%' }}"></div>
                        </div>
                        <small>{{ __('Total Mata Kuliah') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Card 5: Pengguna -->
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-success">
            <div class="card-body">
                <div class="media">
                    <span class="me-3">
                        <i class="la la-users"></i>
                    </span>
                    <div class="media-body text-white">
                        <p class="mb-1">{{ __('Users') }}</p>
                        <h3 class="text-white">{{ $count['user'] }}</h3>
                        <div class="progress mb-2 bg-white">
                            <div class="progress-bar progress-animated bg-white" style="width: {{ $count['user'] > 0 ? '100%' : '10%' }}"></div>
                        </div>
                        <small>{{ __('Pengguna Sistem') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Prodi List Row -->
<div class="row">
    <div class="col-xl-12 col-xxl-12 col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Faculty List') }}</h4>
            </div>
            <div class="card-body pt-2">
                <div class="table-responsive recentOrderTable">
                    <table class="table verticle-middle text-nowrap table-responsive-md">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('No.') }}</th>
                                <th scope="col">{{ __('Faculty') }}</th>
                                <th scope="col">{{ __('Jumlah Prodi') }}</th>
                                <th scope="col">{{ __('Jumlah RPS') }}</th>
                                <th scope="col">{{ __('Jumlah Mahasiswa') }}</th>
                                <th scope="col">{{ __('Jumlah Dosen') }}</th>
                                <th scope="col">{{ __('Jumlah Kurikulum') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faculties as $index => $faculty)
                                <tr>
                                    <td>{{ sprintf('%02d', $index + 1) }}</td>
                                    <td>{{ $faculty->short_name }}</td>
                                    <td>{{ $faculty->jumlah_prodi }}</td>
                                    <td>{{ $faculty->jumlah_rps }}</td>
                                    <td>{{ $faculty->jumlah_mahasiswa }}</td>
                                    <td>{{ $faculty->jumlah_dosen }}</td>
                                    <td>{{ $faculty->jumlah_kurikulum }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('No Data Found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart Morris plugin files -->
<script src="{{ asset('vendor/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('vendor/morris/morris.min.js') }}"></script>

<!-- Chart sparkline plugin files -->
<script src="{{ asset('vendor/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
<script src="{{ asset('js/plugins-init/sparkline-init.js') }}"></script>

<!-- Svganimation scripts -->
<script src="{{ asset('vendor/svganimation/vivus.min.js') }}"></script>
<script src="{{ asset('vendor/svganimation/svg.animation.js') }}"></script>

<!-- CKEditor -->
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>

<!-- Dashboard JS -->
<script src="{{ asset('js/dashboard/dashboard.js') }}"></script>
@endsection
