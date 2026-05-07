@extends('layouts.admin')

@section('title', 'Bahan Kajian (BK) Management')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Bahan Kajian (BK)</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span>Select Study Program (Prodi) to Manage Bahan Kajian</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Prodi Code</th>
                        <th>Study Program</th>
                        <th>Faculty</th>
                        <th>Total BK Items</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prodis as $prodi)
                        <tr>
                            <td style="font-weight: 600;">{{ $prodi->kode_prodi }}</td>
                            <td>{{ $prodi->nama_prodi }}</td>
                            <td>{{ $prodi->fakultas->nama_fakultas }}</td>
                            <td>
                                <span class="badge" style="background: #e0e7ff; color: #4338ca; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                    {{ $prodi->bahan_kajians_count ?? 0 }} Items
                                </span>
                            </td>
                            <td style="display: flex; gap: 0.5rem; align-items: center;">
                                <a href="{{ route('bahan_kajian.manage', $prodi->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; text-decoration: none;">
                                    Manage BK
                                </a>
                                <a href="{{ route('bahan_kajian.export_pdf', $prodi->id) }}" target="_blank" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #dc2626; color: white; border: none; border-radius: 4px; text-decoration: none;">
                                    Mapping PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                No Study Programs found. Please add a Study Program first.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
