@extends('layouts.admin')

@section('title', __('Curriculum Data'))



@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>{{ __('Curriculum') }}</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Academic & OBE') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0);">{{ __('Curriculum') }}</a></li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span>{{ __('Curriculum List') }}</span>
        <a href="{{ route('kurikulum.create') }}" class="btn btn-primary">{{ __('Add Curriculum') }}</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="table table-responsive-md">
                <thead>
                    <tr>
                        <th>{{ __('Curriculum Name') }}</th>
                        <th>{{ __('Prodi') }}</th>
                        <th>{{ __('Year') }}</th>
                        <th>{{ __('Subjects') }}</th>
                        <th>{{ __('Documents') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kurikulums as $k)
                        <tr>
                            <td style="font-weight: 600;">{{ $k->nm_kurikulum }}</td>
                            <td>{{ $k->prodi->nama_prodi }}</td>
                            <td>{{ $k->tahun_akademik }}</td>
                            <td>
                                <a href="{{ route('kurikulum.manage', $k->id) }}" style="color: var(--primary); font-weight: 500;">
                                    {{ $k->subjects_count }} {{ __('Subjects') }}
                                </a>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.25rem;">
                                    @php
                                        $docs = [
                                            'FGD' => $k->berita_acara_fgd,
                                            'Pres.' => $k->daftar_hadir,
                                            'Not.' => $k->notulensi_diskusi,
                                            'Prep.' => $k->laporan_penyusunan,
                                            'Soc.' => $k->laporan_sosialisasi,
                                            'Img' => $k->dokumentasi,
                                        ];
                                    @endphp
                                    @foreach($docs as $label => $path)
                                        @if($path)
                                            <a href="{{ asset('storage/' . $path) }}" target="_blank" title="{{ $label }}" style="text-decoration: none; font-size: 0.65rem; background: #dcfce7; color: #166534; padding: 2px 4px; border-radius: 3px;">{{ $label }}</a>
                                        @else
                                            <span title="{{ $label }} (Missing)" style="font-size: 0.65rem; background: #fee2e2; color: #991b1b; padding: 2px 4px; border-radius: 3px; cursor: help;">{{ $label }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td style="display: flex; gap: 0.5rem; align-items: center;">
                                <a href="{{ route('kurikulum.edit', $k->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">{{ __('Edit') }}</a>
                                <a href="{{ route('kurikulum.export_pdf', $k->id) }}" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #dc2626; color: white; border: none; border-radius: 4px; text-decoration: none;">{{ __('Export PDF') }}</a>
                                <form action="{{ route('kurikulum.destroy', $k->id) }}" method="POST" class="swal-confirm-form" data-swal-msg="{{ __('Are you sure you want to delete this?') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted);">{{ __('No data found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
