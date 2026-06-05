<!DOCTYPE html>
<html>
<head>
    <title>Kurikulum - {{ $kurikulum->prodi->nama_prodi }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 0;
            font-size: 10px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 10px;
            font-weight: bold;
        }
        .level-header {
            text-align: center;
            background-color: #f3f4f6;
            font-weight: bold;
            padding: 2px;
            margin-top: 10px;
            border: 1px solid #000;
            text-transform: uppercase;
            font-size: 9px;
        }
        .semester-container {
            width: 100%;
            margin-top: 5px;
        }
        .semester-table-wrapper {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            margin-right: 2%;
        }
        .semester-table-wrapper:last-child {
            margin-right: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: left;
        }
        th {
            background-color: #ffffff;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 20px;
        }
        .footer-table {
            width: 100%;
            border: none;
        }
        .footer-table td {
            border: none;
            width: 33%;
            text-align: center;
            vertical-align: top;
            padding-top: 40px;
        }
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            display: inline-block;
            width: 150px;
        }
        .total-row td {
            font-weight: bold;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>HORIZON UNIVERSITY INDONESIA</h1>
        <h2>PRODI {{ $kurikulum->prodi->nama_prodi }}</h2>
        <p>TAHUN AKADEMIK {{ $kurikulum->tahun_akademik }}-{{ $kurikulum->tahun_akademik + 1 }}</p>
    </div>

    @php
        $semesters = $kurikulum->subjects->groupBy('semester');
        $levels = [
            1 => [1, 2],
            2 => [3, 4],
            3 => [5, 6],
            4 => [7, 8]
        ];
        $totalUnits = 0;
    @endphp

    @foreach($levels as $level => $semRange)
        <div class="level-header">TINGKAT {{ $level }} (Level {{ $level }})</div>
        <div class="semester-container">
            @php
                $count1 = count($semesters->get($semRange[0], collect()));
                $count2 = count($semesters->get($semRange[1], collect()));
                $maxRows = max($count1, $count2);
                if ($maxRows < 1) $maxRows = 1;
            @endphp
            @foreach($semRange as $semNum)
                <div class="semester-table-wrapper">
                    <div style="font-weight: bold; margin-bottom: 2px;">Semester {{ $semNum == 1 ? 'I' : ($semNum == 2 ? 'II' : ($semNum == 3 ? 'III' : ($semNum == 4 ? 'IV' : ($semNum == 5 ? 'V' : ($semNum == 6 ? 'VI' : ($semNum == 7 ? 'VII' : 'VIII')))))) }}</div>
                    <table>
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 15%;">Kode MK<br>(CODE)</th>
                                <th rowspan="2" style="width: 35%;">Mata Kuliah (Subject)</th>
                                <th colspan="4">{{ __('Rincian Sebaran SKS') }}</th>
                                <th rowspan="2" style="width: 20%;">Prasyarat<br>(Pre-requisite)</th>
                            </tr>
                            <tr>
                                <th style="width: 7%;">{{ __('T') }}</th>
                                <th style="width: 7%;">{{ __('P') }}</th>
                                <th style="width: 7%;">{{ __('PL') }}</th>
                                <th style="width: 7%;">{{ __('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $subSem = $semesters->get($semNum, collect());
                                $semT = 0;
                                $semP = 0;
                                $semPL = 0;
                                $semTotal = 0;
                            @endphp
                            @foreach($subSem as $ks)
                                <tr>
                                    <td class="text-center">{{ $ks->subject->kode_subject }}</td>
                                    <td>{{ $ks->subject->nama_subject }}</td>
                                    <td class="text-center">{{ $ks->subject->sks_t }}</td>
                                    <td class="text-center">{{ $ks->subject->sks_p }}</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">{{ $ks->subject->total_sks }}</td>
                                    <td class="text-center" style="font-size: 7px;">
                                        {{ $ks->subject->prerequisites->count() > 0 ? $ks->subject->prerequisites->pluck('kode_subject')->implode(', ') : 'Tidak Ada (none)' }}
                                    </td>
                                </tr>
                                @php
                                    $semT += $ks->subject->sks_t;
                                    $semP += $ks->subject->sks_p;
                                    $semTotal += $ks->subject->total_sks;
                                    $totalUnits += $ks->subject->total_sks;
                                @endphp
                            @endforeach
                            <!-- Fill empty rows to keep tables aligned -->
                            @for($i = count($subSem); $i < $maxRows; $i++)
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            @endfor
                            <tr class="total-row">
                                <td colspan="2" style="text-align: right;">Total</td>
                                <td class="text-center">{{ $semT }}</td>
                                <td class="text-center">{{ $semP }}</td>
                                <td class="text-center">0</td>
                                <td class="text-center">{{ $semTotal }}</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    @endforeach

    <div style="margin-top: 10px; font-weight: bold; font-size: 10px; text-align: center;">
        TOTAL UNITS: {{ $totalUnits }}
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    Prepared By:<br><br><br><br>
                    <div class="signature-line"></div><br>
                    &nbsp;
                </td>
                <td>
                    Approved By:<br><br><br><br>
                    <div class="signature-line"></div><br>
                    &nbsp;
                </td>
                <td>
                    Noted By:<br><br><br><br>
                    <div class="signature-line"></div><br>
                    &nbsp;
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
