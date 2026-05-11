<!DOCTYPE html>
<html>
<head>
    <title>Mapping BK - {{ $prodi->nama_prodi }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 5px 0; font-size: 11px; }
        
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; overflow: hidden; }
        th { background-color: #f59e0b; color: #000; font-weight: bold; }
        
        .mk-col { width: 40px; }
        .nama-col { width: 150px; text-align: left; }
        .bk-col { width: 30px; font-size: 8px; }
        
        .check { font-family: DejaVu Sans, sans-serif; color: #16a34a; font-weight: bold; font-size: 12px; }
        
        .table-title { background-color: #f59e0b; font-weight: bold; padding: 5px; border: 1px solid #000; margin-top: 10px; display: inline-block; }
    </style>
</head>
<body>

    <div class="header">
        <h2>PETA KURIKULUM (MATA KULIAH - BAHAN KAJIAN)</h2>
        <p>PROGRAM STUDI: {{ strtoupper($prodi->nama_prodi) }}</p>
    </div>

    <!-- <div class="table-title">Tabel 3 BK-MK</div> -->
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th rowspan="2" class="mk-col">Kode MK</th>
                <th rowspan="2" class="nama-col">Nama MK</th>
                <th colspan="{{ $bks->count() }}">Bahan Kajian (BK)</th>
            </tr>
            <tr>
                @foreach($bks as $bk)
                    <th class="bk-col" title="{{ $bk->nm_bahan_kajian }}">{{ $bk->kode_bk }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $index => $subject)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="mk-col">{{ $subject->kode_subject }}</td>
                    <td class="nama-col">{{ $subject->nama_subject }}</td>
                    @foreach($bks as $bk)
                        <td class="bk-col">
                            @if($subject->bks->contains($bk->id))
                                <span class="check">&#10004;</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
