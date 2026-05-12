@extends('layouts.admin')

@section('title', 'CPMK (CLO)')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Course Learning Outcomes (CPMK / CLO)</h1>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <span>Select Study Program (Prodi) to Manage CLO (CPMK)</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Prodi Code</th>
                        <th>Study Program</th>
                        <th>Faculty</th>
                        <th>Total Subjects</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prodis as $prodi)
                        <tr>
                            <td style="font-weight: 600;">{{ $prodi->kode_prodi }}</td>
                            <td>{{ $prodi->nama_prodi }}</td>
                            <td>{{ $prodi->fakultas->nama_fakultas ?? '-' }}</td>
                            <td>
                                <span class="badge" style="background: #e0e7ff; color: #4338ca; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                    {{ $prodi->subjects_count ?? 0 }} Subjects
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('clo.prodi', $prodi->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; text-decoration: none;">
                                    Select Prodi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                No Study Programs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
