@extends('layouts.admin')

@section('title', __('University Data'))



@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>{{ __('University Data') }}</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Institution') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0);">{{ __('University') }}</a></li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span>{{ __('University List') }}</span>
        <a href="{{ route('univ.create') }}" class="btn btn-primary">{{ __('Add University') }}</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="table table-responsive-md">
                <thead>
                    <tr>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('University Name') }}</th>
                        <th>{{ __('Leader (Rector)') }}</th>
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
                                <a href="{{ route('univ.edit', $u->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">{{ __('Edit') }}</a>
                                <form action="{{ route('univ.destroy', $u->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted);">{{ __('No data found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
