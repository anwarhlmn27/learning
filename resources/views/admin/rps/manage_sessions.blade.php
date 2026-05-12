@extends('layouts.admin')

@section('title', 'Manage RPS Sessions')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Manage Sessions: {{ $rp->subject->kode_subject }} - {{ $rp->subject->nama_subject }}</h1>
@endsection

@section('styles')
<style>
    .sticky-counter {
        position: sticky;
        top: 70px;
        z-index: 20;
        background: #fff;
        padding: 1rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #e5e7eb;
    }
    .weight-badge {
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 1.1rem;
    }
    .weight-ok { background: #dcfce7; color: #166534; }
    .weight-warning { background: #fee2e2; color: #991b1b; }
    
    .assessment-row {
        background: #f9fafb;
        padding: 1rem;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        margin-bottom: 0.75rem;
    }
</style>
@endsection

@section('content')
<div style="margin-bottom: 1rem;">
    <a href="{{ route('admin.rps.index') }}" class="btn btn-secondary">Back to RPS List</a>
</div>

<div class="sticky-counter">
    <div>
        <h2 style="margin: 0; font-size: 1rem; color: var(--text-muted);">Total Cumulative Weight</h2>
        <p style="margin: 0; font-size: 0.75rem;">Must be exactly 100%</p>
    </div>
    <div id="total_weight_display" class="weight-badge {{ $totalWeight == 100 ? 'weight-ok' : 'weight-warning' }}">
        <span id="current_total_weight">{{ $totalWeight }}</span>% / 100%
    </div>
</div>

@foreach($rp->sessions as $session)
<div class="card" style="margin-bottom: 1rem;">
    <div class="card-header" style="background-color: #f9fafb; cursor: pointer;" onclick="toggleSession('{{ $session->id }}')">
        <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Session {{ $session->session_number }}: {{ $session->topic_name }}</h3>
    </div>
    <div class="card-body" id="session_{{ $session->id }}" style="display: none;">
        <form action="{{ route('admin.rps.sessions.update', $session->id) }}" method="POST" class="session-form">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="font-weight: 600; display: block; margin-bottom: 0.25rem;">Topic Name</label>
                    <input type="text" name="topic_name" value="{{ $session->topic_name }}" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-weight: 600; display: block; margin-bottom: 0.25rem;">Mapped CLOs (CPMK) for Topic</label>
                    <div style="max-height: 100px; overflow-y: auto; border: 1px solid #e5e7eb; padding: 0.5rem; border-radius: 0.375rem; background: #fff;">
                        @foreach($clos as $clo)
                            <label style="display: block; margin-bottom: 0.25rem; font-size: 0.875rem;">
                                <input type="checkbox" name="clos[]" value="{{ $clo->id }}" 
                                    {{ $session->clos->contains($clo->id) ? 'checked' : '' }}>
                                {{ $clo->kode_clo }} - {{ \Illuminate\Support\Str::limit($clo->deskripsi, 50) }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 600; display: block; margin-bottom: 0.25rem;">Sub CLO / Topic Learning Objective</label>
                <textarea name="sub_clo" rows="2" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">{{ $session->sub_clo }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="font-weight: 600; display: block; margin-bottom: 0.25rem;">Assessment Indicators (Indikator Penilaian)</label>
                    <textarea name="assessment_indicators" rows="3" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">{{ $session->assessment_indicators }}</textarea>
                </div>
                <div>
                    <label style="font-weight: 600; display: block; margin-bottom: 0.25rem;">Evaluation Criteria (Kriteria Evaluasi)</label>
                    <textarea name="evaluation_criteria" rows="3" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">{{ $session->evaluation_criteria }}</textarea>
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 600; display: block; margin-bottom: 0.25rem;">Learning Materials (Bahan Kajian)</label>
                <textarea name="learning_materials" rows="3" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">{{ $session->learning_materials }}</textarea>
            </div>

            <!-- Multi Assessment Section -->
            <hr style="margin: 1.5rem 0; border: none; border-top: 2px dashed #e5e7eb;">
            <h4 style="font-weight: 600; margin-bottom: 1rem; color: var(--primary);">Assessments for this Session</h4>
            
            <div id="assessments_container_{{ $session->id }}">
                @foreach($session->assessments as $index => $assessment)
                    <div class="assessment-row">
                        <div style="display: grid; grid-template-columns: 1fr 1fr 100px auto; gap: 1rem; align-items: flex-end; margin-bottom: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Target CLO</label>
                                <select name="assessments[{{ $index }}][clo_id]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                                    @foreach($clos as $clo)
                                        <option value="{{ $clo->id }}" {{ $assessment->clo_id == $clo->id ? 'selected' : '' }}>{{ $clo->kode_clo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Type</label>
                                <select name="assessments[{{ $index }}][assessment_type_id]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                                    @foreach($assessmentTypes as $type)
                                        <option value="{{ $type->id }}" {{ $assessment->assessment_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Weight %</label>
                                <input type="number" name="assessments[{{ $index }}][weight]" value="{{ $assessment->weight }}" required class="weight-input" oninput="updateGlobalWeight()" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                            </div>
                            <div>
                                <button type="button" onclick="this.parentElement.parentElement.parentElement.remove(); updateGlobalWeight();" class="btn btn-danger" style="padding: 0.5rem; border-radius: 0.375rem; border: none; background: #fee2e2; color: #b91c1c; cursor: pointer; height: 38px;">&times;</button>
                            </div>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Assignment Activities (Aktivitas Penugasan)</label>
                            <textarea name="assessments[{{ $index }}][assignment_activities]" rows="2" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">{{ $assessment->assignment_activities }}</textarea>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Assessment Scope (Ruang Lingkup Tugas)</label>
                            <textarea name="assessments[{{ $index }}][assessment_scope]" rows="2" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">{{ $assessment->assessment_scope }}</textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">How Worked (Cara Pengerjaan)</label>
                                <input type="text" name="assessments[{{ $index }}][how_worked]" value="{{ $assessment->how_worked }}" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;" placeholder="e.g. Individu, Kelompok">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Time Worked (Minutes)</label>
                                <input type="number" name="assessments[{{ $index }}][time_worked]" value="{{ $assessment->time_worked }}" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;" placeholder="e.g. 60">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Assessment Output (Luaran)</label>
                                <input type="text" name="assessments[{{ $index }}][assessment_output]" value="{{ $assessment->assessment_output }}" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;" placeholder="e.g. Makalah, Video">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <button type="button" onclick="addAssessmentRow('{{ $session->id }}')" class="btn btn-primary" style="margin-top: 0.5rem; margin-bottom: 1.5rem; font-size: 0.75rem; background: var(--primary); color: #fff;">+ Add Assessment</button>

            <!-- Activities Section -->
            <hr style="margin: 1.5rem 0;">
            <h4 style="font-weight: 600; margin-bottom: 1rem;">Activities (Connect, Coach, Check, Wrap-up)</h4>
            
            <div id="activities_container_{{ $session->id }}">
                @foreach($session->activities as $index => $activity)
                    <div class="activity-row" style="display: flex; gap: 1rem; align-items: flex-end; margin-bottom: 0.5rem;">
                        <div style="width: 150px;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">Type</label>
                            <select name="activities[{{ $index }}][type]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                                <option value="Connect" {{ $activity->type == 'Connect' ? 'selected' : '' }}>Connect</option>
                                <option value="Coach" {{ $activity->type == 'Coach' ? 'selected' : '' }}>Coach</option>
                                <option value="Check" {{ $activity->type == 'Check' ? 'selected' : '' }}>Check</option>
                                <option value="Wrap-up" {{ $activity->type == 'Wrap-up' ? 'selected' : '' }}>Wrap-up</option>
                            </select>
                        </div>
                        <div style="width: 100px;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">Dur (Min)</label>
                            <input type="number" name="activities[{{ $index }}][duration]" value="{{ $activity->duration }}" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">Activity Content</label>
                            <input type="text" name="activities[{{ $index }}][content]" value="{{ $activity->content }}" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <button type="button" onclick="this.parentElement.parentElement.remove()" class="btn btn-danger" style="padding: 0.5rem; border-radius: 0.375rem; border: none; background: #fee2e2; color: #b91c1c; cursor: pointer; height: 38px;">&times;</button>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <button type="button" onclick="addActivityRow('{{ $session->id }}')" class="btn btn-secondary" style="margin-top: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem; background: #e5e7eb; color: #374151;">+ Add Activity</button>

            <div style="margin-top: 1rem; text-align: right; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary btn-save" style="padding: 0.75rem 1.5rem; font-size: 1rem;">Save Session {{ $session->session_number }}</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<div id="clo_options" style="display:none;">
    @foreach($clos as $clo)
        <option value="{{ $clo->id }}">{{ $clo->kode_clo }}</option>
    @endforeach
</div>

<div id="type_options" style="display:none;">
    @foreach($assessmentTypes as $type)
        <option value="{{ $type->id }}">{{ $type->name }}</option>
    @endforeach
</div>

@section('scripts')
<script>
    function toggleSession(id) {
        const body = document.getElementById('session_' + id);
        body.style.display = body.style.display === 'none' ? 'block' : 'none';
    }

    function updateGlobalWeight() {
        let total = 0;
        document.querySelectorAll('.weight-input').forEach(input => {
            total += parseInt(input.value) || 0;
        });

        const display = document.getElementById('current_total_weight');
        const badge = document.getElementById('total_weight_display');
        display.innerText = total;

        if (total > 100) {
            badge.className = 'weight-badge weight-warning';
            document.querySelectorAll('.btn-save').forEach(btn => btn.disabled = true);
        } else {
            badge.className = total === 100 ? 'weight-badge weight-ok' : 'weight-badge weight-warning';
            document.querySelectorAll('.btn-save').forEach(btn => btn.disabled = false);
        }
    }

    function addAssessmentRow(sessionId) {
        const container = document.getElementById('assessments_container_' + sessionId);
        const index = Date.now();
        const cloOptions = document.getElementById('clo_options').innerHTML;
        const typeOptions = document.getElementById('type_options').innerHTML;

        const row = document.createElement('div');
        row.className = 'assessment-row';
        row.innerHTML = `
            <div style="display: grid; grid-template-columns: 1fr 1fr 100px auto; gap: 1rem; align-items: flex-end; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Target CLO</label>
                    <select name="assessments[${index}][clo_id]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                        ${cloOptions}
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Type</label>
                    <select name="assessments[${index}][assessment_type_id]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                        ${typeOptions}
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Weight %</label>
                    <input type="number" name="assessments[${index}][weight]" value="0" required class="weight-input" oninput="updateGlobalWeight()" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                </div>
                <div>
                    <button type="button" onclick="this.parentElement.parentElement.parentElement.remove(); updateGlobalWeight();" class="btn btn-danger" style="padding: 0.5rem; border-radius: 0.375rem; border: none; background: #fee2e2; color: #b91c1c; cursor: pointer; height: 38px;">&times;</button>
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Assignment Activities (Aktivitas Penugasan)</label>
                <textarea name="assessments[${index}][assignment_activities]" rows="2" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;"></textarea>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Assessment Scope (Ruang Lingkup Tugas)</label>
                <textarea name="assessments[${index}][assessment_scope]" rows="2" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">How Worked (Cara Pengerjaan)</label>
                    <input type="text" name="assessments[${index}][how_worked]" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;" placeholder="e.g. Individu, Kelompok">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Time Worked (Minutes)</label>
                    <input type="number" name="assessments[${index}][time_worked]" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;" placeholder="e.g. 60">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Assessment Output (Luaran)</label>
                    <input type="text" name="assessments[${index}][assessment_output]" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;" placeholder="e.g. Makalah, Video">
                </div>
            </div>
        `;
        container.appendChild(row);
    }

    function addActivityRow(sessionId) {
        const container = document.getElementById('activities_container_' + sessionId);
        const index = Date.now();
        const row = document.createElement('div');
        row.className = 'activity-row';
        row.style.display = 'flex';
        row.style.gap = '1rem';
        row.style.alignItems = 'flex-end';
        row.style.marginBottom = '0.5rem';
        row.innerHTML = `
            <div style="width: 150px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">Type</label>
                <select name="activities[${index}][type]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                    <option value="Connect">Connect</option>
                    <option value="Coach">Coach</option>
                    <option value="Check">Check</option>
                    <option value="Wrap-up">Wrap-up</option>
                </select>
            </div>
            <div style="width: 100px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">Dur (Min)</label>
                <input type="number" name="activities[${index}][duration]" value="0" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
            </div>
            <div style="flex: 1;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">Activity Content</label>
                <input type="text" name="activities[${index}][content]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;" placeholder="Describe activity...">
            </div>
            <div>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="btn btn-danger" style="padding: 0.5rem; border-radius: 0.375rem; border: none; background: #fee2e2; color: #b91c1c; cursor: pointer; height: 38px;">&times;</button>
            </div>
        `;
        container.appendChild(row);
    }

    // Initial calculation
    updateGlobalWeight();
</script>
@endsection
@endsection
