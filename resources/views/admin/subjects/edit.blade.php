@extends('layouts.admin')

@section('title', 'Edit Subject')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Edit Subject</h1>
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

        <form action="{{ route('subjects.update', $subject->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Subject Code</label>
                    <input type="text" name="kode_subject" class="form-control" placeholder="INF101" required value="{{ old('kode_subject', $subject->kode_subject) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Subject Name</label>
                    <input type="text" name="nama_subject" class="form-control" placeholder="Introduction to Computer Science" required value="{{ old('nama_subject', $subject->nama_subject) }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">SKS Theory (T)</label>
                    <input type="number" name="sks_t" id="sks_t" class="form-control" min="0" required value="{{ old('sks_t', $subject->sks_t) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">SKS Practice (P)</label>
                    <input type="number" name="sks_p" id="sks_p" class="form-control" min="0" required value="{{ old('sks_p', $subject->sks_p) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Total SKS</label>
                    <input type="number" name="total_sks" id="total_sks" class="form-control" readonly value="{{ old('total_sks', $subject->total_sks) }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-control" required>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('semester', $subject->semester) == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Assessment Type <span style="color: #dc2626;">*</span></label>
                @error('assesment_type')
                    <div style="color: #dc2626; font-size: 0.75rem; margin-bottom: 0.5rem;">{{ $message }}</div>
                @enderror
                @php
                    $selectedTypes = old('assesment_type', $subject->assesment_type ?? []);
                    if (is_string($selectedTypes)) {
                        $selectedTypes = json_decode($selectedTypes, true) ?? [$selectedTypes];
                    }
                @endphp
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; padding: 0.75rem; border: 1px solid {{ $errors->has('assesment_type') ? '#dc2626' : '#d1d5db' }}; border-radius: 0.375rem; background: #f9fafb;">
                    @foreach($assessmentTypes as $type)
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; cursor: pointer; padding: 0.25rem 0;">
                            <input type="checkbox" name="assesment_type[]" value="{{ $type }}" {{ is_array($selectedTypes) && in_array($type, $selectedTypes) ? 'checked' : '' }}
                                style="accent-color: var(--primary); width: 16px; height: 16px;">
                            {{ $type }}
                        </label>
                    @endforeach
                </div>
                <small style="color: var(--text-muted);">Pilih satu atau lebih assessment type.</small>
            </div>

            <div class="form-group">
                <label class="form-label">Prerequisite Subject</label>
                <select name="prerequisite_id" class="form-control">
                    <option value="">No Prerequisite</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ old('prerequisite_id', $subject->prerequisite_id) == $s->id ? 'selected' : '' }}>
                            [{{ $s->kode_subject }}] {{ $s->nama_subject }}
                        </option>
                    @endforeach
                </select>
                <small style="color: var(--text-muted);">Select the subject that must be taken before this one.</small>
            </div>

            <div style="margin-top: 1rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">Update Subject</button>
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
