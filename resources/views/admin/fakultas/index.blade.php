@extends('layouts.admin')

@section('title', 'Faculty Data')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Faculty Data</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span>Faculty List</span>
        <a href="{{ route('fakultas.create') }}" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">Add Faculty</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>University</th>
                        <th>Code</th>
                        <th>Abbreviation</th>
                        <th>Faculty Name</th>
                        <th>Leader (Dean)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fakultas as $f)
                    <tr>
                        <td>{{ $f->univ ? $f->univ->nama_univ : '-' }}</td>
                        <td style="font-weight: 600;">{{ $f->kode_fakultas }}</td>
                        <td>{{ $f->short_name }}</td>
                        <td>{{ $f->nama_fakultas }}</td>
                        <td>{{ $f->dekan->name ?? '-' }}</td>
                        <td style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('fakultas.edit', $f->id) }}" class="btn" style="background: #e5e7eb; color: #374151; padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</a>
                            <form action="{{ route('fakultas.destroy', $f->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">No data found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
