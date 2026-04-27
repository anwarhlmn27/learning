@extends('layouts.admin')

@section('title', 'Study Program Data')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Study Program Data</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span>Study Program List</span>
        <a href="{{ route('prodi.create') }}" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">Add Study Program</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>University</th>
                        <th>Faculty</th>
                        <th>Code</th>
                        <th>Abbreviation</th>
                        <th>Study Program Name</th>
                        <th>Leader (Head of SP)</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prodis as $p)
                    <tr>
                        <td>{{ ($p->fakultas && $p->fakultas->univ) ? $p->fakultas->univ->nama_univ : '-' }}</td>
                        <td>{{ $p->fakultas ? $p->fakultas->nama_fakultas : '-' }}</td>
                        <td style="font-weight: 600;">{{ $p->kode_prodi }}</td>
                        <td>{{ $p->short_name }}</td>
                        <td>{{ $p->nama_prodi }}</td>
                        <td>{{ $p->nama_pimpinan ?? '-' }}</td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('prodi.edit', $p->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</a>
                                <form action="{{ route('prodi.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">No data found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
