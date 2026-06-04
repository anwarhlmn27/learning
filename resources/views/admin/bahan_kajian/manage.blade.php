@extends('layouts.admin')

@section('title', 'Manage Bahan Kajian - ' . $prodi->nama_prodi)

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Manage Bahan Kajian: {{ $prodi->nama_prodi }}</h1>
@endsection

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('bahan_kajian.index') }}" style="color: var(--text-muted); font-size: 0.875rem;">← Back to List</a>
</div>

@if($errors->has('error'))
    <div class="alert alert-danger" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        {{ $errors->first('error') }}
    </div>
@endif



<div class="card">
    <div class="card-header">
        <span>Bahan Kajian (BK) Items</span>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('bahan_kajian.kategori.manage', $prodi->id) }}" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db;">Manage Categories</a>
            <button onclick="showBkModal()" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">+ Add BK</button>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Code') }}</th>
                        <th>Name (Materi)</th>
                        <th>{{ __('PLO References') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Level') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prodi->bahanKajians as $bk)
                        <tr>
                            <td style="font-weight: 600; color: var(--primary);">{{ $bk->kode_bk }}</td>
                            <td style="font-weight: 600;">
                                {{ $bk->nm_bahan_kajian }}
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal; margin-top: 0.25rem;">{{ Str::limit($bk->deskripsi, 50) }}</div>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                    @forelse($bk->plos as $plo)
                                        <span class="badge" style="background: #f3f4f6; color: #374151; font-size: 0.7rem; padding: 0.125rem 0.375rem; border-radius: 0.25rem;">{{ $plo->kode_plo }}</span>
                                    @empty
                                        <span class="badge" style="background: #fee2e2; color: #b91c1c; font-size: 0.7rem; padding: 0.125rem 0.375rem; border-radius: 0.25rem;">No PLO</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>{{ $bk->kategoriBK ? $bk->kategoriBK->nm_kategori : '-' }}</td>
                            <td>
                                <span class="badge" style="background: #e0e7ff; color: #4338ca; padding: 0.125rem 0.5rem; border-radius: 1rem; font-size: 0.75rem;">{{ $bk->tingkat_kedalaman }}</span>
                            </td>
                            <td>
                                <span style="padding: 0.125rem 0.5rem; border-radius: 1rem; font-size: 0.75rem; 
                                    {{ $bk->status == 'Aktif' ? 'background: #dcfce7; color: #166534;' : 
                                       ($bk->status == 'Revisi' ? 'background: #fef9c3; color: #854d0e;' : 'background: #fee2e2; color: #b91c1c;') }}">
                                    {{ $bk->status }}
                                </span>
                            </td>
                            <td style="display: flex; gap: 0.5rem;">
                                @php
                                    $bkData = $bk->toArray();
                                    $bkData['id_plos'] = $bk->plos->pluck('id')->toArray();
                                @endphp
                                <button onclick='editBk(@json($bkData))' class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</button>
                                <form action="{{ route('bahan_kajian.destroy', $bk->id) }}" method="POST" onsubmit="return confirm('Delete this Bahan Kajian?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">No Bahan Kajian added yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- BK MODAL -->
<div id="bkModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 800px; margin: 0; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
        <div class="card-header" style="flex-shrink: 0; display: flex; justify-content: space-between; align-items: center;">
            <span id="modalTitle" style="font-weight: 600; font-size: 1.125rem;">Add Bahan Kajian</span>
            <button type="button" onclick="closeBkModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted); line-height: 1;">&times;</button>
        </div>
        <div class="card-body" style="overflow-y: auto; padding: 1.5rem;">
            <form id="bkForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" style="font-weight: 600; color: var(--primary);">{{ __('PLO (CPL) References') }} <span style="color: red;">*</span></label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 0.5rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: #f9fafb;">
                        @forelse($prodi->plos as $plo)
                            <label style="display: flex; align-items: flex-start; gap: 0.5rem; font-size: 0.875rem; cursor: pointer;">
                                <input type="checkbox" name="id_plos[]" value="{{ $plo->id }}" class="plo-checkbox" style="width: 1rem; height: 1rem; margin-top: 0.125rem;">
                                <div>
                                    <strong>{{ $plo->kode_plo }}</strong>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ Str::limit($plo->plo_title, 30) }}</div>
                                </div>
                            </label>
                        @empty
                            <span style="color: var(--text-muted); font-size: 0.875rem;">No PLOs found. Please add PLO first.</span>
                        @endforelse
                    </div>
                    @error('id_plos')
                        <div style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">{{ __('Kode BK') }} <span style="color: red;">*</span></label>
                        <input type="text" name="kode_bk" id="field_kode_bk" class="form-control" required placeholder="{{ __('e.g. BK-01') }}">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">{{ __('Nama Bahan Kajian') }} <span style="color: red;">*</span></label>
                        <input type="text" name="nm_bahan_kajian" id="field_nm_bahan_kajian" class="form-control" required placeholder="{{ __('e.g. Software Engineering') }}">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">{{ __('Deskripsi') }} <span style="color: red;">*</span></label>
                    <textarea name="deskripsi" id="field_deskripsi" class="form-control" rows="2" required placeholder="{{ __('Brief description of the study material') }}"></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">{{ __('Sub Bahan Kajian') }} <span style="color: red;">*</span></label>
                    <textarea name="sub_bk" id="field_sub_bk" class="form-control" rows="2" required placeholder="{{ __('Sub-topics separated by comma or new lines') }}"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">{{ __('Kategori') }} <span style="color: red;">*</span></label>
                        <select name="id_kategori_bk" id="field_id_kategori_bk" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            @foreach($prodi->kategoriBks as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nm_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0; display: none;">
                        <label class="form-label">{{ __('Tingkat Kedalaman') }} </label>
                        <select name="tingkat_kedalaman" id="field_tingkat_kedalaman" class="form-control">
                            <option value="">-- Auto Generated --</option>
                            <option value="Introductory">Introductory</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">{{ __('Status') }} <span style="color: red;">*</span></label>
                        <select name="status" id="field_status" class="form-control" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Revisi">Revisi</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">{{ __('Sumber Acuan (References)') }} <span style="color: red;">*</span></label>
                    <textarea name="sumber_acuan" id="field_sumber_acuan" class="form-control" rows="2" required placeholder="{{ __('Books, journals, or standards used') }}"></textarea>
                </div>

                <div style="display: flex; align-items: flex-end; justify-content: flex-end; margin-top: 1rem;">
                    <div style="display: flex; gap: 0.75rem;">
                        <button type="button" onclick="closeBkModal()" class="btn" style="background: #f3f4f6; color: #374151; border: 1px solid #d1d5db;">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Bahan Kajian</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showBkModal() {
        document.getElementById('modalTitle').textContent = 'Add Bahan Kajian';
        document.getElementById('bkForm').action = "{{ route('bahan_kajian.store', $prodi->id) }}";
        document.getElementById('formMethod').value = 'POST';
        
        // Reset checkboxes
        document.querySelectorAll('.plo-checkbox').forEach(cb => cb.checked = false);
        
        document.getElementById('field_kode_bk').value = '';
        document.getElementById('field_nm_bahan_kajian').value = '';
        document.getElementById('field_deskripsi').value = '';
        document.getElementById('field_sub_bk').value = '';
        document.getElementById('field_id_kategori_bk').value = '';
        document.getElementById('field_tingkat_kedalaman').value = 'Introductory';
        document.getElementById('field_sumber_acuan').value = '';
        document.getElementById('field_status').value = 'Aktif';
        
        document.getElementById('bkModal').style.display = 'flex';
    }

    function editBk(bk) {
        document.getElementById('modalTitle').textContent = 'Edit Bahan Kajian';
        let updateUrl = "{{ route('bahan_kajian.update', ':id') }}";
        document.getElementById('bkForm').action = updateUrl.replace(':id', bk.id);
        document.getElementById('formMethod').value = 'PUT';
        
        // Set checkboxes
        document.querySelectorAll('.plo-checkbox').forEach(cb => {
            cb.checked = bk.id_plos.includes(cb.value);
        });
        
        document.getElementById('field_kode_bk').value = bk.kode_bk;
        document.getElementById('field_nm_bahan_kajian').value = bk.nm_bahan_kajian;
        document.getElementById('field_deskripsi').value = bk.deskripsi;
        document.getElementById('field_sub_bk').value = bk.sub_bk;
        document.getElementById('field_id_kategori_bk').value = bk.id_kategori_bk;
        document.getElementById('field_tingkat_kedalaman').value = bk.tingkat_kedalaman;
        document.getElementById('field_sumber_acuan').value = bk.sumber_acuan;
        document.getElementById('field_status').value = bk.status;
        
        document.getElementById('bkModal').style.display = 'flex';
    }

    function closeBkModal() {
        document.getElementById('bkModal').style.display = 'none';
    }

    // Auto-open modal if validation errors exist
    @if($errors->any() && !$errors->has('error'))
        showBkModal();
    @endif
</script>
@endsection
