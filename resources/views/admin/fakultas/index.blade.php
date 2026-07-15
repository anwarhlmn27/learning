@extends('layouts.admin')

@section('title', __('Faculty Data'))



@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>{{ __('Faculty Data') }}</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Institution') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0);">{{ __('Faculty') }}</a></li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span>{{ __('Faculty List') }}</span>
        <a href="{{ route('fakultas.create') }}" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">Add Faculty</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="table table-responsive-md">
                <thead>
                    <tr>
                        <th>{{ __('University') }}</th>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Abbreviation') }}</th>
                        <th>{{ __('Faculty Name') }}</th>
                        <th>{{ __('Leader (Dean)') }}</th>
                        <th>{{ __('Actions') }}</th>
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
                            <form action="{{ route('fakultas.destroy', $f->id) }}" method="POST" class="swal-confirm-form" data-swal-msg="Are you sure you want to delete this?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">{{ __('Delete') }}</button>
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
