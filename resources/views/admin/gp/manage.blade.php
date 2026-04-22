@extends('layouts.admin')

@section('title', 'Manage Graduate Profile - ' . $prodi->nama_prodi)

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Manage GP: {{ $prodi->nama_prodi }}</h1>
@endsection

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('gp.index') }}" style="color: var(--text-muted); font-size: 0.875rem;">← Back to List</a>
</div>

@if(session('success'))
    <div class="alert alert-success" style="padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

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
            <span>Profile Items (Expertise)</span>
            <button onclick="showProfileModal()" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">+ Add Item</button>
        </div>
        <div class="card-body" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Profile Name</th>
                            <th>Expertise</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prodi->gps as $gp)
                            <tr>
                                <td style="font-weight: 600;">{{ $gp->nm_profil }}</td>
                                <td>{{ Str::limit($gp->expertise, 50) }}</td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button type="button" class="btn btn-primary edit-profile-btn" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;"
                                            data-id="{{ $gp->id }}"
                                            data-nm_profil="{{ $gp->nm_profil }}"
                                            data-deskripsi="{{ $gp->deskripsi }}"
                                            data-expertise="{{ $gp->expertise }}">Edit</button>
                                        <button type="button" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;"
                                            onclick="if(confirm('Delete this profile item?')) document.getElementById('delete-profile-{{ $gp->id }}').submit();">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">No profiles added.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- RIGHT: Supporting Documents -->
    <div class="card">
        <div class="card-header">
            <span>Supporting Documents</span>
        </div>
        <div class="card-body">
            <!-- Upload Form -->
            <form action="{{ route('gp.attachment.store', $prodi->id) }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb;">
                @csrf
                <div class="form-group">
                    <label class="form-label">Document Name</label>
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
                    <label class="form-label">File (PDF, Max 2MB)</label>
                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".pdf" required>
                    @error('file')
                        <small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Upload Attachment</button>
            </form>

            <!-- Attachments List -->
            <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 1rem;">Uploaded Files</h4>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @forelse($prodi->gpAttachments as $att)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                        <div>
                            <div style="font-weight: 600; font-size: 0.875rem;">{{ $att->nm_dokumen }}</div>
                            <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" style="font-size: 0.75rem; color: var(--primary);">View File</a>
                        </div>
                        <button type="button" style="background: none; border: none; color: var(--danger); cursor: pointer; font-size: 1.25rem;"
                            onclick="if(confirm('Remove this attachment?')) document.getElementById('delete-attachment-{{ $att->id }}').submit();">×</button>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-muted); font-size: 0.875rem;">No attachments uploaded.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- PROFILE MODAL -->
<div id="profileModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 0;">
        <div class="card-header">
            <span id="modalTitle">Add Profile Item</span>
            <button onclick="closeProfileModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <div class="card-body">
            <form id="profileForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="form-group">
                    <label class="form-label">Profile Name</label>
                    <input type="text" name="nm_profil" id="field_nm_profil" class="form-control" required placeholder="e.g. Software Engineer">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="deskripsi" id="field_deskripsi" class="form-control" rows="3" required placeholder="General description"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Expertise (Technical Skills)</label>
                    <textarea name="expertise" id="field_expertise" class="form-control" rows="3" required placeholder="List of expertise/competencies"></textarea>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                    <button type="button" onclick="closeProfileModal()" class="btn" style="background: #e5e7eb;">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showProfileModal() {
        document.getElementById('modalTitle').textContent = 'Add Profile Item';
        document.getElementById('profileForm').action = "{{ route('gp.profile.store', $prodi->id) }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('field_nm_profil').value = '';
        document.getElementById('field_deskripsi').value = '';
        document.getElementById('field_expertise').value = '';
        document.getElementById('profileModal').style.display = 'flex';
    }

    function editProfile(id, nm_profil, deskripsi, expertise) {
        document.getElementById('modalTitle').textContent = 'Edit Profile Item';
        document.getElementById('profileForm').action = "/admin/gp/profile/" + id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('field_nm_profil').value = nm_profil;
        document.getElementById('field_deskripsi').value = deskripsi;
        document.getElementById('field_expertise').value = expertise;
        document.getElementById('profileModal').style.display = 'flex';
    }

    function closeProfileModal() {
        document.getElementById('profileModal').style.display = 'none';
    }

    // Attach click handlers for edit buttons
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.edit-profile-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var nm_profil = this.getAttribute('data-nm_profil');
                var deskripsi = this.getAttribute('data-deskripsi');
                var expertise = this.getAttribute('data-expertise');
                editProfile(id, nm_profil, deskripsi, expertise);
            });
        });
    });
</script>
@endsection
