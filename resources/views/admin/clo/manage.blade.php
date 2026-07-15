@extends('layouts.admin')

@section('title', 'Manage CLO - ' . $subject->nama_subject)

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Manage CLO: {{ $subject->kode_subject }} — {{ $subject->nama_subject }}</h1>
@endsection

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('clo.index') }}" class="btn btn-warning btn-sm">Back to Subject List</a>
</div>

{{-- Subject Info Card --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body" style="padding: 1rem 1.5rem;">
        <div style="display: flex; gap: 1.5rem 2.5rem; flex-wrap: wrap;">
            <div>
                <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Subject Code</span>
                <div style="font-weight: 700; font-size: 1rem; color: var(--primary);">{{ $subject->kode_subject }}</div>
            </div>
            <div>
                <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Subject Name</span>
                <div style="font-weight: 600;">{{ $subject->nama_subject }}</div>
            </div>
            <div>
                <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">SKS</span>
                <div>{{ $subject->total_sks }} SKS (T:{{ $subject->sks_t }} / P:{{ $subject->sks_p }})</div>
            </div>
            <div>
                <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Semester</span>
                <div>Semester {{ $subject->semester }}</div>
            </div>
        </div>
    </div>
</div>

{{-- CLO List Card --}}
<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
        <span>CLO List
            <span style="font-size: 0.75rem; font-weight: 400; color: var(--text-muted); margin-left: 0.5rem;">
                {{ $subject->clos->count() }} CLO{{ $subject->clos->count() != 1 ? 's' : '' }} defined
            </span>
        </span>
        <button onclick="showCloModal()" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">+ Add CLO</button>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="table table-responsive-md">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 10%;">{{ __('CLO Code') }}</th>
                        <th style="width: 40%;">Description & Bloom Level</th>
                        <th style="width: 15%;">{{ __('Mapped PLO') }}</th>
                        <th style="width: 10%;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subject->clos as $i => $clo)
                        <tr>
                            <td style="color: var(--text-muted); font-size: 0.75rem;">{{ $i + 1 }}</td>
                            <td>
                                <span style="font-weight: 700; color: var(--primary); background: #ede9fe; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">
                                    {{ $clo->kode_clo }}
                                </span>
                            </td>
                            <td>
                                <div style="font-size: 0.875rem; margin-bottom: 0.25rem;">{{ $clo->deskripsi }}</div>
                                <span style="font-size: 0.7rem; background: #f3f4f6; color: #4b5563; padding: 2px 6px; border-radius: 4px;">{{ $clo->bloom_level }}</span>
                            </td>
                            <td>
                                @if($clo->plos->count() > 0)
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                        @foreach($clo->plos as $plo)
                                            <span style="font-size: 0.75rem; background: #f3f4f6; color: #374151; padding: 2px 6px; border-radius: 4px; border: 1px solid #e5e7eb; font-weight: 600;">
                                                {{ $plo->kode_plo }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.8rem;">— Not mapped —</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.4rem; flex-wrap: nowrap;">
                                    @php
                                        // Pluck IDs for edit mapping
                                        $mappedPlos = $clo->plos->pluck('id')->toArray();
                                    @endphp
                                    <button onclick='editClo({!! json_encode($clo) !!}, {!! json_encode($mappedPlos) !!})' class="btn btn-primary" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">Edit</button>
                                    <form action="{{ route('clo.destroy', $clo->id) }}" method="POST" class="swal-confirm-form" data-swal-msg="Delete CLO {{ $clo->kode_clo }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                No CLOs defined for this subject yet. Click <strong>+ Add CLO</strong> to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- CLO MODAL --}}
<div id="cloModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 620px; margin: 0; max-height: 90vh; overflow-y: auto;">
        <div class="card-header">
            <span id="modalTitle">Add CLO</span>
            <button onclick="closeCloModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <div class="card-body">
            <form id="cloForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">{{ __('CLO Code') }} <span style="color: red;">*</span></label>
                        <input type="text"
                               name="kode_clo"
                               id="field_kode_clo"
                               class="form-control @error('kode_clo') is-invalid @enderror"
                               required
                               placeholder="{{ __('e.g. CLO-01, CPMK-1') }}"
                               maxlength="50">
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('Bloom Level') }} <span style="color: red;">*</span></label>
                        <select name="bloom_level" id="field_bloom_level" class="form-control" required>
                            <option value="">- Select Level -</option>
                            <option value="C1 (Mengingat)">C1 (Mengingat)</option>
                            <option value="C2 (Memahami)">C2 (Memahami)</option>
                            <option value="C3 (Mengaplikasikan)">C3 (Mengaplikasikan)</option>
                            <option value="C4 (Menganalisis)">C4 (Menganalisis)</option>
                            <option value="C5 (Mengevaluasi)">C5 (Mengevaluasi)</option>
                            <option value="C6 (Mencipta)">C6 (Mencipta)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">Description / Learning Outcome <span style="color: red;">*</span></label>
                    <textarea name="deskripsi"
                              id="field_deskripsi"
                              class="form-control @error('deskripsi') is-invalid @enderror"
                              rows="3"
                              required
                              placeholder="{{ __('Describe the specific learning outcome for this course...') }}"></textarea>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">{{ __('Mapped PLO') }} <span style="color: red;">*</span></label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; max-height: 150px; overflow-y: auto; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background: #f9fafb;">
                        @forelse($plos as $plo)
                            <label style="display: flex; gap: 0.5rem; font-size: 0.85rem; cursor: pointer; align-items: flex-start;">
                                <input type="checkbox" name="plos[]" value="{{ $plo->id }}" class="plo-checkbox" style="margin-top: 0.2rem;">
                                <span>
                                    <strong>{{ $plo->kode_plo }}</strong>
                                    <span style="display: block; font-size: 0.75rem; color: var(--text-muted);">{{ Str::limit($plo->plo_title, 40) }}</span>
                                </span>
                            </label>
                        @empty
                            <div style="grid-column: span 2; color: var(--danger); font-size: 0.8rem; padding: 0.5rem; text-align: center;">
                                ⚠️ Belum ada CPL (PLO) yang dipetakan ke mata kuliah ini. <br>
                                Harap petakan CPL ke mata kuliah terlebih dahulu di menu <strong>Mapping Subject</strong>.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem; border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                    <button type="button" onclick="closeCloModal()" class="btn" style="background: #e5e7eb; color: #374151;">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save CLO</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    const storeUrl = "{{ route('clo.store', $subject->id) }}";

    function showCloModal() {
        document.getElementById('modalTitle').textContent = 'Add CLO';
        document.getElementById('cloForm').action = storeUrl;
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('field_kode_clo').value = '';
        document.getElementById('field_bloom_level').value = '';
        document.getElementById('field_deskripsi').value = '';
        
        // Uncheck all checkboxes
        document.querySelectorAll('.plo-checkbox').forEach(cb => cb.checked = false);

        document.getElementById('submitBtn').textContent = 'Save CLO';
        document.getElementById('cloModal').style.display = 'flex';
    }

    function editClo(clo, mappedPlos) {
        document.getElementById('modalTitle').textContent = 'Edit CLO: ' + clo.kode_clo;
        let updateUrl = "{{ route('clo.update', ':id') }}";
        document.getElementById('cloForm').action = updateUrl.replace(':id', clo.id);
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('field_kode_clo').value = clo.kode_clo;
        document.getElementById('field_bloom_level').value = clo.bloom_level;
        document.getElementById('field_deskripsi').value = clo.deskripsi;
        
        // Check mapped checkboxes
        document.querySelectorAll('.plo-checkbox').forEach(cb => {
            cb.checked = mappedPlos.includes(cb.value);
        });

        document.getElementById('submitBtn').textContent = 'Update CLO';
        document.getElementById('cloModal').style.display = 'flex';
    }

    function closeCloModal() {
        document.getElementById('cloModal').style.display = 'none';
    }

    // Close modal on backdrop click
    document.getElementById('cloModal').addEventListener('click', function(e) {
        if (e.target === this) closeCloModal();
    });

    // Auto-open modal on validation errors
    @if($errors->any() && !$errors->has('error'))
        showCloModal();
    @endif
</script>
@endsection
@endsection
