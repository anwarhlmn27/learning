@extends('layouts.admin')

@section('title', 'Manage RPS Sessions')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Manage Sessions: {{ $rp->subject->kode_subject }} - {{ $rp->subject->nama_subject }}</h1>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/rps-sessions.css') }}">
<style>
    /* Card Header Session Custom Background */
    .card-header-session {
        background-color: #f9fafb !important;
    }
    .card-header-session h3 {
        color: #111827 !important;
    }

    /* =========================================
       Dark Mode Support
       ========================================= */

    body[data-theme-version="dark"] .sticky-counter {
        background: #1c1c24 !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    body[data-theme-version="dark"] .sticky-counter h2 {
        color: #f3f4f6 !important;
    }
    body[data-theme-version="dark"] .sticky-counter p {
        color: #9ca3af !important;
    }

    body[data-theme-version="dark"] .card-header-session {
        background-color: #1c1c24 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    body[data-theme-version="dark"] .card-header-session h3 {
        color: #f3f4f6 !important;
    }

    body[data-theme-version="dark"] .assessment-row {
        background: #181924 !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    /* Inputs and Forms dark mode integration */
    body[data-theme-version="dark"] .session-form input[type="text"],
    body[data-theme-version="dark"] .session-form input[type="number"],
    body[data-theme-version="dark"] .session-form textarea,
    body[data-theme-version="dark"] .session-form select {
        background-color: #181924 !important;
        color: #f3f4f6 !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    body[data-theme-version="dark"] .session-form .clo-container {
        background-color: #181924 !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        color: #f3f4f6 !important;
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
    <div class="card-header card-header-session" style="cursor: pointer;" onclick="toggleSession('{{ $session->id }}')">
        <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Session {{ $session->session_number }}: {{ $session->topic_name }}</h3>
    </div>
    <div class="card-body" id="session_{{ $session->id }}" style="display: none;">
        <form action="{{ route('admin.rps.sessions.update', $session->id) }}" method="POST" class="session-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ __('Topic Name') }} <span style="color: red;">*</span></label>
                    <input type="text" name="topic_name" value="{{ $session->topic_name }}" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ __('Mapped CLOs (CPMK) for Topic') }} <span style="color: red;">*</span></label>
                    <div class="clo-container" style="max-height: 100px; overflow-y: auto; border: 1px solid #e5e7eb; padding: 0.5rem; border-radius: 0.375rem; background: #fff;">
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
                <label style="font-weight: 600; display: block; margin-bottom: 0.25rem;">Sub CLO / Topic Learning Objective <span style="color: red;">*</span></label>
                <textarea name="sub_clo" rows="2" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">{{ $session->sub_clo }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ __('Assessment Indicators (Indikator Penilaian)') }} <span style="color: red;">*</span></label>
                    <textarea name="assessment_indicators" rows="3" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">{{ $session->assessment_indicators }}</textarea>
                </div>
                <div>
                    <label style="font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ __('Evaluation Criteria (Kriteria Evaluasi)') }} <span style="color: red;">*</span></label>
                    <textarea name="evaluation_criteria" rows="3" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">{{ $session->evaluation_criteria }}</textarea>
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ __('Learning Materials (Bahan Kajian)') }} <span style="color: red;">*</span></label>
                <textarea name="learning_materials" rows="3" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">{{ $session->learning_materials }}</textarea>
            </div>

            <!-- Activities Section -->
            <hr style="margin: 1.5rem 0;">
            <h4 style="font-weight: 600; margin-bottom: 1rem; color: var(--primary);">Activities (Connect, Coach, Check, Wrap-up)</h4>
            
            <div id="activities_container_{{ $session->id }}">
                @foreach($session->activities as $index => $activity)
                    <div class="activity-row" style="display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 0.5rem;">
                        <div style="width: 150px;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Type') }} <span style="color: red;">*</span></label>
                            <select name="activities[{{ $index }}][type]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                                <option value="Connect" {{ $activity->type == 'Connect' ? 'selected' : '' }}>Connect</option>
                                <option value="Coach" {{ $activity->type == 'Coach' ? 'selected' : '' }}>Coach</option>
                                <option value="Check" {{ $activity->type == 'Check' ? 'selected' : '' }}>Check</option>
                                <option value="Wrap-up" {{ $activity->type == 'Wrap-up' ? 'selected' : '' }}>Wrap-up</option>
                            </select>
                        </div>
                        <div style="width: 100px;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Dur (Min)') }} <span style="color: red;">*</span></label>
                            <input type="number" name="activities[{{ $index }}][duration]" value="{{ $activity->duration }}" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Activity Content') }} <span style="color: red;">*</span></label>
                            <textarea name="activities[{{ $index }}][content]" required rows="2" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">{{ $activity->content }}</textarea>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; margin-bottom: 0.25rem;">&nbsp;</label>
                            <button type="button" onclick="this.parentElement.parentElement.remove()" class="btn btn-danger" style="padding: 0.5rem; border-radius: 0.375rem; border: none; background: #fee2e2; color: #b91c1c; cursor: pointer; height: 38px;">&times;</button>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" onclick="addActivityRow('{{ $session->id }}')" class="btn btn-secondary" style="margin-top: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem; background: #e5e7eb; color: #374151;">+ Add Activity</button>

            
            <!-- Multi Assessment Section -->
            <hr style="margin: 1.5rem 0; border: none; border-top: 2px dashed #e5e7eb;">
            <h4 style="font-weight: 600; margin-bottom: 1rem; color: var(--primary);">Assessments for this Session</h4>
            
            <div id="assessments_container_{{ $session->id }}">
                @foreach($session->assessments as $index => $assessment)
                    <div class="assessment-row">
                        <div style="display: grid; grid-template-columns: 1fr 1fr 100px auto; gap: 1rem; align-items: flex-end; margin-bottom: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Target CLO') }} <span style="color: red;">*</span></label>
                                <select name="assessments[{{ $index }}][clo_id]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                                    @foreach($clos as $clo)
                                        <option value="{{ $clo->id }}" {{ $assessment->clo_id == $clo->id ? 'selected' : '' }}>{{ $clo->kode_clo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Tugas') }} <span style="color: red;">*</span></label>
                                <textarea name="assessments[{{ $index }}][assessment_type]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; height: 38px; resize: vertical; min-height: 38px;">{{ $assessment->assessment_type }}</textarea>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Weight % <span style="color: red;">*</span></label>
                                <input type="number" name="assessments[{{ $index }}][weight]" value="{{ $assessment->weight }}" required class="weight-input" oninput="updateGlobalWeight()" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                            </div>
                            <div>
                                <button type="button" onclick="this.parentElement.parentElement.parentElement.remove(); updateGlobalWeight();" class="btn btn-danger" style="padding: 0.5rem; border-radius: 0.375rem; border: none; background: #fee2e2; color: #b91c1c; cursor: pointer; height: 38px;">&times;</button>
                            </div>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Assignment Activities (Aktivitas Penugasan)') }} </label>
                            <textarea name="assessments[{{ $index }}][assignment_activities]" rows="2" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">{{ $assessment->assignment_activities }}</textarea>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Assessment Scope (Ruang Lingkup Tugas)') }} </label>
                            <textarea name="assessments[{{ $index }}][assessment_scope]" rows="2" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">{{ $assessment->assessment_scope }}</textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('How Worked (Cara Pengerjaan)') }} </label>
                                <input type="text" name="assessments[{{ $index }}][how_worked]" value="{{ $assessment->how_worked }}" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;" placeholder="{{ __('e.g. Individu, Kelompok') }}">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Time Worked (Minutes)') }} </label>
                                <input type="number" name="assessments[{{ $index }}][time_worked]" value="{{ $assessment->time_worked }}" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;" placeholder="{{ __('e.g. 60') }}">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Assessment Output (Luaran)') }} </label>
                                <textarea name="assessments[{{ $index }}][assessment_output]" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;" placeholder="{{ __('e.g. Makalah, Video') }}">{{ $assessment->assessment_output }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <button type="button" onclick="addAssessmentRow('{{ $session->id }}')" class="btn btn-primary" style="margin-top: 0.5rem; margin-bottom: 1.5rem; font-size: 0.75rem; background: var(--primary); color: #fff;">+ Add Assessment</button>

            <!-- Resources Section -->
            <hr style="margin: 1.5rem 0;">
            <h4 style="font-weight: 600; margin-bottom: 1rem; color: var(--primary);">Modules / Resources</h4>
            
            <div id="resources_container_{{ $session->id }}">
                @foreach($session->resources as $index => $resource)
                    <div class="resource-row" id="resource_row_{{ $resource->id }}" style="display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 0.5rem;">
                        <input type="hidden" name="existing_resources[{{ $index }}][id]" value="{{ $resource->id }}">
                        <input type="checkbox" name="existing_resources[{{ $index }}][delete]" value="1" id="delete_resource_{{ $resource->id }}" style="display: none;">
                        <div style="flex: 1;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Resource Name') }} <span style="color: red;">*</span></label>
                            <input type="text" name="existing_resources[{{ $index }}][nm_resource]" value="{{ $resource->nm_resource }}" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                        </div>
                        <div style="width: 150px;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Type') }} <span style="color: red;">*</span></label>
                            <select name="existing_resources[{{ $index }}][type]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                                <option value="Modul" {{ $resource->type == 'Modul' ? 'selected' : '' }}>Modul</option>
                                <option value="Materi Tambahan" {{ $resource->type == 'Materi Tambahan' ? 'selected' : '' }}>Materi Tambahan</option>
                                <option value="Video" {{ $resource->type == 'Video' ? 'selected' : '' }}>Video</option>
                            </select>
                        </div>
                        <div style="width: 250px;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">Current File / Update</label>
                            <a href="{{ asset('storage/' . $resource->file_path) }}" target="_blank" style="display: block; font-size: 0.75rem; margin-bottom: 0.25rem; color: var(--primary);">View Current</a>
                            <input type="file" name="existing_resources[{{ $index }}][file]" style="width: 100%; padding: 0.3rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; margin-bottom: 0.25rem;">&nbsp;</label>
                            <button type="button" onclick="deleteExistingResource('{{ $resource->id }}')" class="btn btn-danger" style="padding: 0.5rem; border-radius: 0.375rem; border: none; background: #fee2e2; color: #b91c1c; cursor: pointer; height: 38px;">&times;</button>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" onclick="addResourceRow('{{ $session->id }}')" class="btn btn-secondary" style="margin-top: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem; background: #e5e7eb; color: #374151;">+ Add Resource</button>

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

        const row = document.createElement('div');
        row.className = 'assessment-row';
        row.innerHTML = `
            <div style="display: grid; grid-template-columns: 1fr 1fr 100px auto; gap: 1rem; align-items: flex-end; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Target CLO') }} </label>
                    <select name="assessments[${index}][clo_id]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                        ${cloOptions}
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Type') }} <span style="color: red;">*</span></label>
                    <textarea name="assessments[${index}][assessment_type]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; height: 38px; resize: vertical; min-height: 38px;" placeholder="{{ __('e.g. Lisan, Tertulis, Kinerja') }}"></textarea>
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
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Assignment Activities (Aktivitas Penugasan)') }} </label>
                <textarea name="assessments[${index}][assignment_activities]" rows="2" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;"></textarea>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Assessment Scope (Ruang Lingkup Tugas)') }} </label>
                <textarea name="assessments[${index}][assessment_scope]" rows="2" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('How Worked (Cara Pengerjaan)') }} </label>
                    <input type="text" name="assessments[${index}][how_worked]" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;" placeholder="{{ __('e.g. Individu, Kelompok') }}">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Time Worked (Minutes)') }} </label>
                    <input type="number" name="assessments[${index}][time_worked]" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;" placeholder="{{ __('e.g. 60') }}">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Assessment Output (Luaran)') }} </label>
                    <input type="text" name="assessments[${index}][assessment_output]" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;" placeholder="{{ __('e.g. Makalah, Video') }}">
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
        row.style.alignItems = 'flex-start';
        row.style.marginBottom = '0.5rem';
        row.innerHTML = `
            <div style="width: 150px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Type') }} </label>
                <select name="activities[${index}][type]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                    <option value="Connect">Connect</option>
                    <option value="Coach">Coach</option>
                    <option value="Check">Check</option>
                    <option value="Wrap-up">Wrap-up</option>
                </select>
            </div>
            <div style="width: 100px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Dur (Min)') }} </label>
                <input type="number" name="activities[${index}][duration]" value="0" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
            </div>
            <div style="flex: 1;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Activity Content') }} </label>
                <textarea name="activities[${index}][content]" required rows="2" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;" placeholder="{{ __('Describe activity...') }}"></textarea>
            </div>
            <div>
                <label style="display: block; font-size: 0.875rem; margin-bottom: 0.25rem;">&nbsp;</label>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="btn btn-danger" style="padding: 0.5rem; border-radius: 0.375rem; border: none; background: #fee2e2; color: #b91c1c; cursor: pointer; height: 38px;">&times;</button>
            </div>
        `;
        container.appendChild(row);
    }

    function addResourceRow(sessionId) {
        const container = document.getElementById('resources_container_' + sessionId);
        const index = Date.now();
        const row = document.createElement('div');
        row.className = 'resource-row';
        row.style.display = 'flex';
        row.style.gap = '1rem';
        row.style.alignItems = 'flex-start';
        row.style.marginBottom = '0.5rem';
        row.innerHTML = `
            <div style="flex: 1;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Resource Name') }} <span style="color: red;">*</span></label>
                <input type="text" name="new_resources[${index}][nm_resource]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
            </div>
            <div style="width: 150px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('Type') }} <span style="color: red;">*</span></label>
                <select name="new_resources[${index}][type]" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
                    <option value="Modul">Modul</option>
                    <option value="Materi Tambahan">Materi Tambahan</option>
                    <option value="Video">Video</option>
                </select>
            </div>
            <div style="width: 250px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('File') }} <span style="color: red;">*</span></label>
                <input type="file" name="new_resources[${index}][file]" required style="width: 100%; padding: 0.3rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;">
            </div>
            <div>
                <label style="display: block; font-size: 0.875rem; margin-bottom: 0.25rem;">&nbsp;</label>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="btn btn-danger" style="padding: 0.5rem; border-radius: 0.375rem; border: none; background: #fee2e2; color: #b91c1c; cursor: pointer; height: 38px;">&times;</button>
            </div>
        `;
        container.appendChild(row);
    }

    function deleteExistingResource(resourceId) {
        const row = document.getElementById('resource_row_' + resourceId);
        const checkbox = document.getElementById('delete_resource_' + resourceId);
        if (row && checkbox) {
            checkbox.checked = true;
            row.querySelectorAll('input, select, textarea').forEach(input => {
                if (input !== checkbox) {
                    input.removeAttribute('required');
                }
            });
            row.style.display = 'none';
        }
    }

    // Initial calculation
    updateGlobalWeight();
</script>
@endsection
@endsection
