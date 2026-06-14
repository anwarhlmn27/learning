@extends('layouts.admin')

@section('title', __('Add Curriculum'))

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">{{ __('Add Curriculum') }}</h1>
@endsection

@section('content')
<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header">
        <h4 class="card-title">{{ __('Curriculum Form') }}</h4>
        <a href="{{ route('kurikulum.index') }}" class="btn btn-warning btn-sm">{{ __('Back') }}</a>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger" style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('kurikulum.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('Curriculum Name') }} <span style="color: red;">*</span></label>
                    <input type="text" name="nm_kurikulum" class="form-control" placeholder="{{ __('e.g. Kurikulum 2024 Informatika') }}" required value="{{ old('nm_kurikulum') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Academic Year') }} <span style="color: red;">*</span></label>
                    <input type="number" name="tahun_akademik" class="form-control" placeholder="{{ __('2024') }}" required value="{{ old('tahun_akademik', date('Y')) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('Study Program (Prodi)') }} <span style="color: red;">*</span></label>
                <select name="id_prodi" class="form-control" required>
                    <option value="" disabled selected>{{ __('Select Prodi') }}</option>
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}" {{ old('id_prodi') == $p->id ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>

            <h3 style="font-size: 1rem; font-weight: 600; margin-top: 2rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem;">{{ __('Document Attachments (PDF)') }}</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('Berita Acara FGD') }} </label>
                    <input type="file" name="berita_acara_fgd" class="form-control" accept=".pdf">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Daftar Hadir') }} </label>
                    <input type="file" name="daftar_hadir" class="form-control" accept=".pdf">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('Notulensi Diskusi') }} </label>
                    <input type="file" name="notulensi_diskusi" class="form-control" accept=".pdf">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Laporan Penyusunan') }} </label>
                    <input type="file" name="laporan_penyusunan" class="form-control" accept=".pdf">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('Laporan Sosialisasi') }} </label>
                    <input type="file" name="laporan_sosialisasi" class="form-control" accept=".pdf">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Dokumentasi (PDF/Img)') }}</label>
                    <input type="file" name="dokumentasi" class="form-control" accept=".pdf,image/*">
                </div>
            </div>

            <div style="margin-top: 1rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">{{ __('Save Data') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
