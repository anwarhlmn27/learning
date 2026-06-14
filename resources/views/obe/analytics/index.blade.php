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
                <form action="{{ route('analytics.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end;">
                    <div class="form-group" style="margin: 0; flex: 1;">
                        <label class="form-label">{{ __('Program Studi') }}</label>
                        <select name="prodi_id" class="form-control" onchange="this.form.submit()">
                            <option value="">-- {{ __('Pilih Prodi') }} --</option>
                            @foreach($prodis as $p)
                                <option value="{{ $p->id }}" {{ $selectedProdiId == $p->id ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        @if($prodi)
        <!-- Radar Chart Card -->
        <div class="card">
            <div class="card-header">
                <span>{{ __('Capaian CPL (PLO Attainment)') }} - {{ $prodi->nama_prodi }}</span>
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
        <div class="alert alert-info">{{ __('Silakan pilih Program Studi terlebih dahulu.') }}</div>
        @endif

        <!-- Raw Grades Table -->
        <div class="card">
            <div class="card-header">
                <span>{{ __('Data Nilai Mentah (Grades)') }}</span>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Mahasiswa') }}</th>
                            <th>{{ __('Mata Kuliah') }}</th>
                            <th>{{ __('Asesmen') }}</th>
                            <th>{{ __('Skor') }}</th>
                            <th>{{ __('Kontribusi OBE') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grades as $grade)
                        <tr>
                            <td>
                                <strong>{{ optional($grade->enrollment->student)->nama_student }}</strong><br>
                                <small>{{ optional($grade->enrollment->student)->nim }}</small>
                            </td>
                            <td>{{ optional($grade->rpsAssessment->rpsSession->rps->subject)->nama_subject }}</td>
                            <td>{{ optional($grade->rpsAssessment->assessmentType)->name ?? __('Asesmen') }}</td>
                            <td>{{ $grade->score }}</td>
                            <td>
                                @php
                                    $weight = $grade->rpsAssessment->weight ?? 0;
                                    $contribution = ($grade->score / 100) * $weight;
                                @endphp
                                <span style="font-weight: 600; color: var(--primary);">{{ number_format($contribution, 2) }}%</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">{{ __('Belum ada data nilai yang masuk.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar Analytics Info -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        @if(isset($gps) && count($gps) > 0)
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
                    @foreach($gps as $gp)
                    <div style="padding: 0.75rem; background: #f8fafc; border-radius: 0.5rem; border-left: 3px solid #cbd5e1;">
                        <strong style="display: block; font-size: 0.9rem;">{{ $gp->nm_profil }}</strong>
                        <small style="color: #64748b;">{{ __('Target kompetensi sedang dievaluasi') }}</small>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(isset($radarLabels) && count($radarLabels) > 0)
        const ctx = document.getElementById('ploRadarChart').getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: {!! json_encode($radarLabels) !!},
                datasets: [{
                    label: 'Capaian Rata-rata Cohort',
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
