@extends('layouts.admin')

@section('title', __('Add Vision & Mission'))

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">{{ __('Add Vision & Mission') }}</h1>
@endsection

@section('content')
<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header">
        <h4 class="card-title">{{ __('Vision & Mission Form') }}</h4>
        <a href="{{ route('visi.index') }}" class="btn btn-warning btn-sm">{{ __('Back') }}</a>
    </div>
    <div class="card-body">
        <form action="{{ route('visi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('Entity Type') }} <span style="color: red;">*</span></label>
                    <select name="entity_type" id="entity_type" class="form-control" required onchange="toggleEntities()">
                        <option value="">-- {{ __('Select Type') }} --</option>
                        <option value="Univ" {{ old('entity_type') == 'Univ' ? 'selected' : '' }}>{{ __('University') }}</option>
                        <option value="Fakultas" {{ old('entity_type') == 'Fakultas' ? 'selected' : '' }}>{{ __('Faculty') }}</option>
                        <option value="Prodi" {{ old('entity_type') == 'Prodi' ? 'selected' : '' }}>{{ __('Study Program') }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Select Target Entity') }} <span style="color: red;">*</span></label>
                    <select name="entity_id" id="entity_id" class="form-control @error('entity_id') is-invalid @enderror" required>
                        <option value="">-- {{ __('Select Entity') }} --</option>
                    </select>
                    @error('entity_id')
                        <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('Vision (Visi)') }} <span style="color: red;">*</span></label>
                <textarea name="visi" class="form-control @error('visi') is-invalid @enderror" rows="3" required placeholder="{{ __('Enter Vision statement') }}">{{ old('visi') }}</textarea>
                @error('visi')
                    <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="dynamic-section" style="margin-top: 2rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="font-size: 1rem; font-weight: 600; color: var(--primary); margin: 0;">{{ __('Missions (Misi)') }}</h3>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addField('misi-container', 'misi[]', '{{ __('Enter Mission') }}')">+ {{ __('Add Item') }}</button>
                </div>
                <div id="misi-container">
                    @if(old('misi'))
                        @foreach(old('misi') as $i => $val)
                            <div class="input-group" style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                                <input type="text" name="misi[]" class="form-control" value="{{ $val }}" required placeholder="{{ __('Enter Mission') }}">
                                @if($i > 0)
                                <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()" style="padding: 0 1rem;">X</button>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="input-group" style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <input type="text" name="misi[]" class="form-control" required placeholder="{{ __('Enter Mission') }}">
                        </div>
                    @endif
                </div>
                @error('misi.*')
                    <div style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
                @error('misi')
                    <div style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="dynamic-section" style="margin-top: 2rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="font-size: 1rem; font-weight: 600; color: var(--primary); margin: 0;">{{ __('Objectives (Tujuan)') }}</h3>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addField('tujuan-container', 'tujuan[]', '{{ __('Enter Objective') }}')">+ {{ __('Add Item') }}</button>
                </div>
                <div id="tujuan-container">
                    @if(old('tujuan'))
                        @foreach(old('tujuan') as $val)
                            <div class="input-group" style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                                <input type="text" name="tujuan[]" class="form-control" value="{{ $val }}" required placeholder="{{ __('Enter Objective') }}">
                                <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()" style="padding: 0 1rem;">X</button>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="dynamic-section" style="margin-top: 2rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="font-size: 1rem; font-weight: 600; color: var(--primary); margin: 0;">{{ __('Strategies (Strategi)') }}</h3>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addField('strategi-container', 'strategi[]', '{{ __('Enter Strategy') }}')">+ {{ __('Add Item') }}</button>
                </div>
                <div id="strategi-container">
                    @if(old('strategi'))
                        @foreach(old('strategi') as $val)
                            <div class="input-group" style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                                <input type="text" name="strategi[]" class="form-control" value="{{ $val }}" required placeholder="{{ __('Enter Strategy') }}">
                                <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()" style="padding: 0 1rem;">X</button>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <h3 style="font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem 0; border-top: 1px solid #e5e7eb; padding-top: 1.5rem; color: var(--primary);">{{ __('Supporting Documents') }}</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('Doc. Penyusunan') }} </label>
                    <input type="file" name="doc_penyusunan" class="form-control @error('doc_penyusunan') is-invalid @enderror" accept=".pdf">
                    @error('doc_penyusunan')
                        <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Doc. Pengesahan') }} </label>
                    <input type="file" name="doc_pengesahan" class="form-control @error('doc_pengesahan') is-invalid @enderror" accept=".pdf">
                    @error('doc_pengesahan')
                        <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Doc. Sosialisasi') }} </label>
                    <input type="file" name="doc_sosialisasi" class="form-control @error('doc_sosialisasi') is-invalid @enderror" accept=".pdf">
                    @error('doc_sosialisasi')
                        <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Doc. Hasil Survey') }} </label>
                    <input type="file" name="doc_hasil_survey" class="form-control @error('doc_hasil_survey') is-invalid @enderror" accept=".pdf">
                    @error('doc_hasil_survey')
                        <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">{{ __('Save Data') }}</button>
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
        select.innerHTML = '<option value="">-- {{ __('Select Entity') }} --</option>';

        if (type && data[type]) {
            data[type].forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.name;
                select.appendChild(option);
            });
        }
    }

    function addField(containerId, inputName, placeholder) {
        const container = document.getElementById(containerId);
        const div = document.createElement('div');
        div.className = 'input-group';
        div.style = 'display: flex; gap: 0.5rem; margin-bottom: 0.5rem;';
        
        div.innerHTML = `
            <input type="text" name="${inputName}" class="form-control" required placeholder="${placeholder}">
            <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()" style="padding: 0 1rem;">X</button>
        `;
        container.appendChild(div);
    }

    // Restore selection on validation error
    @if(old('entity_type'))
        toggleEntities();
        document.getElementById('entity_id').value = "{{ old('entity_id') }}";
    @endif
</script>
@endsection
