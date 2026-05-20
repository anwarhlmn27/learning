@extends('layouts.admin')

@section('title', 'Assessment Types Management')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Assessment Types</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span>Assessment Types Master Data</span>
    </div>
    <div class="card-body">
        <form action="{{ route('assessment_types.store') }}" method="POST" style="margin-bottom: 2rem;">
            @csrf
            <div style="display: flex; gap: 1rem; align-items: flex-end;">
                <div style="flex: 1;">
                    <label class="form-label">Type Name (e.g. Quiz, Project, UAS) <span style="color: red;">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="height: 38px;">+ Add Type</button>
                </div>
            </div>
        </form>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>Type Name</th>
                        <th style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($types as $index => $type)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <form action="{{ route('assessment_types.update', $type->id) }}" method="POST" style="display: flex; gap: 0.5rem; margin: 0;">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $type->name }}" class="form-control" style="padding: 0.25rem 0.5rem;" required>
                                    <button type="submit" class="btn btn-success" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">Update</button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('assessment_types.destroy', $type->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this type?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                No Assessment Types found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
