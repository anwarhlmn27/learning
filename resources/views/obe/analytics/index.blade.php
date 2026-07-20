@extends('layouts.admin')

@section('title', __('OBE Analytics Dashboard'))

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>{{ __('OBE Analytics') }}</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Academic & OBE') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0);">{{ __('OBE Analytics') }}</a></li>
        </ol>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Main Charts & Tables -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Filter Card -->
        <div class="card">
            <div class="card-body">
                <form action="{{ route('analytics.index') }}" method="GET" id="filterForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Fakultas') }}</label>
                            <select name="fakultas_id" id="fakultas_id" class="form-control select2">
                                <option value="">-- {{ __('Pilih Fakultas') }} --</option>
                                @foreach($fakultas as $f)
                                    <option value="{{ $f->id }}" {{ $selectedFakultasId == $f->id ? 'selected' : '' }}>{{ $f->nama_fakultas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Program Studi') }}</label>
                            <select name="prodi_id" id="prodi_id" class="form-control select2">
                                <option value="">-- {{ __('Pilih Prodi') }} --</option>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id }}" {{ $selectedProdiId == $p->id ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Angkatan') }}</label>
                            <select name="angkatan" id="angkatan" class="form-control select2">
                                <option value="">-- {{ __('Pilih Angkatan') }} --</option>
                                @foreach($angkatans as $a)
                                    <option value="{{ $a }}" {{ $selectedAngkatan == $a ? 'selected' : '' }}>{{ $a }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Mahasiswa') }}</label>
                            <select name="student_id" id="student_id" class="form-control select2">
                                <option value="">-- {{ __('Pilih Mahasiswa') }} --</option>
                                @foreach($students as $s)
                                    <option value="{{ $s->id }}" {{ $selectedStudentId == $s->id ? 'selected' : '' }}>{{ $s->nim }} - {{ $s->nama_student }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-right">
                            <button type="submit" class="btn btn-primary">{{ __('Tampilkan Analitik') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($prodi)
        <!-- Radar Chart Card -->
        <div class="card">
            <div class="card-header">
                <span>{{ __('Capaian CPL (PLO Attainment)') }} - {{ $student ? $student->nama_student : 'Rata-rata Cohort' }}</span>
            </div>
            <div class="card-body" style="height: 400px; display: flex; justify-content: center; align-items: center;">
                @if(count($radarLabels) > 0)
                    <canvas id="ploRadarChart"></canvas>
                @else
                    <p style="color: var(--text-muted);">{{ __('Belum ada data PLO untuk Prodi ini.') }}</p>
                @endif
            </div>
        </div>
        @else
        <div class="alert alert-info">{{ __('Silakan filter Fakultas dan Program Studi terlebih dahulu.') }}</div>
        @endif

        <!-- Raw Grades Table -->
        <div class="card">
            <div class="card-header">
                <span>{{ __('Data Nilai Mentah (Grades)') }}</span>
            </div>
            <div class="card-body" style="padding: 1rem;">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="gradesTable">
                        <thead>
                            <tr>
                                <th>{{ __('Mahasiswa') }}</th>
                                <th>{{ __('Mata Kuliah') }}</th>
                                <th>{{ __('Asesmen') }}</th>
                                <th>{{ __('Skor Mentah') }}</th>
                                <th>{{ __('Bobot') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grades as $grade)
                            <tr>
                                <td>
                                    <strong>{{ optional($grade->enrollment->student)->nama_student }}</strong><br>
                                    <small>{{ optional($grade->enrollment->student)->nim }}</small>
                                </td>
                                <td>{{ optional($grade->rpsAssessment->session->rps->subject)->nama_subject }}</td>
                                <td>
                                    {{ $grade->rpsAssessment->assessment_type ?? __('Asesmen') }}
                                    <br><small class="text-muted">CPMK: {{ optional($grade->rpsAssessment->clo)->kode_clo }}</small>
                                </td>
                                <td>{{ $grade->score }}</td>
                                <td>
                                    @php
                                        $weight = $grade->rpsAssessment->weight ?? 0;
                                    @endphp
                                    <span style="font-weight: 600; color: var(--primary);">{{ $weight }}%</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Analytics Info -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        @if(isset($gpAttainments) && count($gpAttainments) > 0)
        <!-- Recommendation Card -->
        <div class="card" style="border-top: 4px solid var(--success);">
            <div class="card-header">
                <span>{{ __('Profil Lulusan (Graduate Profiles)') }}</span>
            </div>
            <div class="card-body">
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1rem;">
                    {{ __('Berdasarkan ambang batas CPL yang dicapai:') }}
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($gpAttainments as $ga)
                    @php
                        $isDominant = ($highestGpScore > 0 && $ga['attainment'] == $highestGpScore);
                        $bgCard = $isDominant ? '#f0fdf4' : '#f8fafc';
                        $borderCard = $isDominant ? '3px solid #22c55e' : '3px solid #cbd5e1';
                        $icon = $isDominant ? '⭐' : '';
                    @endphp
                    <div style="padding: 0.75rem; background: {{ $bgCard }}; border-radius: 0.5rem; border-left: {{ $borderCard }};">
                        <strong style="display: block; font-size: 0.9rem;">{{ $icon }} {{ $ga['gp']->nm_profil }}</strong>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.25rem;">
                            <small style="color: #64748b;">{{ __('Kecocokan Profil:') }}</small>
                            <span style="font-weight: 600; font-size: 0.85rem; color: {{ $isDominant ? '#15803d' : '#475569' }}">{{ number_format($ga['attainment'], 1) }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if(isset($ploAttainments) && count($ploAttainments) > 0)
        <!-- PLO Status Card (CQI) -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <span>{{ __('Status Capaian CPL (CQI)') }}</span>
                <span class="badge" style="background: var(--warning); color: #000;">{{ __('Standard Target: 70%') }}</span>
            </div>
            <div class="card-body" style="padding: 0;">
                @foreach($ploAttainments as $pa)
                @php
                    $isAchieved = $pa['attainment'] >= $pa['target'];
                    $color = $isAchieved ? 'var(--success)' : 'var(--danger)';
                    $bgColor = $isAchieved ? '#f0fdf4' : '#fef2f2';
                @endphp
                <div style="padding: 1rem; border-bottom: 1px solid #f1f5f9; background: {{ $bgColor }};">
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.5rem;">
                        <strong>{{ $pa['plo']->kode_plo }} <span title="{{ $pa['plo']->plo_title }}">&#9432;</span></strong>
                        <span style="font-weight: 600; color: {{ $color }};">{{ number_format($pa['attainment'], 1) }}%</span>
                    </div>
                    <div style="height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                        <div style="width: {{ $pa['attainment'] }}%; height: 100%; background: {{ $color }};"></div>
                    </div>
                    @if(!$isAchieved)
                        <div style="margin-top: 0.5rem; font-size: 0.75rem; color: var(--danger); display: flex; align-items: center; gap: 0.25rem;">
                            <span>⚠️</span> {{ __('Butuh tindakan perbaikan (Action Plan)') }}
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
    </div>
</div>
@endsection

@section('scripts')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<!-- Load Select2 CSS and JS if not included in layout -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Initialize Select2
        $('.select2').select2({
            width: '100%',
            placeholder: function(){
                $(this).data('placeholder');
            }
        });

        // Initialize DataTable for grades
        if ($.fn.DataTable.isDataTable('#gradesTable')) {
            $('#gradesTable').DataTable().destroy();
        }
        $('#gradesTable').DataTable({
            pageLength: 10,
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });

        // Cascading Dropdown Logic
        $('#fakultas_id').on('change', function() {
            let fakultasId = $(this).val();
            let $prodiSelect = $('#prodi_id');
            $prodiSelect.empty().append('<option value="">-- Pilih Prodi --</option>');
            $('#angkatan').empty().append('<option value="">-- Pilih Angkatan --</option>');
            $('#student_id').empty().append('<option value="">-- Pilih Mahasiswa --</option>');
            
            if (fakultasId) {
                $.ajax({
                    url: "{{ route('analytics.api.prodis') }}",
                    type: "GET",
                    data: { fakultas_id: fakultasId },
                    success: function(data) {
                        $.each(data, function(key, prodi) {
                            $prodiSelect.append('<option value="'+ prodi.id +'">'+ prodi.nama_prodi +'</option>');
                        });
                        $prodiSelect.trigger('change');
                    }
                });
            }
        });

        $('#prodi_id').on('change', function() {
            let prodiId = $(this).val();
            let $angkatanSelect = $('#angkatan');
            $angkatanSelect.empty().append('<option value="">-- Pilih Angkatan --</option>');
            $('#student_id').empty().append('<option value="">-- Pilih Mahasiswa --</option>');
            
            if (prodiId) {
                $.ajax({
                    url: "{{ route('analytics.api.angkatans') }}",
                    type: "GET",
                    data: { prodi_id: prodiId },
                    success: function(data) {
                        $.each(data, function(key, angkatan) {
                            $angkatanSelect.append('<option value="'+ angkatan +'">'+ angkatan +'</option>');
                        });
                    }
                });
            }
        });

        $('#angkatan').on('change', function() {
            let prodiId = $('#prodi_id').val();
            let angkatan = $(this).val();
            let $studentSelect = $('#student_id');
            $studentSelect.empty().append('<option value="">-- Pilih Mahasiswa --</option>');
            
            if (prodiId && angkatan) {
                $.ajax({
                    url: "{{ route('analytics.api.students') }}",
                    type: "GET",
                    data: { prodi_id: prodiId, angkatan: angkatan },
                    success: function(data) {
                        $.each(data, function(key, student) {
                            $studentSelect.append('<option value="'+ student.id +'">'+ student.nim + ' - ' + student.nama_student +'</option>');
                        });
                    }
                });
            }
        });

        // Initialize Radar Chart
        @if(isset($radarLabels) && count($radarLabels) > 0)
        const ctx = document.getElementById('ploRadarChart').getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: {!! json_encode($radarLabels) !!},
                datasets: [{
                    label: '{{ $student ? "Capaian Mahasiswa" : "Capaian Cohort" }}',
                    data: {!! json_encode($radarData) !!},
                    fill: true,
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    borderColor: 'rgb(79, 70, 229)',
                    pointBackgroundColor: 'rgb(79, 70, 229)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(79, 70, 229)'
                },
                {
                    label: 'Standard Minimum (70%)',
                    data: Array({!! count($radarLabels) !!}).fill(70),
                    fill: false,
                    borderColor: 'rgba(239, 68, 68, 0.5)',
                    borderDash: [5, 5],
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                elements: {
                    line: {
                        borderWidth: 3
                    }
                },
                scales: {
                    r: {
                        angleLines: {
                            display: true
                        },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                }
            }
        });
        @endif
    });
</script>
@endsection
