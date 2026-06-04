@extends('layouts.admin')

@section('title', 'University Data')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">University Data</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span>University List</span>
        <a href="{{ route('univ.create') }}" class="btn btn-primary">Add University</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('University Name') }}</th>
                        <th>Leader (Rector)</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Website') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($univs as $u)
                        <tr>
                            <td style="font-weight: 600;">{{ $u->kode_univ }}</td>
                            <td>{{ $u->nama_univ }}</td>
                            <td>{{ $u->rektor->name ?? '-' }}</td>
                            <td>{{ $u->email }}</td>
                            <td><a href="{{ $u->website }}" target="_blank" style="color: var(--primary);">Link</a></td>
                            <td style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('univ.edit', $u->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</a>
                                <form action="{{ route('univ.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted);">No data found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
