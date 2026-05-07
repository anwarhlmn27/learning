@extends('layouts.admin')

@section('title', 'Manage Mapping - ' . $prodi->nama_prodi)

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Manage Mapping: {{ $prodi->nama_prodi }}</h1>
@endsection

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('course_mapping.index') }}" style="color: var(--text-muted); font-size: 0.875rem;">← Back to List</a>
</div>

{{-- Filter Bar --}}
<div class="card" style="margin-bottom: 1.25rem;">
    <div class="card-body" style="padding: 1rem 1.25rem;">
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.25rem; text-transform: uppercase;">Search</label>
                <input type="text" id="filterSearch" onkeyup="applyFilters()"
                       placeholder="Subject or PLO…"
                       style="width: 100%; padding: 0.45rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none; box-sizing: border-box;">
            </div>
            <div style="min-width: 140px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.25rem; text-transform: uppercase;">Level</label>
                <select id="filterLevel" onchange="applyFilters()"
                        style="width: 100%; padding: 0.45rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: #fff; outline: none;">
                    <option value="">All Levels</option>
                    <option value="I">I - Introduced</option>
                    <option value="R">R - Reinforced</option>
                    <option value="M">M - Mastered</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button onclick="resetFilters()"
                        style="padding: 0.45rem 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: #f9fafb; cursor: pointer; color: var(--text-muted); font-weight: 600;">
                    ↺ Reset
                </button>
            </div>
        </div>
        <div id="filterInfo" style="margin-top: 0.6rem; font-size: 0.75rem; color: var(--text-muted); display: none;">
            Showing <strong id="filterCount">0</strong> of <strong id="filterTotal">0</strong> mappings
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
        <span>Curriculum Mapping List</span>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('course_mapping.export_pdf', $prodi->id) }}" class="btn btn-success" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;" target="_blank">Export PDF</a>
            <button onclick="showMappingModal()" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">+ Add Mapping</button>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table id="mappingTable">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>PLO</th>
                        <th>Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="mappingTbody">
                    @forelse($prodi->courseMapings as $maping)
                        <tr class="mapping-row" 
                            data-subject="{{ strtolower($maping->subject->nama_subject . ' ' . $maping->subject->kode_subject) }}"
                            data-plo="{{ strtolower($maping->plo->kode_plo . ' ' . $maping->plo->plo_title) }}"
                            data-level="{{ $maping->level_maping }}">
                            <td>
                                <div style="font-weight: 600;">{{ $maping->subject->nama_subject }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $maping->subject->kode_subject }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $maping->plo->kode_plo }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ Str::limit($maping->plo->plo_title, 50) }}</div>
                            </td>
                            <td>
                                <span class="badge" style="background: #f3f4f6; color: #374151; padding: 0.25rem 0.5rem; font-weight: bold;">{{ $maping->level_maping }}</span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button onclick='editMapping({!! json_encode($maping) !!})' class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</button>
                                    <form action="{{ route('course_mapping.destroy', $maping->id) }}" method="POST" onsubmit="return confirm('Delete this mapping?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="noDataRow"><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">No mappings added yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div id="noResultRow" style="display:none; text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.875rem;">
                No mappings match the filter.
            </div>
        </div>
    </div>
</div>

<!-- MAPPING MODAL -->
<div id="mappingModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 600px; margin: 0; max-height: 90vh; overflow-y: auto;">
        <div class="card-header">
            <span id="modalTitle">Add Curriculum Mapping</span>
            <button onclick="closeMappingModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <div class="card-body">
            <form id="mappingForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="form-group">
                    <label class="form-label">Subject (Mata Kuliah)</label>
                    <select name="id_subject" id="field_id_subject" class="form-control @error('id_subject') is-invalid @enderror" required>
                        <option value="">-- Select Subject --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('id_subject') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->kode_subject }} - {{ $subject->nama_subject }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">PLO (CPL)</label>
                    <select name="id_plo" id="field_id_plo" class="form-control @error('id_plo') is-invalid @enderror" required>
                        <option value="">-- Select PLO --</option>
                        @foreach($plos as $plo)
                            <option value="{{ $plo->id }}" {{ old('id_plo') == $plo->id ? 'selected' : '' }}>
                                {{ $plo->kode_plo }} - {{ Str::limit($plo->plo_title, 50) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Mapping Level</label>
                    <select name="level_maping" id="field_level_maping" class="form-control @error('level_maping') is-invalid @enderror" required>
                        <option value="">-- Select Level --</option>
                        <option value="I" {{ old('level_maping') == 'I' ? 'selected' : '' }}>I - Introduced</option>
                        <option value="R" {{ old('level_maping') == 'R' ? 'selected' : '' }}>R - Reinforced</option>
                        <option value="M" {{ old('level_maping') == 'M' ? 'selected' : '' }}>M - Mastered</option>
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                    <button type="button" onclick="closeMappingModal()" class="btn" style="background: #e5e7eb;">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Mapping</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    const totalRows = document.querySelectorAll('.mapping-row').length;
    if(document.getElementById('filterTotal')) document.getElementById('filterTotal').textContent = totalRows;

    function applyFilters() {
        const search = document.getElementById('filterSearch').value.toLowerCase().trim();
        const level = document.getElementById('filterLevel').value;
        const rows = document.querySelectorAll('.mapping-row');
        let visible = 0;

        rows.forEach(row => {
            const matchSearch = !search || row.dataset.subject.includes(search) || row.dataset.plo.includes(search);
            const matchLevel = !level || row.dataset.level === level;

            if (matchSearch && matchLevel) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        const hasFilter = search || level;
        document.getElementById('filterInfo').style.display = hasFilter ? 'block' : 'none';
        document.getElementById('filterCount').textContent = visible;
        document.getElementById('noResultRow').style.display = (visible === 0 && totalRows > 0) ? 'block' : 'none';
    }

    function resetFilters() {
        document.getElementById('filterSearch').value = '';
        document.getElementById('filterLevel').value = '';
        applyFilters();
    }

    function showMappingModal() {
        document.getElementById('modalTitle').textContent = 'Add Curriculum Mapping';
        document.getElementById('mappingForm').action = "{{ route('course_mapping.store', $prodi->id) }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('field_id_subject').value = '';
        document.getElementById('field_id_plo').value = '';
        document.getElementById('field_level_maping').value = '';
        document.getElementById('mappingModal').style.display = 'flex';
    }

    function editMapping(maping) {
        document.getElementById('modalTitle').textContent = 'Edit Curriculum Mapping';
        document.getElementById('mappingForm').action = "/admin/course-mapping/" + maping.id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('field_id_subject').value = maping.id_subject;
        document.getElementById('field_id_plo').value = maping.id_plo;
        document.getElementById('field_level_maping').value = maping.level_maping;
        document.getElementById('mappingModal').style.display = 'flex';
    }

    function closeMappingModal() {
        document.getElementById('mappingModal').style.display = 'none';
    }

    document.getElementById('mappingModal').addEventListener('click', function(e) {
        if (e.target === this) closeMappingModal();
    });

    @if($errors->any() && !$errors->has('error'))
        document.getElementById('mappingModal').style.display = 'flex';
        @if(old('_method') == 'PUT')
            document.getElementById('modalTitle').textContent = 'Edit Curriculum Mapping';
        @endif
    @endif
</script>
@endsection
@endsection
