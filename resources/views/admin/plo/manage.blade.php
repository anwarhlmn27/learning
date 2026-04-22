@extends('layouts.admin')

@section('title', 'Manage PLO - ' . $prodi->nama_prodi)

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Manage PLO: {{ $prodi->nama_prodi }}</h1>
@endsection

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('plo.index') }}" style="color: var(--text-muted); font-size: 0.875rem;">← Back to List</a>
</div>

@if($errors->has('error'))
    <div class="alert alert-danger" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        {{ $errors->first('error') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <span>PLO List</span>
        <button onclick="showPloModal()" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">+ Add PLO</button>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>GP Reference</th>
                        <th>PLO Code</th>
                        <th>PLO Content</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prodi->plos as $plo)
                        <tr>
                            <td>
                                <span class="badge" style="background: #f3f4f6; color: #374151;">{{ $plo->gp->nm_profil ?? 'N/A' }}</span>
                            </td>
                            <td style="font-weight: 600;">{{ $plo->title_plo }}</td>
                            <td>{{ Str::limit($plo->plo, 100) }}</td>
                            <td style="display: flex; gap: 0.5rem;">
                                <button onclick='editPlo({!! json_encode($plo) !!})' class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</button>
                                <form action="{{ route('plo.destroy', $plo->id) }}" method="POST" onsubmit="return confirm('Delete this PLO?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No PLOs added yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- PLO MODAL -->
<div id="ploModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 600px; margin: 0;">
        <div class="card-header">
            <span id="modalTitle">Add PLO Item</span>
            <button onclick="closePloModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <div class="card-body">
            <form id="ploForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="form-group">
                    <label class="form-label">Graduate Profile (GP) Reference</label>
                    <select name="id_gp" id="field_id_gp" class="form-control @error('id_gp') is-invalid @enderror" required>
                        <option value="">-- Select GP --</option>
                        @foreach($prodi->gps as $gp)
                            <option value="{{ $gp->id }}">{{ $gp->nm_profil }}</option>
                        @endforeach
                    </select>
                    @error('id_gp')
                        <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">PLO Code</label>
                    <input type="text" name="title_plo" id="field_title_plo" class="form-control @error('title_plo') is-invalid @enderror" required placeholder="e.g. PLO-01">
                    @error('title_plo')
                        <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">PLO Content</label>
                    <textarea name="plo" id="field_plo" class="form-control @error('plo') is-invalid @enderror" rows="3" required placeholder="Outcome statement"></textarea>
                    @error('plo')
                        <div class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Detail (Optional)</label>
                    <textarea name="detail" id="field_detail" class="form-control" rows="2"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Description (Optional)</label>
                    <textarea name="deskripsi" id="field_deskripsi" class="form-control" rows="2"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                    <button type="button" onclick="closePloModal()" class="btn" style="background: #e5e7eb;">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save PLO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showPloModal() {
        document.getElementById('modalTitle').textContent = 'Add PLO Item';
        document.getElementById('ploForm').action = "{{ route('plo.store', $prodi->id) }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('field_id_gp').value = '';
        document.getElementById('field_title_plo').value = '';
        document.getElementById('field_plo').value = '';
        document.getElementById('field_detail').value = '';
        document.getElementById('field_deskripsi').value = '';
        document.getElementById('ploModal').style.display = 'flex';
    }

    function editPlo(plo) {
        document.getElementById('modalTitle').textContent = 'Edit PLO Item';
        document.getElementById('ploForm').action = "/admin/plo/" + plo.id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('field_id_gp').value = plo.id_gp;
        document.getElementById('field_title_plo').value = plo.title_plo;
        document.getElementById('field_plo').value = plo.plo;
        document.getElementById('field_detail').value = plo.detail || '';
        document.getElementById('field_deskripsi').value = plo.deskripsi || '';
        document.getElementById('ploModal').style.display = 'flex';
    }

    function closePloModal() {
        document.getElementById('ploModal').style.display = 'none';
    }

    // Auto-open modal if validation errors exist
    @if($errors->any() && !$errors->has('error'))
        @if(old('_method') == 'PUT')
            // Note: This would need the ID of the item being edited, 
            // for simplicity we just show the "Add" state or you can refine this.
            showPloModal();
        @else
            showPloModal();
        @endif
    @endif
</script>
@endsection
