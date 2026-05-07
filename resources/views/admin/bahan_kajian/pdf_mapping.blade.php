<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tabel 5 Pemetaan CPL-BK - {{ $prodi->nama_prodi }}</title>
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

    <div class="table-title">Tabel 5 CPL-BK MR</div>
    
    <table>
        <thead>
            <tr>
                <th class="bg-yellow" style="width: 15%;">Kode BK</th>
                @forelse($prodi->plos as $plo)
                    <th class="bg-yellow">{{ str_replace('PLO', 'CPL', $plo->kode_plo) }}</th>
                @empty
                    <th class="bg-yellow">Belum ada CPL</th>
                @endforelse
            </tr>
        </thead>
        <tbody>
            @php
                // Inisialisasi total check per PLO
                $totals = [];
                foreach($prodi->plos as $plo) {
                    $totals[$plo->id] = 0;
                }
            @endphp
            
            @forelse($prodi->bahanKajians as $bk)
                <tr>
                    <td style="text-align: left;">{{ $bk->kode_bk }}</td>
                    @forelse($prodi->plos as $plo)
                        <td>
                            @if($bk->plos->contains('id', $plo->id))
                                <span class="check-icon">✔</span>
                                @php $totals[$plo->id]++; @endphp
                            @endif
                        </td>
                    @empty
                        <td>-</td>
                    @endforelse
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 1 + max(1, $prodi->plos->count()) }}" style="text-align: center;">Belum ada data Bahan Kajian.</td>
                </tr>
            @endforelse
            
            @if($prodi->bahanKajians->count() > 0 && $prodi->plos->count() > 0)
                <tr>
                    <td style="font-weight: bold; text-align: left;">TOTAL</td>
                    @foreach($prodi->plos as $plo)
                        <td class="bg-yellow" style="font-weight: bold;">
                            {{ $totals[$plo->id] > 0 ? $totals[$plo->id] : '' }}
                        </td>
                    @endforeach
                </tr>
            @endif
        </tbody>
    </table>

</body>
</html>
