@extends('layouts.admin')

@section('title', 'Subjects / Courses')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Subjects Data</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span>Subject List</span>
        <a href="{{ route('subjects.create') }}" class="btn btn-primary">Add Subject</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Subject Name</th>
                        <th>SKS (T/P/Total)</th>
                        <th>Sem.</th>
                        <th>Prerequisite</th>
                        <th>Assessment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $s)
                        <tr>
                            <td style="font-weight: 600;">{{ $s->kode_subject }}</td>
                            <td>{{ $s->nama_subject }}</td>
                            <td>{{ $s->sks_t }} / {{ $s->sks_p }} / {{ $s->total_sks }}</td>
                            <td>{{ $s->semester }}</td>
                            <td>
                                @if($s->prerequisite)
                                    <span title="{{ $s->prerequisite->nama_subject }}">{{ $s->prerequisite->kode_subject }}</span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.75rem;">-</span>
                                @endif
                            </td>
                            <td><span style="font-size: 0.75rem; background: #f3f4f6; padding: 2px 6px; border-radius: 4px;">{{ $s->assesment_type }}</span></td>
                            <td style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('subjects.edit', $s->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</a>
                                <form action="{{ route('subjects.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subject?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted);">No subjects found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
