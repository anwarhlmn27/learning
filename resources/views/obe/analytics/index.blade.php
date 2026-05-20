@extends('layouts.admin')

@section('title', 'OBE Analytics Dashboard')

@section('header_left')
    <h1 style="margin: 0; font-size: 1.25rem; font-weight: 600;">OBE Analytics</h1>
@endsection

@section('content')
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Main Charts & Tables -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Filter Card -->
        <div class="card">
            <div class="card-body">
                <form action="{{ route('analytics.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end;">
                    <div class="form-group" style="margin: 0; flex: 1;">
                        <label class="form-label">Program Studi</label>
                        <select name="prodi_id" class="form-control">
                            <option value="">-- Pilih Prodi --</option>
                            @foreach(\App\Models\Prodi::all() as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter Data</button>
                </form>
            </div>
        </div>

        <!-- Radar Chart Card -->
        <div class="card">
            <div class="card-header">
                <span>Capaian CPL (PLO) - Radar Chart</span>
            </div>
            <div class="card-body" style="height: 400px; display: flex; justify-content: center; align-items: center;">
                <canvas id="ploRadarChart"></canvas>
            </div>
        </div>

        <!-- Raw Grades Table -->
        <div class="card">
            <div class="card-header">
                <span>Data Nilai Mentah (Grades)</span>
            </div>
            <div class="card-body" style="padding: 0;">
                <table>
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Mata Kuliah</th>
                            <th>Asesmen</th>
                            <th>Skor</th>
                            <th>Kontribusi OBE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grades = \App\Models\StudentGrade::with(['enrollment.student', 'rpsAssessment.rpsSession.rps.subject'])->latest()->take(10)->get();
                        @endphp
                        @forelse($grades as $grade)
                        <tr>
                            <td>
                                <strong>{{ optional($grade->enrollment->student)->nama_student }}</strong><br>
                                <small>{{ optional($grade->enrollment->student)->nim }}</small>
                            </td>
                            <td>{{ optional($grade->rpsAssessment->rpsSession->rps->subject)->nama_subject }}</td>
                            <td>{{ optional($grade->rpsAssessment->assessmentType)->name ?? 'Asesmen' }}</td>
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
                            <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">Belum ada data nilai yang masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar Analytics Info -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Recommendation Card -->
        <div class="card" style="border-top: 4px solid var(--success);">
            <div class="card-header">
                <span>Rekomendasi Profil Lulusan</span>
            </div>
            <div class="card-body">
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1rem;">
                    Berdasarkan ambang batas (threshold) CPL yang telah dicapai:
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="padding: 0.75rem; background: #f0fdf4; border-radius: 0.5rem; border-left: 3px solid var(--success);">
                        <strong style="display: block; font-size: 0.9rem;">Software Engineer</strong>
                        <small style="color: #166534;">CPL-01 & CPL-03 > 80%</small>
                    </div>
                    <div style="padding: 0.75rem; background: #f8fafc; border-radius: 0.5rem; border-left: 3px solid #cbd5e1;">
                        <strong style="display: block; font-size: 0.9rem; color: #64748b;">System Analyst</strong>
                        <small style="color: #94a3b8;">CPL-02 < 70% (Belum Tercapai)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- PLO Status Card -->
        <div class="card">
            <div class="card-header">
                <span>Status Capaian CPL</span>
            </div>
            <div class="card-body" style="padding: 0;">
                @php
                    $plos = \App\Models\Plo::take(5)->get();
                @endphp
                @foreach($plos as $plo)
                <div style="padding: 1rem; border-bottom: 1px solid #f1f5f9;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.5rem;">
                        <strong>{{ $plo->code }}</strong>
                        <span>75%</span>
                    </div>
                    <div style="height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                        <div style="width: 75%; height: 100%; background: var(--primary);"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('ploRadarChart').getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['CPL-01', 'CPL-02', 'CPL-03', 'CPL-04', 'CPL-05', 'CPL-06'],
                datasets: [{
                    label: 'Capaian Rata-rata',
                    data: [85, 70, 90, 65, 80, 75],
                    fill: true,
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    borderColor: 'rgb(79, 70, 229)',
                    pointBackgroundColor: 'rgb(79, 70, 229)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(79, 70, 229)'
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
    });
</script>
@endsection
