<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pemetaan CPL-PL - {{ $prodi->nama_prodi }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #000;
        }
        h2 {
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            font-weight: bold;
        }
        .bg-yellow {
            background-color: #ffc000;
        }
        .check-icon {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
        }
        .table-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    <div class="table-title">Tabel 3 Pemetaan CPL-PL</div>
    
    <table>
        <thead>
            <tr>
                <th rowspan="2" class="bg-yellow" style="width: 5%;">{{ __('No') }}</th>
                <th rowspan="2" class="bg-yellow" style="width: 15%;">{{ __('Kode CPL') }}</th>
                @if($prodi->gps->count() > 0)
                    <th colspan="{{ $prodi->gps->count() }}" class="bg-yellow">Profil Lulusan (PL)</th>
                @else
                    <th class="bg-yellow">Profil Lulusan (PL)</th>
                @endif
            </tr>
            <tr>
                @forelse($prodi->gps as $gp)
                    <th class="bg-yellow">{{ $gp->kode_profil }}</th>
                @empty
                    <th class="bg-yellow">{{ __('Belum ada PL') }}</th>
                @endforelse
            </tr>
        </thead>
        <tbody>
            @forelse($prodi->plos as $index => $plo)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $plo->kode_plo }}</td>
                    @forelse($prodi->gps as $gp)
                        <td>
                            @if($plo->gps->contains('id', $gp->id))
                                <!-- <span class="check-icon">☑</span> or ✔ -->
                                <span class="check-icon">✔</span>
                            @endif
                        </td>
                    @empty
                        <td>-</td>
                    @endforelse
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 2 + max(1, $prodi->gps->count()) }}" style="text-align: center;">Belum ada data CPL (PLO).</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
