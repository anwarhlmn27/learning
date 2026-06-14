@extends('layouts.admin')

@section('title', 'CPMK (CLO)')



@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>{{ __('CPMK (CLO)') }}</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Academic & OBE') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0);">{{ __('CPMK (CLO)') }}</a></li>
        </ol>
    </div>
</div>


<div class="card">
    <div class="card-header">
        <span>Select Study Program (Prodi) to Manage CLO (CPMK)</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="table table-responsive-md">
                <thead>
                    <tr>
                        <th>{{ __('Prodi Code') }}</th>
                        <th>{{ __('Study Program') }}</th>
                        <th>{{ __('Faculty') }}</th>
                        <th>{{ __('Total Subjects') }}</th>
                        <th>{{ __('Action') }}</th>
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
