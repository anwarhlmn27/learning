@extends('layouts.admin')

@section('title', __('Manage Graduate Profile') . ' - ' . $prodi->nama_prodi)

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">{{ __('Manage GP:') }} {{ $prodi->nama_prodi }}</h1>
@endsection

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('gp.index') }}" class="btn btn-warning btn-sm">{{ __(' Back to List') }}</a>
</div>



@if($errors->has('error'))
    <div class="alert alert-danger" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        {{ $errors->first('error') }}
    </div>
@endif

{{-- Hidden delete forms (placed OUTSIDE the table so browser doesn't strip them) --}}
@foreach($prodi->gps as $gp)
    <form id="delete-profile-{{ $gp->id }}" action="{{ route('gp.profile.destroy', $gp->id) }}" method="POST" style="display:none;">
        @csrf @method('DELETE')
    </form>
@endforeach
@foreach($prodi->gpAttachments as $att)
    <form id="delete-attachment-{{ $att->id }}" action="{{ route('gp.attachment.destroy', $att->id) }}" method="POST" style="display:none;">
        @csrf @method('DELETE')
    </form>
@endforeach

<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem; align-items: start;">
    
    <!-- LEFT: Graduate Profiles (Expertise) -->
    <div class="card">
        <div class="card-header">
            <span>{{ __('Profile Items (Expertise)') }}</span>
            <button onclick="showProfileModal()" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">+ {{ __('Add Item') }}</button>
        </div>
        <div class="card-body" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="table table-responsive-md">
                    <thead>
                        <tr>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Profile Name') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prodi->gps as $gp)
                            <tr>
                                <td style="font-weight: 600; color: var(--primary);">{{ $gp->kode_profil }}</td>
                                <td>{{ $gp->nm_profil }}</td>
                                <td>
                                    <span style="padding: 0.125rem 0.5rem; border-radius: 1rem; font-size: 0.75rem; 
                                        {{ $gp->status == 'Aktif' ? 'background: #dcfce7; color: #166534;' : 
                                           ($gp->status == 'Draft' ? 'background: #f3f4f6; color: #374151;' : 
                                           ($gp->status == 'Revisi' ? 'background: #fef9c3; color: #854d0e;' : 'background: #fee2e2; color: #b91c1c;')) }}">
                                        {{ $gp->status }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button type="button" class="btn btn-primary edit-profile-btn" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;"
                                            data-id="{{ $gp->id }}"
                                            data-kode_profil="{{ $gp->kode_profil }}"
                                            data-nm_profil="{{ $gp->nm_profil }}"
                                            data-deskripsi="{{ $gp->deskripsi }}"
                                            data-career_pathway="{{ $gp->career_pathway }}"
                                            data-kompetensi="{{ $gp->kompetensi }}"
                                            data-sumber_acuan="{{ $gp->sumber_acuan }}"
                                            data-stakeholders="{{ $gp->stakeholders }}"
                                            data-status="{{ $gp->status }}">{{ __('Edit') }}</button>
                                        <button type="button" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;"
                                            onclick="if(confirm('{{ __('Delete this profile item?') }}')) document.getElementById('delete-profile-{{ $gp->id }}').submit();">{{ __('Delete') }}</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">{{ __('No data found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- RIGHT: Supporting Documents -->
    <div class="card">
        <div class="card-header">
            <span>{{ __('Supporting Documents') }}</span>
        </div>
        <div class="card-body">
            <!-- Upload Form -->
            <form action="{{ route('gp.attachment.store', $prodi->id) }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb;">
                @csrf
                <div class="form-group">
                    <label class="form-label">{{ __('Document Name') }} <span style="color: red;">*</span></label>
                    <select name="nm_dokumen" class="form-control @error('nm_dokumen') is-invalid @enderror" required>
                        <option value="">-- Select Type --</option>
                        <option value="FGD Report">FGD Report</option>
                        <option value="Alumni Survey Report">Alumni Survey Report</option>
                        <option value="Job Market Analysis">Job Market Analysis</option>
                        <option value="Internal Institutional Analysis">Internal Institutional Analysis</option>
                        <option value="National & International Standards">National & International Standards</option>
                        <option value="Future Trends Identification">Future Trends Identification</option>
                        <option value="Other Attachment">Other Attachment</option>
                    </select>
                    @error('nm_dokumen')
                        <small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('File (PDF, Max 2MB)') }} <span style="color: red;">*</span></label>
                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".pdf" required>
                    @error('file')
                        <small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">{{ __('Upload Attachment') }}</button>
            </form>

            <!-- Attachments List -->
            <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 1rem;">{{ __('Uploaded Files') }}</h4>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @forelse($prodi->gpAttachments as $att)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                        <div>
                            <div style="font-weight: 600; font-size: 0.875rem;">{{ $att->nm_dokumen }}</div>
                            <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" style="font-size: 0.75rem; color: var(--primary);">{{ __('View File') }}</a>
                        </div>
                        <button type="button" style="background: none; border: none; color: var(--danger); cursor: pointer; font-size: 1.25rem;"
                            onclick="if(confirm('{{ __('Remove this attachment?') }}')) document.getElementById('delete-attachment-{{ $att->id }}').submit();">×</button>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-muted); font-size: 0.875rem;">{{ __('No data found.') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- PROFILE MODAL -->
<div id="profileModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 800px; margin: 0; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
        <div class="card-header" style="flex-shrink: 0; display: flex; justify-content: space-between; align-items: center;">
            <span id="modalTitle" style="font-weight: 600; font-size: 1.125rem;">{{ __('Add Profile Item') }}</span>
            <button type="button" onclick="closeProfileModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted); line-height: 1;">&times;</button>
        </div>
        <div class="card-body" style="overflow-y: auto; padding: 1.5rem;">
            <form id="profileForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">{{ __('Profile Code') }} <span style="color: red;">*</span></label>
                        <input type="text" name="kode_profil" id="field_kode_profil" class="form-control" required placeholder="{{ __('e.g. GP-01') }}">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">{{ __('Profile Name') }} <span style="color: red;">*</span></label>
                        <input type="text" name="nm_profil" id="field_nm_profil" class="form-control" required placeholder="{{ __('e.g. Software Engineer') }}">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">{{ __('Status') }} <span style="color: red;">*</span></label>
                        <select name="status" id="field_status" class="form-control" required>
                            <option value="Draft">{{ __('Draft') }}</option>
                            <option value="Aktif">{{ __('Aktif') }}</option>
                            <option value="Revisi">{{ __('Revisi') }}</option>
                            <option value="Tidak Aktif">{{ __('Tidak Aktif') }}</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <!-- Left Column -->
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">{{ __('Description') }} <span style="color: red;">*</span></label>
                            <textarea name="deskripsi" id="field_deskripsi" class="form-control" rows="3" required placeholder="{{ __('General description of the profile') }}"></textarea>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">{{ __('Competency (Skills)') }} <span style="color: red;">*</span></label>
                            <textarea name="kompetensi" id="field_kompetensi" class="form-control" rows="3" required placeholder="{{ __('List of competencies required') }}"></textarea>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">{{ __('Stakeholders') }} <span style="color: red;">*</span></label>
                            <textarea name="stakeholders" id="field_stakeholders" class="form-control" rows="3" required placeholder="{{ __('Involved parties or target audience') }}"></textarea>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">{{ __('Career Pathway') }} <span style="color: red;">*</span></label>
                            <textarea name="career_pathway" id="field_career_pathway" class="form-control" rows="3" required placeholder="{{ __('Potential career opportunities') }}"></textarea>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">{{ __('Reference Sources') }} <span style="color: red;">*</span></label>
                            <textarea name="sumber_acuan" id="field_sumber_acuan" class="form-control" rows="3" required placeholder="{{ __('e.g. SKKNI, Vision Mission') }}"></textarea>
                        </div>
                        <div style="display: flex; align-items: flex-end; justify-content: flex-end; flex: 1; margin-top: 1rem;">
                            <div style="display: flex; gap: 0.75rem; width: 100%;">
                                <button type="button" onclick="closeProfileModal()" class="btn" style="flex: 1; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db;">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn btn-primary" style="flex: 1;">{{ __('Save Data') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showProfileModal() {
        document.getElementById('modalTitle').textContent = '{{ __('Add Profile Item') }}';
        document.getElementById('profileForm').action = "{{ route('gp.profile.store', $prodi->id) }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('field_kode_profil').value = '';
        document.getElementById('field_nm_profil').value = '';
        document.getElementById('field_deskripsi').value = '';
        document.getElementById('field_career_pathway').value = '';
        document.getElementById('field_kompetensi').value = '';
        document.getElementById('field_sumber_acuan').value = '';
        document.getElementById('field_stakeholders').value = '';
        document.getElementById('field_status').value = 'Draft';
        document.getElementById('profileModal').style.display = 'flex';
    }

    function editProfile(data) {
        document.getElementById('modalTitle').textContent = '{{ __('Edit Profile Item') }}';
        let updateUrl = "{{ route('gp.profile.update', ':id') }}";
        document.getElementById('profileForm').action = updateUrl.replace(':id', data.id);
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('field_kode_profil').value = data.kode_profil;
        document.getElementById('field_nm_profil').value = data.nm_profil;
        document.getElementById('field_deskripsi').value = data.deskripsi;
        document.getElementById('field_career_pathway').value = data.career_pathway;
        document.getElementById('field_kompetensi').value = data.kompetensi;
        document.getElementById('field_sumber_acuan').value = data.sumber_acuan;
        document.getElementById('field_stakeholders').value = data.stakeholders;
        document.getElementById('field_status').value = data.status;
        document.getElementById('profileModal').style.display = 'flex';
    }

    function closeProfileModal() {
        document.getElementById('profileModal').style.display = 'none';
    }

    // Attach click handlers for edit buttons
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.edit-profile-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var data = {
                    id: this.getAttribute('data-id'),
                    kode_profil: this.getAttribute('data-kode_profil'),
                    nm_profil: this.getAttribute('data-nm_profil'),
                    deskripsi: this.getAttribute('data-deskripsi'),
                    career_pathway: this.getAttribute('data-career_pathway'),
                    kompetensi: this.getAttribute('data-kompetensi'),
                    sumber_acuan: this.getAttribute('data-sumber_acuan'),
                    stakeholders: this.getAttribute('data-stakeholders'),
                    status: this.getAttribute('data-status')
                };
                editProfile(data);
            });
        });
    });
</script>
@endsection
