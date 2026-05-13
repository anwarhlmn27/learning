<!DOCTYPE html>
<html>
<head>
    <title>Mapping PLO - {{ $prodi->nama_prodi }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 5px 0; font-size: 11px; }
        
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; overflow: hidden; }
        th { background-color: #f59e0b; color: #000; font-weight: bold; }
        
        .mk-col { width: 45px; }
        .nama-col { width: auto; text-align: left; }
        .plo-col { width: 35px; font-size: 8px; }
        .no-col { width: 25px; }
        .sks-col { width: 30px; }
        
        .check { font-family: DejaVu Sans, sans-serif; color: #000; font-weight: bold; font-size: 12px; }
        
        .table-title { background-color: #f59e0b; font-weight: bold; padding: 5px; border: 1px solid #000; margin-top: 10px; display: inline-block; }
    </style>
</head>
<body>

    <div class="header">
        <h2>PETA KURIKULUM (MATA KULIAH - CPL/PLO)</h2>
        <p>PROGRAM STUDI: {{ strtoupper($prodi->nama_prodi) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="no-col">No</th>
                <th rowspan="2" class="mk-col">Kode MK</th>
                <th rowspan="2" class="nama-col">Nama Mata Kuliah</th>
                <th rowspan="2" class="sks-col">SKS</th>
                <th colspan="{{ $plos->count() }}">Capaian Pembelajaran Lulusan (CPL/PLO)</th>
            </tr>
            <tr>
                @foreach($plos as $plo)
                    <th class="plo-col" title="{{ $plo->plo_title }}">{{ $plo->kode_plo }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $index => $subject)
                <tr>
                    <td class="no-col">{{ $index + 1 }}</td>
                    <td class="mk-col">{{ $subject->kode_subject }}</td>
                    <td class="nama-col">{{ $subject->nama_subject }}</td>
                    <td class="sks-col">{{ $subject->total_sks }}</td>
                    @foreach($plos as $plo)
                        <td class="plo-col">
                            @php
                                $mapping = $subject->plos->where('id', $plo->id)->first();
                            @endphp
                            @if($mapping)
                                {{ $mapping->pivot->mapping_level }}
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
