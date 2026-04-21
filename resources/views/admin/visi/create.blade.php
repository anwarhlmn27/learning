@extends('layouts.admin')

@section('title', 'Add Vision & Mission')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Add Vision & Mission</h1>
@endsection

@section('content')
<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header">
        <span>Vision & Mission Form</span>
        <a href="{{ route('visi.index') }}" style="font-size: 0.875rem; color: var(--text-muted);">Back</a>
    </div>
    <div class="card-body">
        <form action="{{ route('visi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Entity Type</label>
                    <select name="entity_type" id="entity_type" class="form-control" required onchange="toggleEntities()">
                        <option value="">-- Select Type --</option>
                        <option value="Univ" {{ old('entity_type') == 'Univ' ? 'selected' : '' }}>University</option>
                        <option value="Fakultas" {{ old('entity_type') == 'Fakultas' ? 'selected' : '' }}>Faculty</option>
                        <option value="Prodi" {{ old('entity_type') == 'Prodi' ? 'selected' : '' }}>Study Program</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Select Target Entity</label>
                    <select name="entity_id" id="entity_id" class="form-control @error('entity_id') is-invalid @enderror" required>
                        <option value="">-- Select Entity --</option>
                    </select>
                    @error('entity_id')
                        <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Vision (Visi)</label>
                <textarea name="visi" class="form-control @error('visi') is-invalid @enderror" rows="3" required placeholder="Enter Vision statement">{{ old('visi') }}</textarea>
                @error('visi')
                    <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Mission (Misi)</label>
                <textarea name="misi" class="form-control @error('misi') is-invalid @enderror" rows="3" required placeholder="Enter Mission statement">{{ old('misi') }}</textarea>
                @error('misi')
                    <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem 0; color: var(--primary);">Objectives (Tujuan)</h3>
                    @for($i=1; $i<=5; $i++)
                        <div class="form-group">
                            <label class="form-label">Objective {{ $i }}</label>
                            <input type="text" name="tujuan{{ $i }}" class="form-control" value="{{ old('tujuan'.$i) }}">
                        </div>
                    @endfor
                </div>
                <div>
                    <h3 style="font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem 0; color: var(--primary);">Strategies (Strategi)</h3>
                    @for($i=1; $i<=5; $i++)
                        <div class="form-group">
                            <label class="form-label">Strategy {{ $i }}</label>
                            <input type="text" name="strategi{{ $i }}" class="form-control" value="{{ old('strategi'.$i) }}">
                        </div>
                    @endfor
                </div>
            </div>

            <h3 style="font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem 0; border-top: 1px solid #e5e7eb; padding-top: 1.5rem; color: var(--primary);">Supporting Documents</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Doc. Penyusunan</label>
                    <input type="file" name="doc_penyusunan" class="form-control @error('doc_penyusunan') is-invalid @enderror" accept=".pdf">
                    @error('doc_penyusunan')
                        <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Doc. Pengesahan</label>
                    <input type="file" name="doc_pengesahan" class="form-control @error('doc_pengesahan') is-invalid @enderror" accept=".pdf">
                    @error('doc_pengesahan')
                        <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Doc. Sosialisasi</label>
                    <input type="file" name="doc_sosialisasi" class="form-control @error('doc_sosialisasi') is-invalid @enderror" accept=".pdf">
                    @error('doc_sosialisasi')
                        <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Doc. Hasil Survey</label>
                    <input type="file" name="doc_hasil_survey" class="form-control @error('doc_hasil_survey') is-invalid @enderror" accept=".pdf">
                    @error('doc_hasil_survey')
                        <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">Save Vision & Mission</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const data = {
        Univ: @json($univs->map(fn($u) => ['id' => $u->id, 'name' => $u->nama_univ])),
        Fakultas: @json($fakultas->map(fn($f) => ['id' => $f->id, 'name' => $f->nama_fakultas . ' (' . ($f->univ->nama_univ ?? '-') . ')'])),
        Prodi: @json($prodis->map(fn($p) => ['id' => $p->id, 'name' => $p->nama_prodi . ' (' . ($p->fakultas->nama_fakultas ?? '-') . ')']))
    };

    function toggleEntities() {
        const type = document.getElementById('entity_type').value;
        const select = document.getElementById('entity_id');
        select.innerHTML = '<option value="">-- Select Entity --</option>';

        if (type && data[type]) {
            data[type].forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.name;
                select.appendChild(option);
            });
        }
    }

    // Restore selection on validation error
    @if(old('entity_type'))
        toggleEntities();
        document.getElementById('entity_id').value = "{{ old('entity_id') }}";
    @endif
</script>
@endsection
