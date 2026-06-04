@extends('layouts.admin')

@section('title', 'Manage PLO - ' . $prodi->nama_prodi)

@section('styles')
<style>
    .multiselect-control:hover {
        border-color: #9ca3af !important;
    }
    .multiselect-control:focus-within {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }
    .multiselect-option:hover {
        background-color: #f3f4f6;
    }
</style>
@endsection

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
        <span>Program Learning Outcomes (PLO)</span>
        <button onclick="showPloModal()" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">+ Add PLO</button>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('PLO Code') }}</th>
                        <th>{{ __('PLO Title') }}</th>
                        <th>{{ __('GP References') }}</th>
                        <th>Outcome (Rumusan)</th>
                        <th>{{ __('Domain') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prodi->plos as $plo)
                        <tr>
                            <td style="font-weight: 600; color: var(--primary);">{{ $plo->kode_plo }}</td>
                            <td style="font-weight: 600;">{{ $plo->plo_title }}</td>
                            <td>
                                <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                    @forelse($plo->gps as $gp)
                                        <span class="badge" style="background: #f3f4f6; color: #374151; font-size: 0.7rem;">{{ $gp->kode_profil }}</span>
                                    @empty
                                        <span class="badge" style="background: #fee2e2; color: #b91c1c; font-size: 0.7rem;">No GP</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>{{ Str::limit($plo->rumusan_plo, 80) }}</td>
                            <td>
                                <span class="badge" style="background: #e0e7ff; color: #4338ca;">{{ $plo->domain }}</span>
                            </td>
                            <td>
                                <span style="padding: 0.125rem 0.5rem; border-radius: 1rem; font-size: 0.75rem; 
                                    {{ $plo->status == 'Aktif' ? 'background: #dcfce7; color: #166534;' : 
                                       ($plo->status == 'Draft' ? 'background: #f3f4f6; color: #374151;' : 
                                       ($plo->status == 'Revisi' ? 'background: #fef9c3; color: #854d0e;' : 'background: #fee2e2; color: #b91c1c;')) }}">
                                    {{ $plo->status }}
                                </span>
                            </td>
                            <td style="display: flex; gap: 0.5rem;">
                                @php
                                    $ploData = $plo->toArray();
                                    $ploData['id_gps'] = $plo->gps->pluck('id')->toArray();
                                @endphp
                                <button onclick='editPlo(@json($ploData))' class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</button>
                                <form action="{{ route('plo.destroy', $plo->id) }}" method="POST" onsubmit="return confirm('Delete this PLO?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align: center; color: var(--text-muted);">No PLOs added yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- PLO MODAL -->
<div id="ploModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 800px; margin: 0; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
        <div class="card-header" style="flex-shrink: 0; display: flex; justify-content: space-between; align-items: center;">
            <span id="modalTitle" style="font-weight: 600; font-size: 1.125rem;">Add PLO Item</span>
            <button type="button" onclick="closePloModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted); line-height: 1;">&times;</button>
        </div>
        <div class="card-body" style="overflow-y: auto; padding: 1.5rem;">
            <form id="ploForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" style="font-weight: 600; color: var(--primary);">{{ __('Graduate Profile (GP) References') }} <span style="color: red;">*</span></label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.5rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: #f9fafb;">
                        @forelse($prodi->gps as $gp)
                            <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; cursor: pointer;">
                                <input type="checkbox" name="id_gps[]" value="{{ $gp->id }}" class="gp-checkbox" style="width: 1rem; height: 1rem;">
                                <span><strong>{{ $gp->kode_profil }}</strong> - {{ Str::limit($gp->nm_profil, 20) }}</span>
                            </label>
                        @empty
                            <span style="color: var(--text-muted); font-size: 0.875rem;">No Graduate Profiles found. Please add GP first.</span>
                        @endforelse
                    </div>
                    @error('id_gps')
                        <div style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">{{ __('PLO Code') }} <span style="color: red;">*</span></label>
                        <input type="text" name="kode_plo" id="field_kode_plo" class="form-control" required placeholder="{{ __('e.g. PLO-01') }}">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">{{ __('PLO Title') }} <span style="color: red;">*</span></label>
                        <input type="text" name="plo_title" id="field_plo_title" class="form-control" required placeholder="{{ __('e.g. Computing Fundamentals') }}">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">{{ __('Domain') }} <span style="color: red;">*</span></label>
                        <select name="domain" id="field_domain" class="form-control" required>
                            <option value="Knowledge">Knowledge</option>
                            <option value="Skill">Skill</option>
                            <option value="Attitude">Attitude</option>
                            <option value="General Competency">General Competency</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">{{ __('Status') }} <span style="color: red;">*</span></label>
                        <select name="status" id="field_status" class="form-control" required>
                            <option value="Draft">Draft</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Revisi">Revisi</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <!-- Left Column -->
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">{{ __('Outcome (Rumusan PLO)') }} <span style="color: red;">*</span></label>
                            <textarea name="rumusan_plo" id="field_rumusan_plo" class="form-control" rows="3" required placeholder="{{ __('Outcome statement') }}"></textarea>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">{{ __('Bloom Level') }} <span style="color: red;">*</span></label>
                                <select name="bloom_level" id="field_bloom_level" class="form-control" required onchange="updateKKO()">
                                    <option value="">-- Pilih Level --</option>
                                    <option value="C1">C1 - Remember</option>
                                    <option value="C2">C2 - Understand</option>
                                    <option value="C3">C3 - Apply</option>
                                    <option value="C4">C4 - Analyze</option>
                                    <option value="C5">C5 - Evaluate</option>
                                    <option value="C6">C6 - Create</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">{{ __('KKO') }} <span style="color: red;">*</span></label>
                                <div class="custom-multiselect-container" style="position: relative; width: 100%;">
                                    <!-- Control box -->
                                    <div class="multiselect-control" id="kko-control" style="min-height: 38px; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.375rem 0.75rem; background: #fff; cursor: pointer; display: flex; flex-wrap: wrap; gap: 0.25rem; align-items: center; justify-content: space-between; transition: border-color 0.15s, box-shadow 0.15s;">
                                        <div id="kko-placeholder" style="color: #6b7280; font-size: 0.875rem;">Pilih KKO...</div>
                                        <div id="kko-tags" style="display: flex; flex-wrap: wrap; gap: 0.25rem; align-items: center;"></div>
                                        <span style="font-size: 0.75rem; color: #6b7280; margin-left: auto;">▼</span>
                                    </div>

                                    <!-- Dropdown list -->
                                    <div class="multiselect-dropdown" id="kko-dropdown" style="display: none; position: absolute; top: 105%; left: 0; right: 0; background: #fff; border: 1px solid #d1d5db; border-radius: 0.375rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); z-index: 50; max-height: 250px; overflow-y: auto; padding: 0.5rem;">
                                        <!-- Search bar inside dropdown -->
                                        <input type="text" id="kko-search" placeholder="{{ __('Cari KKO...') }}" style="width: 100%; border: 1px solid #e5e7eb; border-radius: 0.25rem; padding: 0.375rem 0.5rem; margin-bottom: 0.5rem; font-size: 0.875rem; outline: none; box-sizing: border-box;" onclick="event.stopPropagation()">
                                        
                                        <!-- Options -->
                                        <div id="kko-options-list">
                                            <!-- Dynamically populated options -->
                                        </div>
                                    </div>
                                </div>
                                <small style="color: var(--text-muted); font-size: 0.7rem; display: block; margin-top: 0.25rem;">Pilih KKO dari daftar di atas.</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">{{ __('Indicators of Achievement') }} <span style="color: red;">*</span></label>
                            <textarea name="indikator_ketercapaian" id="field_indikator_ketercapaian" class="form-control" rows="2" required placeholder="{{ __('Achievement indicators') }}"></textarea>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">{{ __('Target') }} <span style="color: red;">*</span></label>
                                <input type="text" name="target_capaian" id="field_target_capaian" class="form-control" required placeholder="{{ __('e.g. 75%') }}">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">{{ __('Measurement') }} <span style="color: red;">*</span></label>
                                <select name="metode_pengukuran" id="field_metode_pengukuran" class="form-control" required>
                                    <option value="Direct">Direct</option>
                                    <option value="Indirect">Indirect</option>
                                    <option value="Both">Both</option>
                                </select>
                            </div>
                        </div>
                        <div style="display: flex; align-items: flex-end; justify-content: flex-end; flex: 1; margin-top: 1rem;">
                            <div style="display: flex; gap: 0.75rem; width: 100%;">
                                <button type="button" onclick="closePloModal()" class="btn" style="flex: 1; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db;">Cancel</button>
                                <button type="submit" class="btn btn-primary" style="flex: 1;">Save PLO</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // const mappingKKO = {
    //     'C1': ['Mengingat', 'Menyebutkan', 'Menghafal'],
    //     'C2': ['Menjelaskan', 'Mengklasifikasikan', 'Merangkum'],
    //     'C3': ['Menerapkan', 'Menggunakan', 'Mendemonstrasikan'],
    //     'C4': ['Menganalisis', 'Memecahkan', 'Membandingkan'],
    //     'C5': ['Mengevaluasi', 'Mengkritik', 'Menyimpulkan'],
    //     'C6': ['Mendesain', 'Membangun', 'Merancang']
    // };
    const mappingKKO = {
        'C1': [
            'Mengingat', 'Menyebutkan', 'Menghafal', 'Mencatat', 'Mengulang', 
            'Menunjukkan', 'Menyatakan', 'Mengenali', 'Membaca', 'Menuliskan', 
            'Mendaftar', 'Memilih', 'Mendefinisikan'
        ],
        'C2': [
            'Menjelaskan', 'Mengklasifikasikan', 'Merangkum', 'Mengidentifikasi', 'Menguraikan', 
            'Menginterpretasikan', 'Mengubah', 'Memperkirakan', 'Menerjemahkan', 'Mencontohkan', 
            'Membedakan', 'Menjustifikasi', 'Memetakan', 'Menerangkan', 'Mengasosiasikan'
        ],
        'C3': [
            'Menerapkan', 'Menggunakan', 'Mendemonstrasikan', 'Mengimplementasikan', 'Menghitung', 
            'Menjalankan', 'Mengoperasikan', 'Membuat', 'Memanipulasi', 'Memodifikasi', 
            'Menyesuaikan', 'Memecahkan Masalah', 'Mempraktikkan', 'Menjadwalkan', 'Menentukan'
        ],
        'C4': [
            'Menganalisis', 'Memecahkan', 'Membandingkan', 'Mendiagnosis', 'Mengaudit', 
            'Menelaah', 'Menguji', 'Menemukan', 'Mengoreksi', 'Memisahkan', 
            'Menghubungkan', 'Bagi Menjadi Bagian', 'Mendeteksi', 'Mengorganisasikan', 'Menstrukturkan'
        ],
        'C5': [
            'Mengevaluasi', 'Mengkritik', 'Menyimpulkan', 'Menilai', 'Merekomendasikan', 
            'Memvalidasi', 'Memprediksi', 'Memutuskan', 'Memilih Terbaik', 'Mengukur', 
            'Mempertahankan', 'Memproyeksikan', 'Mendukung', 'Menimbang', 'Menaksir'
        ],
        'C6': [
            'Mendesain', 'Membangun', 'Merancang', 'Mengembangkan', 'Membuat Baru', 
            'Menciptakan', 'Memformulasikan', 'Menyusun', 'Merakit', 'Mengintegrasikan', 
            'Mengonstruksi', 'Membuat Prototipe', 'Memproduksi', 'Menghasilkan', 'Menginisiasi'
        ]
    };

    function updateKKO() {
        const level = document.getElementById('field_bloom_level').value;
        const optionsList = document.getElementById('kko-options-list');
        
        // Kosongkan daftar KKO sebelumnya
        optionsList.innerHTML = '';
        
        if (level && mappingKKO[level]) {
            mappingKKO[level].forEach(kko => {
                let label = document.createElement('label');
                label.className = 'multiselect-option';
                label.dataset.searchText = kko.toLowerCase();
                label.style.cssText = 'display: flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; cursor: pointer; user-select: none; transition: background 0.15s; margin-bottom: 2px;';
                
                let checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'kko[]';
                checkbox.value = kko;
                checkbox.className = 'kko-checkbox';
                checkbox.style.cssText = 'width: 16px; height: 16px; accent-color: var(--primary);';
                
                let span = document.createElement('span');
                span.textContent = kko;
                
                label.appendChild(checkbox);
                label.appendChild(span);
                
                optionsList.appendChild(label);
                
                // Add event listener to checkbox
                checkbox.addEventListener('change', updateKKOTags);
            });
        }
        
        // Update tags after dynamic rebuild
        updateKKOTags();
    }

    function updateKKOTags() {
        const tagsContainer = document.getElementById('kko-tags');
        const placeholder = document.getElementById('kko-placeholder');
        const checkboxes = document.querySelectorAll('.kko-checkbox');
        
        tagsContainer.innerHTML = '';
        let checkedCount = 0;

        checkboxes.forEach(cb => {
            if (cb.checked) {
                checkedCount++;
                const text = cb.nextElementSibling.textContent;
                
                // Create badge
                const badge = document.createElement('span');
                badge.style.cssText = 'background: var(--primary); color: white; padding: 0.2rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 500; margin: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);';
                badge.innerHTML = `${text} <span class="remove-kko-badge" data-val="${cb.value}" style="cursor: pointer; font-weight: bold; font-size: 0.8rem; margin-left: 4px; opacity: 0.8; transition: opacity 0.15s;">&times;</span>`;
                
                // Remove on click
                badge.querySelector('.remove-kko-badge').addEventListener('mouseover', function() {
                    this.style.opacity = '1';
                });
                badge.querySelector('.remove-kko-badge').addEventListener('mouseout', function() {
                    this.style.opacity = '0.8';
                });
                badge.querySelector('.remove-kko-badge').addEventListener('click', function(e) {
                    e.stopPropagation();
                    cb.checked = false;
                    updateKKOTags();
                });

                tagsContainer.appendChild(badge);
            }
        });

        if (checkedCount > 0) {
            placeholder.style.display = 'none';
        } else {
            placeholder.style.display = 'block';
        }
    }

    function showPloModal() {
        document.getElementById('modalTitle').textContent = 'Add PLO Item';
        document.getElementById('ploForm').action = "{{ route('plo.store', $prodi->id) }}";
        document.getElementById('formMethod').value = 'POST';
        
        // Reset checkboxes
        document.querySelectorAll('.gp-checkbox').forEach(cb => cb.checked = false);
        
        document.getElementById('field_kode_plo').value = '';
        document.getElementById('field_plo_title').value = '';
        document.getElementById('field_rumusan_plo').value = '';
        document.getElementById('field_domain').value = 'Knowledge';
        document.getElementById('field_bloom_level').value = '';
        updateKKO(); // Clear KKO options & tags
        document.getElementById('field_indikator_ketercapaian').value = '';
        document.getElementById('field_target_capaian').value = '';
        document.getElementById('field_metode_pengukuran').value = 'Direct';
        document.getElementById('field_status').value = 'Draft';
        
        document.getElementById('ploModal').style.display = 'flex';
    }

    function editPlo(plo) {
        document.getElementById('modalTitle').textContent = 'Edit PLO Item';
        let updateUrl = "{{ route('plo.update', ':id') }}";
        document.getElementById('ploForm').action = updateUrl.replace(':id', plo.id);
        document.getElementById('formMethod').value = 'PUT';
        
        // Set checkboxes
        document.querySelectorAll('.gp-checkbox').forEach(cb => {
            cb.checked = plo.id_gps.includes(cb.value);
        });
        
        document.getElementById('field_kode_plo').value = plo.kode_plo;
        document.getElementById('field_plo_title').value = plo.plo_title;
        document.getElementById('field_rumusan_plo').value = plo.rumusan_plo;
        document.getElementById('field_domain').value = plo.domain;
        document.getElementById('field_bloom_level').value = plo.bloom_level;
        
        updateKKO(); // Populate KKO based on the selected bloom level
        
        // Set KKO checkboxes
        if (plo.kko) {
            const selectedKKOs = plo.kko.split(', ');
            document.querySelectorAll('.kko-checkbox').forEach(cb => {
                if (selectedKKOs.includes(cb.value)) {
                    cb.checked = true;
                }
            });
        }
        updateKKOTags(); // Render the tags
        
        document.getElementById('field_indikator_ketercapaian').value = plo.indikator_ketercapaian;
        document.getElementById('field_target_capaian').value = plo.target_capaian;
        document.getElementById('field_metode_pengukuran').value = plo.metode_pengukuran;
        document.getElementById('field_status').value = plo.status;
        
        document.getElementById('ploModal').style.display = 'flex';
    }

    function closePloModal() {
        document.getElementById('ploModal').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const control = document.getElementById('kko-control');
        const dropdown = document.getElementById('kko-dropdown');
        const searchInput = document.getElementById('kko-search');

        // Toggle dropdown
        control.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = dropdown.style.display === 'block';
            dropdown.style.display = isOpen ? 'none' : 'block';
            if (!isOpen) {
                searchInput.focus();
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-multiselect-container')) {
                dropdown.style.display = 'none';
            }
        });

        // Filter search
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const optionsList = document.querySelectorAll('.multiselect-option');
            optionsList.forEach(opt => {
                const text = opt.dataset.searchText;
                if (text.includes(query)) {
                    opt.style.display = 'flex';
                } else {
                    opt.style.display = 'none';
                }
            });
        });
    });

    // Auto-open modal if validation errors exist
    @if($errors->any() && !$errors->has('error'))
        showPloModal();
    @endif
</script>
@endsection
