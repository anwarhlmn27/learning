@extends('layouts.admin')

@section('title', 'Graduate Profile')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Graduate Profile</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span>Study Program List</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table>
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
                                <span class="badge" style="background: var(--primary-light); color: var(--primary);">{{ $p->gps_count }} Items</span>
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
