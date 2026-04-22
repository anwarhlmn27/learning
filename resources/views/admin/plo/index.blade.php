@extends('layouts.admin')

@section('title', 'PLO (CPL)')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Program Learning Outcomes (PLO/CPL)</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span>Study Program List</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Prodi Code</th>
                        <th>Study Program Name</th>
                        <th>Faculty</th>
                        <th>PLO Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prodis as $p)
                        <tr>
                            <td style="font-weight: 600;">{{ $p->kode_prodi }}</td>
                            <td>{{ $p->nama_prodi }}</td>
                            <td>{{ $p->fakultas->nama_fakultas ?? '-' }}</td>
                            <td>
                                <span class="badge" style="background: var(--primary-light); color: var(--primary);">{{ $p->plos_count }} PLOs</span>
                            </td>
                            <td>
                                <a href="{{ route('plo.manage', $p->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Manage PLO</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted);">No study programs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
