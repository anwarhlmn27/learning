@extends('layouts.admin')

@section('title', __('Rencana Pembelajaran Semester (RPS)'))

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">{{ __('RPS Management') }}</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span>{{ __('Select Study Program (Prodi) to Manage RPS') }}</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Prodi Code') }}</th>
                        <th>{{ __('Study Program') }}</th>
                        <th>{{ __('Faculty') }}</th>
                        <th>{{ __('Total RPS') }}</th>
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
                                    {{ $prodi->rps_count ?? 0 }} RPS
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.rps.prodi', $prodi->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; text-decoration: none;">
                                    {{ __('Select Prodi') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                {{ __('No Study Programs found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
