<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RPS - {{ $rp->subject->nama_subject ?? 'Subject' }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; line-height: 1.4; }
        h1, h2, h3, h4 { text-align: center; margin: 5px 0; }
        .section-title { font-size: 14px; font-weight: bold; background-color: #f0f0f0; padding: 5px; margin-top: 20px; border: 1px solid #000; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; vertical-align: top; }
        th { background-color: #f0f0f0; text-align: center; }
        .visi-misi { margin-bottom: 20px; }
        .visi-misi h4 { text-align: left; background: none; border: none; padding: 0; margin-top: 10px; font-size: 13px; }
        .visi-misi p { margin: 5px 0; }
        .visi-misi ul, .visi-misi ol { margin: 5px 0; padding-left: 20px; }
    </style>
</head>
<body>

    <!-- Header / Identitas -->
    <div style="text-align: center; margin-bottom: 20px;">
        <h2>RENCANA PEMBELAJARAN SEMESTER (RPS)</h2>
        <h3>{{ $rp->subject->nama_subject ?? '-' }} ({{ $rp->subject->kode_subject ?? '-' }})</h3>
        <p>Kurikulum: {{ $rp->kurikulum->nm_kurikulum ?? '-' }} (Versi {{ $rp->versi }})</p>
    </div>

    <!-- Visi Misi Universitas -->
    @if($visiUniv)
    <div class="visi-misi">
        <div class="section-title">Visi & Misi Universitas</div>
        <h4>Visi:</h4>
        <p>{{ $visiUniv->visi }}</p>
        
        <h4>Misi:</h4>
        <ul>
            @foreach($visiUniv->details->where('type', 'misi') as $misi)
                <li>{{ $misi->konten }}</li>
            @endforeach
        </ul>

        <h4>Tujuan:</h4>
        <ul>
            @foreach($visiUniv->details->where('type', 'tujuan') as $tujuan)
                <li>{{ $tujuan->konten }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Visi Misi Fakultas -->
    @if($visiFakultas)
    <div class="visi-misi">
        <div class="section-title">Visi & Misi Fakultas</div>
        <h4>Visi:</h4>
        <p>{{ $visiFakultas->visi }}</p>
        
        <h4>Misi:</h4>
        <ul>
            @foreach($visiFakultas->details->where('type', 'misi') as $misi)
                <li>{{ $misi->konten }}</li>
            @endforeach
        </ul>

        <h4>Tujuan:</h4>
        <ul>
            @foreach($visiFakultas->details->where('type', 'tujuan') as $tujuan)
                <li>{{ $tujuan->konten }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Visi Misi Prodi -->
    @if($visiProdi)
    <div class="visi-misi">
        <div class="section-title">Visi & Misi Program Studi</div>
        <h4>Visi:</h4>
        <p>{{ $visiProdi->visi }}</p>
        
        <h4>Misi:</h4>
        <ul>
            @foreach($visiProdi->details->where('type', 'misi') as $misi)
                <li>{{ $misi->konten }}</li>
            @endforeach
        </ul>

        <h4>Tujuan:</h4>
        <ul>
            @foreach($visiProdi->details->where('type', 'tujuan') as $tujuan)
                <li>{{ $tujuan->konten }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Informasi Mata Kuliah -->
    <div class="section-title">Informasi Mata Kuliah</div>
    <table>
        <tr>
            <th width="30%" style="text-align: left;">Nama Mata Kuliah</th>
            <td>{{ $rp->subject->nama_subject ?? '-' }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Kode Mata Kuliah</th>
            <td>{{ $rp->subject->kode_subject ?? '-' }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Bobot SKS</th>
            <td>{{ $rp->subject->total_sks ?? '-' }} SKS (T: {{ $rp->subject->sks_t ?? 0 }}, P: {{ $rp->subject->sks_p ?? 0 }})</td>
        </tr>
        <tr>
            <th style="text-align: left;">Semester</th>
            <td>{{ $rp->subject->semester ?? '-' }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Mata Kuliah Prasyarat (Prerequisite)</th>
            <td>{{ $rp->subject->prerequisites->count() > 0 ? $rp->subject->prerequisites->pluck('nama_subject')->implode(', ') : 'Tidak Ada' }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Deskripsi Mata Kuliah</th>
            <td>{{ $rp->subject->deskripsi ?? '-' }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Tanggal Penyusunan</th>
            <td>{{ $rp->tanggal_penyusunan ? \Carbon\Carbon::parse($rp->tanggal_penyusunan)->format('d M Y') : '-' }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Pengembang RPS</th>
            <td>{{ $rp->pengembang_rps ?? '-' }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Dosen Pengampu</th>
            <td>{{ $rp->dosen_pengampu ?? '-' }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Kepala Program Studi</th>
            <td>{{ $rp->subject->prodi->nama_pimpinan ?? '-' }}</td>
        </tr>
    </table>

    <!-- PLO & CLO -->
    <div class="section-title">Capaian Pembelajaran</div>
    
    <div style="margin-bottom: 10px;">
        <h4 style="text-align: left; background: none; border: none; padding: 0; font-size: 13px;">Program Learning Outcomes (PLO) yang dibebankan pada Mata Kuliah:</h4>
        <ul style="padding-left: 20px; margin-top: 5px;">
            @if($rp->subject->plos && $rp->subject->plos->count() > 0)
                @foreach($rp->subject->plos as $plo)
                    <li><strong>{{ $plo->kode_plo }}:</strong> {{ $plo->deskripsi }}</li>
                @endforeach
            @else
                <li>-</li>
            @endif
        </ul>
    </div>

    <div style="margin-bottom: 10px;">
        <h4 style="text-align: left; background: none; border: none; padding: 0; font-size: 13px;">Course Learning Outcomes (CLO) / Capaian Pembelajaran Mata Kuliah (CPMK):</h4>
        <ul style="padding-left: 20px; margin-top: 5px;">
            @if($rp->subject->clos && $rp->subject->clos->count() > 0)
                @foreach($rp->subject->clos as $clo)
                    <li><strong>{{ $clo->kode_clo }}:</strong> {{ $clo->deskripsi }}</li>
                @endforeach
            @else
                <li>-</li>
            @endif
        </ul>
    </div>

    <!-- Referensi & Media -->
    <div class="section-title">Referensi & Media Pembelajaran</div>
    <div style="margin-bottom: 10px;">
        <h4 style="text-align: left; background: none; border: none; padding: 0; font-size: 13px; margin-top: 10px;">Referensi:</h4>
        <p style="margin-top: 5px; white-space: pre-wrap;">{{ $rp->referensi ?? '-' }}</p>
    </div>

    <div style="margin-bottom: 10px;">
        <h4 style="text-align: left; background: none; border: none; padding: 0; font-size: 13px; margin-top: 10px;">Media Pembelajaran:</h4>
        <p style="margin-top: 5px; white-space: pre-wrap;">{{ $rp->media_pembelajaran ?? '-' }}</p>
    </div>

    <!-- Tabel Sesi RPS -->
    <div class="section-title" style="page-break-before: auto;">Rincian Pembelajaran</div>
    <table>
        <thead>
            <tr>
                <th width="5%">Minggu Ke</th>
                <th width="20%">Kemampuan Akhir yang Diharapkan (Sub-CPMK)</th>
                <th width="20%">Bahan Kajian (Materi Pembelajaran)</th>
                <th width="30%">Metode/Aktivitas Pembelajaran<br>(Connect, Coach, Check, Wrap-up)</th>
                <th width="15%">Bentuk & Kriteria Penilaian</th>
                <th width="10%">Bobot (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rp->sessions as $session)
            <tr>
                <td style="text-align: center;">{{ $session->session_number }}</td>
                <td>{{ $session->sub_clo }}</td>
                <td>
                    @if($session->learning_materials)
                        <div style="white-space: pre-wrap;">{{ $session->learning_materials }}</div>
                    @else
                        {{ $session->topic_name }}
                    @endif
                </td>
                <td>
                    @if($session->activities->count() > 0)
                        <ul style="margin: 0; padding-left: 15px;">
                        @foreach($session->activities as $activity)
                            <li><strong>{{ $activity->type }} ({{ $activity->duration }}'):</strong> {{ $activity->content }}</li>
                        @endforeach
                        </ul>
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($session->assessment_indicators)
                        <div style="margin-bottom: 5px;"><strong>Indikator:</strong><br>{{ $session->assessment_indicators }}</div>
                    @endif
                    @if($session->evaluation_criteria)
                        <div style="margin-bottom: 5px;"><strong>Kriteria:</strong><br>{{ $session->evaluation_criteria }}</div>
                    @endif

                    @if($session->assessments->count() > 0)
                        <div style="margin-top: 5px; padding-top: 5px; border-top: 1px solid #eee;">
                        @foreach($session->assessments as $assess)
                            <div style="margin-bottom: 8px; border-bottom: 1px dotted #eee; padding-bottom: 5px;">
                                <strong>{{ $assess->type->name }} ({{ $assess->clo->kode_clo }}):</strong><br>
                                @if($assess->assessment_output) Luaran: {{ $assess->assessment_output }}<br> @endif
                                @if($assess->assignment_activities) <span style="font-size: 10px;">Aktivitas: {{ $assess->assignment_activities }}</span><br> @endif
                                <strong>Weight: {{ $assess->weight }}%</strong>
                            </div>
                        @endforeach
                        </div>
                    @endif
                </td>
                <td style="text-align: center;">
                    {{ $session->assessments->sum('weight') }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
