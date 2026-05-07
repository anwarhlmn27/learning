@extends('layouts.admin')

@section('title', 'Add Subject')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Add Subject</h1>
@endsection

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <span>Subject Form</span>
        <a href="{{ route('subjects.index') }}" style="font-size: 0.875rem; color: var(--text-muted);">Back</a>
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

        <form action="{{ route('subjects.store') }}" method="POST">
            @csrf
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Program Studi</label>
                <select name="id_prodi" class="form-control" required>
                    <option value="">-- Select Program Studi --</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}" {{ old('id_prodi', $selected_prodi_id) == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama_prodi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Subject Code</label>
                    <input type="text" name="kode_subject" class="form-control" placeholder="INF101" required value="{{ old('kode_subject') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Subject Name</label>
                    <input type="text" name="nama_subject" class="form-control" placeholder="Introduction to Computer Science" required value="{{ old('nama_subject') }}">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3" required placeholder="Isi dan tujuan MK">{{ old('deskripsi') }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">SKS Theory (T)</label>
                    <input type="number" name="sks_t" id="sks_t" class="form-control" min="0" required value="{{ old('sks_t', 0) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">SKS Practice (P)</label>
                    <input type="number" name="sks_p" id="sks_p" class="form-control" min="0" required value="{{ old('sks_p', 0) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Total SKS</label>
                    <input type="number" name="total_sks" id="total_sks" class="form-control" readonly value="{{ old('total_sks', 0) }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-control" required>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Jenis Subject</label>
                    <select name="jenis_subject" class="form-control" required>
                        <option value="Wajib Prodi" {{ old('jenis_subject') == 'Wajib Prodi' ? 'selected' : '' }}>Wajib Prodi</option>
                        <option value="Wajib Universitas" {{ old('jenis_subject') == 'Wajib Universitas' ? 'selected' : '' }}>Wajib Universitas</option>
                        <option value="Pilihan" {{ old('jenis_subject') == 'Pilihan' ? 'selected' : '' }}>Pilihan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Revisi" {{ old('status') == 'Revisi' ? 'selected' : '' }}>Revisi</option>
                        <option value="Tidak Aktif" {{ old('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Prerequisite Subject</label>
                <select name="prerequisite_id" class="form-control">
                    <option value="">No Prerequisite</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ old('prerequisite_id') == $s->id ? 'selected' : '' }}>
                            [{{ $s->kode_subject }}] {{ $s->nama_subject }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Bahan Kajian (Mapping)</label>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background: #f9fafb; max-height: 150px; overflow-y: auto;">
                    @foreach($bks as $bk)
                        <label style="display: flex; align-items: flex-start; gap: 0.4rem; font-size: 0.85rem; cursor: pointer;">
                            <input type="checkbox" name="bks[]" value="{{ $bk->id }}" {{ is_array(old('bks')) && in_array($bk->id, old('bks')) ? 'checked' : '' }}
                                style="accent-color: var(--primary); width: 16px; height: 16px; margin-top: 0.2rem;">
                            <span><strong>{{ $bk->kode_bk }}</strong><br><span style="color: var(--text-muted); font-size: 0.75rem;">{{ $bk->nm_bahan_kajian }}</span></span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">PLO (Mapping)</label>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background: #f9fafb; max-height: 150px; overflow-y: auto;">
                    @foreach($plos as $plo)
                        <label style="display: flex; align-items: flex-start; gap: 0.4rem; font-size: 0.85rem; cursor: pointer;">
                            <input type="checkbox" name="plos[]" value="{{ $plo->id }}" {{ is_array(old('plos')) && in_array($plo->id, old('plos')) ? 'checked' : '' }}
                                style="accent-color: var(--primary); width: 16px; height: 16px; margin-top: 0.2rem;">
                            <span><strong>{{ $plo->kode_plo }}</strong><br><span style="color: var(--text-muted); font-size: 0.75rem;">{{ $plo->plo_title }}</span></span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="margin-top: 1rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">Save Subject</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sksT = document.getElementById('sks_t');
        const sksP = document.getElementById('sks_p');
        const totalSks = document.getElementById('total_sks');

        function calculateTotal() {
            totalSks.value = (parseInt(sksT.value) || 0) + (parseInt(sksP.value) || 0);
        }

        sksT.addEventListener('input', calculateTotal);
        sksP.addEventListener('input', calculateTotal);
    });
</script>
@endsection
