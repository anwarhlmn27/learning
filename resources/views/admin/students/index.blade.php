@extends('layouts.lms')

@section('header_title', __('Data Mahasiswa'))

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2 style="margin: 0; font-size: 1.5rem; color: #111827;">{{ __('Data Mahasiswa') }}</h2>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('mahasiswa.template') }}" class="btn btn-outline btn-sm" style="background: #f3f4f6; color: #374151; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
            <i>📥</i> {{ __('Download Template') }}
        </a>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('modal-add').style.display = 'flex'">
            <i>➕</i> {{ __('Add Mahasiswa') }}
        </button>
        <button class="btn btn-success btn-sm" onclick="document.getElementById('modal-import').style.display = 'flex'">
            <i>📄</i> {{ __('Import CSV') }}
        </button>
    </div>
</div>

<!-- Filter Form -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form action="{{ route('mahasiswa.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label">{{ __('Search') }}</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="{{ __('Name, NIM, or Email') }}" value="{{ request('search') }}">
            </div>
            <div style="width: 200px;">
                <label class="form-label">{{ __('Prodi') }}</label>
                <select name="prodi" class="form-control form-control-sm">
                    <option value="">{{ __('All Prodi') }}</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>
            <div style="width: 150px;">
                <label class="form-label">{{ __('Tahun Akademik') }}</label>
                <select name="angkatan" class="form-control form-control-sm">
                    <option value="">{{ __('Semua Tahun') }}</option>
                    @php
                        $angkatanList = \App\Models\Student::select('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan');
                    @endphp
                    @foreach($angkatanList as $akt)
                        <option value="{{ $akt }}" {{ request('angkatan') == $akt ? 'selected' : '' }}>{{ $akt }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-outline-primary btn-sm">{{ __('Filter') }}</button>
                <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <form id="bulk-frozen-form" action="{{ route('mahasiswa.bulk_update_frozen') }}" method="POST">
            @csrf
        </form>
        <div style="display: flex; justify-content: flex-end; padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
            <button type="submit" form="bulk-frozen-form" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">{{ __('Simpan Status Eligible') }}</button>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left; width: 40px;">
                            <input type="checkbox" id="check-all-frozen" onclick="toggleAllFrozen(this)">
                        </th>
                        <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('NIM') }}</th>
                        <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Nama Mahasiswa') }}</th>
                        <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Angkatan') }}</th>
                        <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Email') }}</th>
                        <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Prodi') }}</th>
                        <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Status Eligible') }}</th>
                        <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Action') }}</th>
                    </tr>
                </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb; text-align: center;">
                        <input type="hidden" form="bulk-frozen-form" name="displayed_student_ids[]" value="{{ $student->id }}">
                        <input type="checkbox" form="bulk-frozen-form" class="check-frozen" name="frozen_ids[]" value="{{ $student->id }}" {{ $student->is_frozen ? 'checked' : '' }} onchange="updateCheckAllState()">
                    </td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ $student->nim }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ $student->nama_student }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ $student->angkatan }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ $student->user->email ?? '-' }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ optional($student->prodi)->nama_prodi ?? '-' }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                        @if($student->is_frozen)
                            <span style="background: #fee2e2; color: #991b1b; padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.75rem;">{{ __('Belum Eligible') }}</span>
                        @else
                            <span style="background: #dcfce7; color: #166534; padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.75rem;">{{ __('Eligible') }}</span>
                        @endif
                    </td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                        <button type="button" class="btn" style="background: #eef2ff; color: #4f46e5; border: none; padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="openEditModal('{{ route('mahasiswa.update', $student) }}', '{{ $student->nim }}', '{{ $student->nama_student }}', '{{ $student->angkatan }}', '{{ $student->user->email ?? '' }}', '{{ $student->prodi_id }}')">Edit</button>
                        <form action="{{ route('mahasiswa.destroy', $student) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mahasiswa ini? Akun login yang terkait juga akan dihapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding: 1rem; text-align: center; color: #6b7280;">{{ __('Tidak ada data mahasiswa.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div style="padding: 1rem;">
            {{ $students->links() }}
        </div>
    </div>
</div>

<!-- Modal Add Mahasiswa -->
<div id="modal-add" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; max-height: 90vh; overflow-y: auto;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10;">
            <h3 style="margin: 0; font-size: 1.1rem;">{{ __('Add Data Mahasiswa') }}</h3>
            <button onclick="document.getElementById('modal-add').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <form action="{{ route('mahasiswa.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">{{ __('NIM') }} <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="nim" class="form-control" required value="{{ old('nim') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Nama Lengkap Mahasiswa') }} <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="nama_student" class="form-control" required value="{{ old('nama_student') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Angkatan (Tahun)') }} <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="angkatan" class="form-control" required value="{{ old('angkatan') ?? date('Y') }}" min="2000" max="{{ date('Y') + 1 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Email') }} <span style="color:#ef4444;">*</span></label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Program Studi') }} <span style="color:#ef4444;">*</span></label>
                    <select name="prodi_id" class="form-control" required>
                        <option value="">{{ __('Pilih Prodi') }}</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" {{ old('prodi_id') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Password') }} <span style="font-size:0.75rem;color:#6b7280;font-weight:normal;">{{ __('(Kosongkan untuk default: LmsHorizon$01)') }}</span></label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="add-mhs-password" class="form-control" autocomplete="new-password" style="padding-right:2.8rem;" oninput="checkMhsPassword(this.value)">
                        <button type="button" onclick="toggleMhsPassword()" title="Tampilkan/Sembunyikan" style="position:absolute;right:0.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#6b7280;font-size:1.1rem;line-height:1;padding:0;" id="add-mhs-eye">👁️</button>
                    </div>
                    <div id="add-mhs-rules" style="margin-top:0.5rem;display:none;">
                        <div style="font-size:0.75rem;color:#6b7280;margin-bottom:0.25rem;">{{ __('Persyaratan password:') }}</div>
                        <ul style="margin:0;padding-left:1.2rem;list-style:none;">
                            <li id="mhs-rule-min"    style="font-size:0.78rem;color:#9ca3af;transition:color .2s;">✗ {{ __('Minimal 8 karakter') }}</li>
                            <li id="mhs-rule-upper"  style="font-size:0.78rem;color:#9ca3af;transition:color .2s;">✗ {{ __('Mengandung huruf BESAR') }}</li>
                            <li id="mhs-rule-lower"  style="font-size:0.78rem;color:#9ca3af;transition:color .2s;">✗ {{ __('Mengandung huruf kecil') }}</li>
                            <li id="mhs-rule-number" style="font-size:0.78rem;color:#9ca3af;transition:color .2s;">✗ {{ __('Mengandung angka') }}</li>
                            <li id="mhs-rule-symbol" style="font-size:0.78rem;color:#9ca3af;transition:color .2s;">✗ {{ __('Mengandung simbol (!@#$...)') }}</li>
                        </ul>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn" style="background: #f3f4f6; color: #374151;" onclick="document.getElementById('modal-add').style.display = 'none'">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Data') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Mahasiswa -->
<div id="modal-edit" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; max-height: 90vh; overflow-y: auto;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10;">
            <h3 style="margin: 0; font-size: 1.1rem;">{{ __('Edit Data Mahasiswa') }}</h3>
            <button onclick="document.getElementById('modal-edit').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">{{ __('NIM') }} </label>
                    <input type="text" name="nim" id="edit-nim" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Nama Lengkap Mahasiswa') }}</label>
                    <input type="text" name="nama_student" id="edit-nama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Angkatan (Tahun)') }}</label>
                    <input type="number" name="angkatan" id="edit-angkatan" class="form-control" required min="2000" max="{{ date('Y') + 1 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Email') }} </label>
                    <input type="email" name="email" id="edit-email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Program Studi') }}</label>
                    <select name="prodi_id" id="edit-prodi" class="form-control" required>
                        <option value="">{{ __('Pilih Prodi') }}</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn" style="background: #f3f4f6; color: #374151;" onclick="document.getElementById('modal-edit').style.display = 'none'">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update Data') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import CSV -->
<div id="modal-import" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem;">{{ __('Import Mahasiswa via CSV') }}</h3>
            <button onclick="document.getElementById('modal-import').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <div style="background: #eff6ff; color: #1e40af; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                <strong>{{ __('Format CSV Required:') }}</strong><br>
                Column 1: <code>email</code><br>
                Column 2: <code>kode_prodi</code><br>
                Column 3: <code>nim</code><br>
                Column 4: <code>nama_student</code><br>
                Column 5: <code>angkatan</code><br>
                <em>{{ __('Default password will be applied to all imported users:') }} <strong>LmsHorizon$01</strong></em>
            </div>
            <form action="{{ route('mahasiswa.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">{{ __('Upload CSV File') }}</label>
                    <input type="file" name="file" accept=".csv" class="form-control" required style="padding: 0.5rem;">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn" style="background: #f3f4f6; color: #374151;" onclick="document.getElementById('modal-import').style.display = 'none'">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('Import Data') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(actionUrl, nim, nama, angkatan, email, prodi_id) {
        document.getElementById('edit-form').action = actionUrl;
        document.getElementById('edit-nim').value = nim;
        document.getElementById('edit-nama').value = nama;
        document.getElementById('edit-angkatan').value = angkatan;
        document.getElementById('edit-email').value = email;
        
        const prodiSelect = document.getElementById('edit-prodi');
        prodiSelect.value = prodi_id;
        if (window.jQuery && typeof jQuery.fn.selectpicker === 'function') {
            $(prodiSelect).selectpicker('refresh');
        }
        
        document.getElementById('modal-edit').style.display = 'flex';
    }

    {{-- Reopen the Add modal automatically if validation failed --}}
    @if($errors->any() && !old('_method'))
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('modal-add').style.display = 'flex';
    });
    @endif

    /* --- Password toggle & validation for Add Mahasiswa modal --- */
    function toggleMhsPassword() {
        const inp = document.getElementById('add-mhs-password');
        const btn = document.getElementById('add-mhs-eye');
        if (inp.type === 'password') {
            inp.type = 'text';
            btn.textContent = '🙈';
        } else {
            inp.type = 'password';
            btn.textContent = '👁️';
        }
    }

    function setMhsRule(id, ok) {
        const el = document.getElementById(id);
        if (!el) return;
        if (ok) {
            el.style.color = '#16a34a';
            el.textContent = '✓ ' + el.textContent.slice(2);
        } else {
            el.style.color = '#9ca3af';
            el.textContent = '✗ ' + el.textContent.slice(2);
        }
    }

    function checkMhsPassword(val) {
        const rules = document.getElementById('add-mhs-rules');
        rules.style.display = val.length > 0 ? 'block' : 'none';
        setMhsRule('mhs-rule-min',    val.length >= 8);
        setMhsRule('mhs-rule-upper',  /[A-Z]/.test(val));
        setMhsRule('mhs-rule-lower',  /[a-z]/.test(val));
        setMhsRule('mhs-rule-number', /[0-9]/.test(val));
        setMhsRule('mhs-rule-symbol', /[^A-Za-z0-9]/.test(val));
    }

    function toggleAllFrozen(checkbox) {
        const checkboxes = document.querySelectorAll('.check-frozen');
        checkboxes.forEach(cb => cb.checked = checkbox.checked);
    }

    function updateCheckAllState() {
        const checkboxes = document.querySelectorAll('.check-frozen');
        const checkAll = document.getElementById('check-all-frozen');
        if (checkboxes.length === 0) return;
        let allChecked = true;
        checkboxes.forEach(cb => {
            if (!cb.checked) allChecked = false;
        });
        checkAll.checked = allChecked;
    }

    // Initialize check all state on load
    document.addEventListener('DOMContentLoaded', function() {
        updateCheckAllState();
    });
</script>
@endsection
