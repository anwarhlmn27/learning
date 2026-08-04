@extends('layouts.admin')

@section('title', __('Edit Subject'))

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
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">{{ __('Edit Subject') }}</h1>
@endsection

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h4 class="card-title">{{ __('Subject Form') }}</h4>
        <a href="{{ route('subjects.index') }}" class="btn btn-warning btn-sm">{{ __('Back') }}</a>
    </div>
    <div class="card-body">


        <form action="{{ route('subjects.update', $subject->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">{{ __('Program Studi') }} <span style="color: red;">*</span></label>
                <select name="id_prodi" class="form-control" required>
                    <option value="">-- {{ __('Select Program Studi') }} --</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}" {{ old('id_prodi', $subject->id_prodi) == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama_prodi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('Subject Code') }} <span style="color: red;">*</span></label>
                    <input type="text" name="kode_subject" class="form-control" placeholder="{{ __('INF101') }}" required value="{{ old('kode_subject', $subject->kode_subject) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Subject Name') }} <span style="color: red;">*</span></label>
                    <input type="text" name="nama_subject" class="form-control" placeholder="{{ __('Introduction to Computer Science') }}" required value="{{ old('nama_subject', $subject->nama_subject) }}">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">{{ __('Deskripsi') }} <span style="color: red;">*</span></label>
                <textarea name="deskripsi" class="form-control" rows="3" required placeholder="{{ __('Isi dan tujuan MK') }}">{{ old('deskripsi', $subject->deskripsi) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('SKS Theory (T)') }} <span style="color: red;">*</span></label>
                    <input type="number" name="sks_t" id="sks_t" class="form-control" min="0" required value="{{ old('sks_t', $subject->sks_t) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('SKS Practice (P)') }} <span style="color: red;">*</span></label>
                    <input type="number" name="sks_p" id="sks_p" class="form-control" min="0" required value="{{ old('sks_p', $subject->sks_p) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('SKS Praktik Lapangan (PL)') }} <span style="color: red;">*</span></label>
                    <input type="number" name="sks_pl" id="sks_pl" class="form-control" min="0" required value="{{ old('sks_pl', $subject->sks_pl) }}">
                </div>
                <div class="form-group">
                    <label class="form-label"><b>{{ __('Total SKS') }}</b></label>
                    <input type="number" name="total_sks" id="total_sks" class="form-control" readonly value="{{ old('total_sks', $subject->total_sks) }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('Semester') }} <span style="color: red;">*</span></label>
                    <select name="semester" class="form-control" required>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('semester', $subject->semester) == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Jenis Subject') }} <span style="color: red;">*</span></label>
                    <select name="jenis_subject" class="form-control" required>
                        <option value="Wajib Prodi" {{ old('jenis_subject', $subject->jenis_subject) == 'Wajib Prodi' ? 'selected' : '' }}>Wajib Prodi</option>
                        <option value="Wajib Universitas" {{ old('jenis_subject', $subject->jenis_subject) == 'Wajib Universitas' ? 'selected' : '' }}>Wajib Universitas</option>
                        <option value="Pilihan" {{ old('jenis_subject', $subject->jenis_subject) == 'Pilihan' ? 'selected' : '' }}>Pilihan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Status') }} <span style="color: red;">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="Aktif" {{ old('status', $subject->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Revisi" {{ old('status', $subject->status) == 'Revisi' ? 'selected' : '' }}>Revisi</option>
                        <option value="Tidak Aktif" {{ old('status', $subject->status) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">{{ __('Prerequisite Subjects') }} </label>
                <div class="custom-multiselect-container" style="position: relative; width: 100%;">
                    <!-- Control box (the input box that shows selected items) -->
                    <div class="multiselect-control" id="prereq-control" style="min-height: 38px; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.375rem 0.75rem; background: #fff; cursor: pointer; display: flex; flex-wrap: wrap; gap: 0.25rem; align-items: center; justify-content: space-between; transition: border-color 0.15s, box-shadow 0.15s;">
                        <div id="prereq-placeholder" style="color: #6b7280; font-size: 0.875rem;">{{ __('Select Prerequisite Subjects...') }}</div>
                        <div id="prereq-tags" style="display: flex; flex-wrap: wrap; gap: 0.25rem; align-items: center;"></div>
                        <span style="font-size: 0.75rem; color: #6b7280; margin-left: auto;">▼</span>
                    </div>

                    <!-- Dropdown list -->
                    <div class="multiselect-dropdown" id="prereq-dropdown" style="display: none; position: absolute; top: 105%; left: 0; right: 0; background: #fff; border: 1px solid #d1d5db; border-radius: 0.375rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); z-index: 50; max-height: 250px; overflow-y: auto; padding: 0.5rem;">
                        <!-- Search bar inside dropdown -->
                        <input type="text" id="prereq-search" placeholder="{{ __('Search subject...') }}" style="width: 100%; border: 1px solid #e5e7eb; border-radius: 0.25rem; padding: 0.375rem 0.5rem; margin-bottom: 0.5rem; font-size: 0.875rem; outline: none; box-sizing: border-box;" onclick="event.stopPropagation()">
                        
                        <!-- Options -->
                        <div id="prereq-options-list">
                            @php
                                $selectedPrereqs = old('prerequisite_ids', $subject->prerequisites->pluck('id')->toArray());
                            @endphp
                            @foreach($subjects as $s)
                                <label class="multiselect-option" data-search-text="{{ strtolower($s->kode_subject . ' ' . $s->nama_subject) }}" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; cursor: pointer; user-select: none; transition: background 0.15s; margin-bottom: 2px;">
                                    <input type="checkbox" name="prerequisite_ids[]" value="{{ $s->id }}" class="prereq-checkbox" style="width: 16px; height: 16px; accent-color: var(--primary);" {{ is_array($selectedPrereqs) && in_array($s->id, $selectedPrereqs) ? 'checked' : '' }}>
                                    <span>[{{ $s->kode_subject }}] {{ $s->nama_subject }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ __('You can select multiple prerequisite subjects for this course.') }}</small>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">{{ __('Bahan Kajian (Mapping)') }} </label>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background: #f9fafb; max-height: 150px; overflow-y: auto;">
                    @php
                        $selectedBks = old('bks', $subject->bks->pluck('id')->toArray());
                    @endphp
                    @foreach($bks as $bk)
                        <label style="display: flex; align-items: flex-start; gap: 0.4rem; font-size: 0.85rem; cursor: pointer;">
                            <input type="checkbox" name="bks[]" value="{{ $bk->id }}" {{ is_array($selectedBks) && in_array($bk->id, $selectedBks) ? 'checked' : '' }}
                                style="accent-color: var(--primary); width: 16px; height: 16px; margin-top: 0.2rem;">
                            <span><strong>{{ $bk->kode_bk }}</strong><br><span style="color: var(--text-muted); font-size: 0.75rem;">{{ $bk->nm_bahan_kajian }}</span></span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">{{ __('PLO (Mapping)') }} </label>
                <div style="padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background: #f9fafb; max-height: 250px; overflow-y: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <th style="text-align: left; padding: 0.5rem; font-size: 0.75rem;">{{ __('PLO Code') }}</th>
                                <th style="text-align: left; padding: 0.5rem; font-size: 0.75rem;">{{ __('Level Mapping') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $selectedPlos = old('plos', $subject->plos->pluck('id')->toArray());
                                $ploMappings = $subject->plos->pluck('pivot.mapping_level', 'id')->toArray();
                            @endphp
                            @foreach($plos as $plo)
                                @php
                                    $isMapped = is_array($selectedPlos) && in_array($plo->id, $selectedPlos);
                                    $currentLevel = old("plo_levels.$plo->id", $ploMappings[$plo->id] ?? 'I');
                                @endphp
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 0.5rem;">
                                        <label style="display: flex; align-items: flex-start; gap: 0.4rem; font-size: 0.85rem; cursor: pointer;">
                                            <input type="checkbox" name="plos[]" value="{{ $plo->id }}" {{ $isMapped ? 'checked' : '' }}
                                                class="plo-checkbox" data-plo-id="{{ $plo->id }}"
                                                style="accent-color: var(--primary); width: 16px; height: 16px; margin-top: 0.2rem;">
                                            <span><strong>{{ $plo->kode_plo }}</strong><br><span style="color: var(--text-muted); font-size: 0.75rem;">{{ $plo->plo_title }}</span></span>
                                        </label>
                                    </td>
                                    <td style="padding: 0.5rem;">
                                        <select name="plo_levels[{{ $plo->id }}]" class="form-control plo-level-select" style="padding: 0.25rem; font-size: 0.75rem; width: auto;" 
                                            {{ $isMapped ? '' : 'disabled' }}>
                                            <option value="I" {{ $currentLevel == 'I' ? 'selected' : '' }}>I - Introduced</option>
                                            <option value="R" {{ $currentLevel == 'R' ? 'selected' : '' }}>R - Reinforced</option>
                                            <option value="M" {{ $currentLevel == 'M' ? 'selected' : '' }}>M - Mastered</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="margin-top: 1rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">{{ __('Update Data') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sksT = document.getElementById('sks_t');
        const sksP = document.getElementById('sks_p');
        const sksPl = document.getElementById('sks_pl');
        const totalSks = document.getElementById('total_sks');

        function calculateTotal() {
            totalSks.value = (parseInt(sksT.value) || 0) + (parseInt(sksP.value) || 0) + (parseInt(sksPl.value) || 0);
        }

        sksT.addEventListener('input', calculateTotal);
        sksP.addEventListener('input', calculateTotal);
        sksPl.addEventListener('input', calculateTotal);

        // Handle PLO checkbox and level select
        const ploCheckboxes = document.querySelectorAll('.plo-checkbox');
        ploCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const ploId = this.dataset.ploId;
                const select = document.querySelector(`select[name="plo_levels[${ploId}]"]`);
                if (select) {
                    select.disabled = !this.checked;
                }
            });
        });

        // Custom Multi-select Combo Box logic
        const control = document.getElementById('prereq-control');
        const dropdown = document.getElementById('prereq-dropdown');
        const searchInput = document.getElementById('prereq-search');
        const checkboxes = document.querySelectorAll('.prereq-checkbox');
        const placeholder = document.getElementById('prereq-placeholder');
        const tagsContainer = document.getElementById('prereq-tags');
        const optionsList = document.querySelectorAll('.multiselect-option');

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
            optionsList.forEach(opt => {
                const text = opt.dataset.searchText;
                if (text.includes(query)) {
                    opt.style.display = 'flex';
                } else {
                    opt.style.display = 'none';
                }
            });
        });

        // Update tags and display
        function updateTags() {
            tagsContainer.innerHTML = '';
            let checkedCount = 0;

            checkboxes.forEach(cb => {
                if (cb.checked) {
                    checkedCount++;
                    const text = cb.nextElementSibling.textContent;
                    
                    // Create badge
                    const badge = document.createElement('span');
                    badge.style.cssText = 'background: var(--primary); color: white; padding: 0.2rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 500; margin: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);';
                    badge.innerHTML = `${text} <span class="remove-badge" data-val="${cb.value}" style="cursor: pointer; font-weight: bold; font-size: 0.8rem; margin-left: 4px; opacity: 0.8; transition: opacity 0.15s;">&times;</span>`;
                    
                    // Remove on click
                    badge.querySelector('.remove-badge').addEventListener('mouseover', function() {
                        this.style.opacity = '1';
                    });
                    badge.querySelector('.remove-badge').addEventListener('mouseout', function() {
                        this.style.opacity = '0.8';
                    });
                    badge.querySelector('.remove-badge').addEventListener('click', function(e) {
                        e.stopPropagation();
                        cb.checked = false;
                        updateTags();
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

        // Checkbox change event
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateTags);
        });

        // Initialize tags on load (for old input or edit screen)
        updateTags();
    });
</script>
@endsection
