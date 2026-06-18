@extends('layouts.admin')

@section('title', 'Graduate Profile')



@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>{{ __('Graduate Profile (GP)') }}</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Academic & OBE') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0);">{{ __('Graduate Profile (GP)') }}</a></li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span>Study Program List</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="table table-responsive-md">
                <thead>
                    <tr>
                        <th>{{ __('Prodi Code') }}</th>
                        <th>{{ __('Study Program Name') }}</th>
                        <th>{{ __('Faculty') }}</th>
                        <th>{{ __('Profile Items') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prodis as $p)
                        <tr>
                            <td style="font-weight: 600;">{{ $p->kode_prodi }}</td>
                            <td>{{ $p->nama_prodi }}</td>
                            <td>{{ $p->fakultas->nama_fakultas ?? '-' }}</td>
                            <td>
                                <span class="badge" style="background: #e0e7ff !important; color: #4338ca !important; padding: 0.35em 0.65em; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">{{ $p->gps_count }} Items</span>
                            </td>
                            <td>
                                <a href="{{ route('gp.manage', $p->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Manage Profiles & Docs</a>
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
