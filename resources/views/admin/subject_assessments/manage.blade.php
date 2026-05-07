@extends('layouts.admin')

@section('title', 'Manage Assessments - ' . $subject->kode_subject)

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Assessments for {{ $subject->kode_subject }}</h1>
@endsection

@section('content')
<div style="margin-bottom: 1rem;">
    <a href="{{ route('subjects.index') }}" style="font-size: 0.875rem; color: var(--text-muted);">← Back to Subjects</a>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <span>Subject Info</span>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div>
                <strong style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Kode Subject</strong>
                <div>{{ $subject->kode_subject }}</div>
            </div>
            <div>
                <strong style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Nama Subject</strong>
                <div>{{ $subject->nama_subject }}</div>
            </div>
            <div>
                <strong style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Total Bobot Asesmen</strong>
                @php
                    $totalBobot = $subject->assessments->sum('weight');
                @endphp
                <div style="font-weight: 600; color: {{ $totalBobot == 100 ? '#166534' : '#991b1b' }};">
                    {{ $totalBobot }}%
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; align-items: start;">
    {{-- Form Add --}}
    <div class="card">
        <div class="card-header">
            <span>Add Assessment</span>
        </div>
        <div class="card-body">
            @if($errors->any() && !old('edit_id'))
                <div class="alert alert-danger" style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                    <ul style="margin: 0; padding-left: 1.25rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('subject_assessments.store', $subject->id) }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Assessment Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Project / Midterm Exam" required value="{{ !old('edit_id') ? old('name') : '' }}">
                </div>
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Weight (%)</label>
                    <input type="number" name="weight" class="form-control" placeholder="e.g. 40" required min="1" max="100" value="{{ !old('edit_id') ? old('weight') : '' }}">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Rubric Link (Optional)</label>
                    <input type="url" name="rubric_link" class="form-control" placeholder="e.g. https://docs.google.com/..." value="{{ !old('edit_id') ? old('rubric_link') : '' }}">
                </div>

                <div style="margin-top: 1.5rem; text-align: right;">
                    <button type="submit" class="btn btn-primary">Save Assessment</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Data List --}}
    <div class="card">
        <div class="card-header">
            <span>Assessment Components</span>
        </div>
        <div class="card-body" style="padding: 0;">
            @if(session('success'))
                <div style="padding: 1rem;">
                    <div class="alert alert-success" style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.5rem; margin-bottom: 0;">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <div style="overflow-x: auto;">
                <table id="dataTable" style="width: 100%; text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <th style="padding: 0.75rem 1rem; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Name</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Weight</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Rubric</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subject->assessments as $assessment)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 0.75rem 1rem; font-weight: 500;">{{ $assessment->name }}</td>
                                <td style="padding: 0.75rem 1rem;">{{ $assessment->weight }}%</td>
                                <td style="padding: 0.75rem 1rem;">
                                    @if($assessment->rubric_link)
                                        <a href="{{ $assessment->rubric_link }}" target="_blank" style="color: var(--primary); text-decoration: none; font-size: 0.8rem;">View Rubric ↗</a>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.75rem;">-</span>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                        <button type="button" class="btn btn-secondary edit-btn" 
                                                data-id="{{ $assessment->id }}"
                                                data-name="{{ $assessment->name }}"
                                                data-weight="{{ $assessment->weight }}"
                                                data-rubric="{{ $assessment->rubric_link }}"
                                                style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</button>
                                        
                                        <form action="{{ route('subject_assessments.destroy', $assessment->id) }}" method="POST" onsubmit="return confirm('Delete this assessment component?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding: 1.5rem; text-align: center; color: var(--text-muted);">
                                    No assessments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span>Edit Assessment</span>
            <button type="button" onclick="closeEditModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <div class="card-body">
            @if($errors->any() && old('edit_id'))
                <div class="alert alert-danger" style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                    <ul style="margin: 0; padding-left: 1.25rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="edit_id" id="edit_id" value="">
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Assessment Name</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Weight (%)</label>
                    <input type="number" name="weight" id="edit_weight" class="form-control" required min="1" max="100">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Rubric Link (Optional)</label>
                    <input type="url" name="rubric_link" id="edit_rubric" class="form-control">
                </div>

                <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    const editModal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');
    
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_weight').value = this.dataset.weight;
            document.getElementById('edit_rubric').value = this.dataset.rubric;
            
            editForm.action = `/admin/subject_assessments/${id}`;
            editModal.style.display = 'flex';
        });
    });

    function closeEditModal() {
        editModal.style.display = 'none';
    }

    @if($errors->any() && old('edit_id'))
        // Reopen modal if there were errors
        editForm.action = `/admin/subject_assessments/{{ old('edit_id') }}`;
        document.getElementById('edit_id').value = "{{ old('edit_id') }}";
        document.getElementById('edit_name').value = "{{ old('name') }}";
        document.getElementById('edit_weight').value = "{{ old('weight') }}";
        document.getElementById('edit_rubric').value = "{{ old('rubric_link') }}";
        editModal.style.display = 'flex';
    @endif
</script>
@endsection
@endsection
