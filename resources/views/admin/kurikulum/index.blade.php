@extends('layouts.admin')

@section('title', 'Curriculum Data')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Curriculum Data</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span>Curriculum List</span>
        <a href="{{ route('kurikulum.create') }}" class="btn btn-primary">Add Curriculum</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Curriculum Name</th>
                        <th>Prodi</th>
                        <th>Year</th>
                        <th>Subjects</th>
                        <th>Documents</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kurikulums as $k)
                        <tr>
                            <td style="font-weight: 600;">{{ $k->nm_kurikulum }}</td>
                            <td>{{ $k->prodi->nama_prodi }}</td>
                            <td>{{ $k->tahun_akademik }}</td>
                            <td>
                                <a href="{{ route('kurikulum.manage', $k->id) }}" style="color: var(--primary); font-weight: 500;">
                                    {{ $k->subjects_count }} Subjects
                                </a>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.25rem;">
                                    @php
                                        $docs = [
                                            'FGD' => $k->berita_acara_fgd,
                                            'Pres.' => $k->daftar_hadir,
                                            'Not.' => $k->notulensi_diskusi,
                                            'Prep.' => $k->laporan_penyusunan,
                                            'Soc.' => $k->laporan_sosialisasi,
                                            'Img' => $k->dokumentasi,
                                        ];
                                    @endphp
                                    @foreach($docs as $label => $path)
                                        @if($path)
                                            <a href="{{ asset('storage/' . $path) }}" target="_blank" title="{{ $label }}" style="text-decoration: none; font-size: 0.65rem; background: #dcfce7; color: #166534; padding: 2px 4px; border-radius: 3px;">{{ $label }}</a>
                                        @else
                                            <span title="{{ $label }} (Missing)" style="font-size: 0.65rem; background: #fee2e2; color: #991b1b; padding: 2px 4px; border-radius: 3px; cursor: help;">{{ $label }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td style="display: flex; gap: 0.5rem; align-items: center;">
                                <a href="{{ route('kurikulum.edit', $k->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</a>
                                <a href="{{ route('kurikulum.export_pdf', $k->id) }}" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #dc2626; color: white; border: none; border-radius: 4px; text-decoration: none;">Export PDF</a>
                                <form action="{{ route('kurikulum.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this curriculum?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted);">No curriculum found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
