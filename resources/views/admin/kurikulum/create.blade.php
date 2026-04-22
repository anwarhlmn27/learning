@extends('layouts.admin')

@section('title', 'Add Curriculum')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Add Curriculum</h1>
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

        <form action="{{ route('kurikulum.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Curriculum Name</label>
                    <input type="text" name="nm_kurikulum" class="form-control" placeholder="e.g. Kurikulum 2024 Informatika" required value="{{ old('nm_kurikulum') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Academic Year</label>
                    <input type="number" name="tahun_akademik" class="form-control" placeholder="2024" required value="{{ old('tahun_akademik', date('Y')) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Study Program (Prodi)</label>
                <select name="id_prodi" class="form-control" required>
                    <option value="" disabled selected>Select Prodi</option>
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}" {{ old('id_prodi') == $p->id ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>

            <h3 style="font-size: 1rem; font-weight: 600; margin-top: 2rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem;">Document Attachments (PDF)</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
                <div class="form-group">
                    <label class="form-label">Berita Acara FGD</label>
                    <input type="file" name="berita_acara_fgd" class="form-control" accept=".pdf">
                </div>
                <div class="form-group">
                    <label class="form-label">Daftar Hadir</label>
                    <input type="file" name="daftar_hadir" class="form-control" accept=".pdf">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Notulensi Diskusi</label>
                    <input type="file" name="notulensi_diskusi" class="form-control" accept=".pdf">
                </div>
                <div class="form-group">
                    <label class="form-label">Laporan Penyusunan</label>
                    <input type="file" name="laporan_penyusunan" class="form-control" accept=".pdf">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Laporan Sosialisasi</label>
                    <input type="file" name="laporan_sosialisasi" class="form-control" accept=".pdf">
                </div>
                <div class="form-group">
                    <label class="form-label">Dokumentasi (PDF/Img)</label>
                    <input type="file" name="dokumentasi" class="form-control" accept=".pdf,image/*">
                </div>
            </div>

            <div style="margin-top: 1rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">Create Curriculum</button>
            </div>
        </form>
    </div>
</div>
@endsection
