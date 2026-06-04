@extends('layouts.admin')

@section('title', 'Edit Curriculum')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Edit Curriculum</h1>
@endsection

@section('content')
<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header">
        <span>Curriculum Form</span>
        <a href="{{ route('kurikulum.index') }}" style="font-size: 0.875rem; color: var(--text-muted);">Back</a>
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

        <form action="{{ route('kurikulum.update', $kurikulum->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('Curriculum Name') }} <span style="color: red;">*</span></label>
                    <input type="text" name="nm_kurikulum" class="form-control" required value="{{ old('nm_kurikulum', $kurikulum->nm_kurikulum) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Academic Year') }} <span style="color: red;">*</span></label>
                    <input type="number" name="tahun_akademik" class="form-control" required value="{{ old('tahun_akademik', $kurikulum->tahun_akademik) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('Study Program (Prodi)') }} <span style="color: red;">*</span></label>
                <select name="id_prodi" class="form-control" required>
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}" {{ old('id_prodi', $kurikulum->id_prodi) == $p->id ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>

            <h3 style="font-size: 1rem; font-weight: 600; margin-top: 2rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem;">Document Attachments</h3>
            <p style="font-size: 0.75rem; color: var(--text-muted);">Leave empty to keep existing files.</p>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
                <div class="form-group">
                    <label class="form-label">Berita Acara FGD @if($kurikulum->berita_acara_fgd) <span style="color: var(--success);">✓</span> @endif</label>
                    <input type="file" name="berita_acara_fgd" class="form-control" accept=".pdf">
                </div>
                <div class="form-group">
                    <label class="form-label">Daftar Hadir @if($kurikulum->daftar_hadir) <span style="color: var(--success);">✓</span> @endif</label>
                    <input type="file" name="daftar_hadir" class="form-control" accept=".pdf">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Notulensi Diskusi @if($kurikulum->notulensi_diskusi) <span style="color: var(--success);">✓</span> @endif</label>
                    <input type="file" name="notulensi_diskusi" class="form-control" accept=".pdf">
                </div>
                <div class="form-group">
                    <label class="form-label">Laporan Penyusunan @if($kurikulum->laporan_penyusunan) <span style="color: var(--success);">✓</span> @endif</label>
                    <input type="file" name="laporan_penyusunan" class="form-control" accept=".pdf">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Laporan Sosialisasi @if($kurikulum->laporan_sosialisasi) <span style="color: var(--success);">✓</span> @endif</label>
                    <input type="file" name="laporan_sosialisasi" class="form-control" accept=".pdf">
                </div>
                <div class="form-group">
                    <label class="form-label">Dokumentasi @if($kurikulum->dokumentasi) <span style="color: var(--success);">✓</span> @endif</label>
                    <input type="file" name="dokumentasi" class="form-control" accept=".pdf,image/*">
                </div>
            </div>

            <div style="margin-top: 1rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">Update Curriculum</button>
            </div>
        </form>
    </div>
</div>
@endsection
