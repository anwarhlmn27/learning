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
                <label class="form-label">Vision (Visi)</label>
                <textarea name="visi" class="form-control @error('visi') is-invalid @enderror" rows="3" required placeholder="Enter Vision statement">{{ old('visi', $visi->visi) }}</textarea>
                @error('visi')
                    <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Mission (Misi)</label>
                <textarea name="misi" class="form-control @error('misi') is-invalid @enderror" rows="3" required placeholder="Enter Mission statement">{{ old('misi', $visi->misi) }}</textarea>
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
                            <input type="text" name="tujuan{{ $i }}" class="form-control" value="{{ old('tujuan'.$i, $visi->{'tujuan'.$i}) }}">
                        </div>
                    @endfor
                </div>
                <div>
                    <h3 style="font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem 0; color: var(--primary);">Strategies (Strategi)</h3>
                    @for($i=1; $i<=5; $i++)
                        <div class="form-group">
                            <label class="form-label">Strategy {{ $i }}</label>
                            <input type="text" name="strategi{{ $i }}" class="form-control" value="{{ old('strategi'.$i, $visi->{'strategi'.$i}) }}">
                        </div>
                    @endfor
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
