@extends('layouts.admin')

@section('title', 'Edit Vision & Mission')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Edit Vision & Mission</h1>
@endsection

@section('content')
<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header">
        <span>Edit Vision & Mission Form</span>
        <a href="{{ route('visi.index') }}" style="font-size: 0.875rem; color: var(--text-muted);">Back</a>
    </div>
    <div class="card-body">
        <form action="{{ route('visi.update', $visi->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Entity Type</label>
                    <input type="text" class="form-control" value="{{ str_replace('App\\Models\\', '', $visi->visible_type) }}" readonly disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Entity Name</label>
                    <input type="text" class="form-control" value="{{ $visi->visible->nama_univ ?? $visi->visible->nama_fakultas ?? $visi->visible->nama_prodi ?? 'N/A' }}" readonly disabled>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Vision (Visi) <span style="color: red;">*</span></label>
                <textarea name="visi" class="form-control @error('visi') is-invalid @enderror" rows="3" required placeholder="Enter Vision statement">{{ old('visi', $visi->visi) }}</textarea>
                @error('visi')
                    <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            @php
                $oldMisi = old('misi') ? old('misi') : $visi->details->where('type', 'misi')->sortBy('urutan')->pluck('konten')->toArray();
                if (empty($oldMisi)) $oldMisi = [''];

                $oldTujuan = old('tujuan') ? old('tujuan') : $visi->details->where('type', 'tujuan')->sortBy('urutan')->pluck('konten')->toArray();
                $oldStrategi = old('strategi') ? old('strategi') : $visi->details->where('type', 'strategi')->sortBy('urutan')->pluck('konten')->toArray();
            @endphp

            <div class="dynamic-section" style="margin-top: 2rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="font-size: 1rem; font-weight: 600; color: var(--primary); margin: 0;">Missions (Misi)</h3>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addField('misi-container', 'misi[]', 'Enter Mission')">+ Add Misi</button>
                </div>
                <div id="misi-container">
                    @foreach($oldMisi as $i => $val)
                        <div class="input-group" style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <input type="text" name="misi[]" class="form-control" value="{{ $val }}" required placeholder="Enter Mission">
                            @if($i > 0)
                            <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()" style="padding: 0 1rem;">X</button>
                            @endif
                        </div>
                    @endforeach
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
                    <h3 style="font-size: 1rem; font-weight: 600; color: var(--primary); margin: 0;">Objectives (Tujuan)</h3>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addField('tujuan-container', 'tujuan[]', 'Enter Objective')">+ Add Tujuan</button>
                </div>
                <div id="tujuan-container">
                    @foreach($oldTujuan as $val)
                        <div class="input-group" style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <input type="text" name="tujuan[]" class="form-control" value="{{ $val }}" required placeholder="Enter Objective">
                            <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()" style="padding: 0 1rem;">X</button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="dynamic-section" style="margin-top: 2rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="font-size: 1rem; font-weight: 600; color: var(--primary); margin: 0;">Strategies (Strategi)</h3>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addField('strategi-container', 'strategi[]', 'Enter Strategy')">+ Add Strategi</button>
                </div>
                <div id="strategi-container">
                    @foreach($oldStrategi as $val)
                        <div class="input-group" style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <input type="text" name="strategi[]" class="form-control" value="{{ $val }}" required placeholder="Enter Strategy">
                            <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()" style="padding: 0 1rem;">X</button>
                        </div>
                    @endforeach
                </div>
            </div>

            <h3 style="font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem 0; border-top: 1px solid #e5e7eb; padding-top: 1.5rem; color: var(--primary);">Supporting Documents</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                @php
                    $docs = [
                        'doc_penyusunan' => 'Doc. Penyusunan',
                        'doc_pengesahan' => 'Doc. Pengesahan',
                        'doc_sosialisasi' => 'Doc. Sosialisasi',
                        'doc_hasil_survey' => 'Doc. Hasil Survey'
                    ];
                @endphp
                @foreach($docs as $field => $label)
                    <div class="form-group">
                        <label class="form-label">{{ $label }}</label>
                        @if($visi->$field)
                            <div style="margin-bottom: 0.5rem; font-size: 0.75rem;">
                                <a href="{{ asset('storage/' . $visi->$field) }}" target="_blank" style="color: var(--primary);">Current File</a>
                            </div>
                        @endif
                        <input type="file" name="{{ $field }}" class="form-control @error($field) is-invalid @enderror" accept=".pdf">
                        @error($field)
                            <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">Update Vision & Mission</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
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
</script>
@endsection
