@extends('layouts.lms')

@section('header_title', __('Data Dosen'))

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2 style="margin: 0; font-size: 1.5rem; color: #111827;">{{ __('Data Dosen') }}</h2>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('dosen.template') }}" class="btn btn-outline btn-sm" style="background: #f3f4f6; color: #374151; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
            <i>📥</i> {{ __('Download Template') }}
        </a>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('modal-add').style.display = 'flex'">
            <i>➕</i> {{ __('Add Dosen') }}
        </button>
        <button class="btn btn-success btn-sm" onclick="document.getElementById('modal-import').style.display = 'flex'">
            <i>📄</i> {{ __('Import CSV') }}
        </button>
    </div>
</div>

<!-- Filter Form -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form action="{{ route('dosen.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label">{{ __('Search') }}</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="{{ __('Name, NIDN, or Email') }}" value="{{ request('search') }}">
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
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-outline-primary btn-sm">{{ __('Filter') }}</button>
                <a href="{{ route('dosen.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('NIDN') }}</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Nama Dosen') }}</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Gelar') }}</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Email') }}</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Prodi') }}</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dosens as $dosen)
                <tr>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ $dosen->nidn }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ $dosen->nama_dosen }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ $dosen->gelar ?? '-' }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ $dosen->user->email ?? '-' }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ optional($dosen->prodi)->nama_prodi ?? '-' }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                        <button class="btn" style="background: #eef2ff; color: #4f46e5; border: none; padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="openEditModal('{{ route('dosen.update', $dosen) }}', '{{ $dosen->nidn }}', '{{ $dosen->nama_dosen }}', '{{ $dosen->gelar }}', '{{ $dosen->user->email ?? '' }}', '{{ $dosen->prodi_id }}')">{{ __('Edit') }}</button>
                        <form action="{{ route('dosen.destroy', $dosen) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dosen ini? Akun login yang terkait juga akan dihapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 1rem; text-align: center; color: #6b7280;">{{ __('Tidak ada data dosen.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div style="padding: 1rem;">
            {{ $dosens->links() }}
        </div>
    </div>
</div>

<!-- Modal Add Dosen -->
<div id="modal-add" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; max-height: 90vh; overflow-y: auto;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10;">
            <h3 style="margin: 0; font-size: 1.1rem;">{{ __('Add Data Dosen') }}</h3>
            <button onclick="document.getElementById('modal-add').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <form action="{{ route('dosen.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">{{ __('NIDN') }} <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="nidn" class="form-control" required value="{{ old('nidn') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Nama Lengkap Dosen') }} <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="nama_dosen" class="form-control" required value="{{ old('nama_dosen') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Gelar') }} <span style="font-size:0.75rem;color:#6b7280;font-weight:normal;">{{ __('(Opsional)') }}</span></label>
                    <input type="text" name="gelar" class="form-control" value="{{ old('gelar') }}">
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
                        <input type="password" name="password" id="add-dosen-password" class="form-control" autocomplete="new-password" style="padding-right:2.8rem;" oninput="checkDosenPassword(this.value)">
                        <span onclick="toggleDosenPassword()" style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);cursor:pointer;color:#6b7280;font-size:1rem;z-index:10;" title="{{ __('Lihat Password') }}" id="add-dosen-eye">
                            <i class="fa fa-eye-slash" style="font-size:1.1rem;"></i>
                        </span>
                    </div>
                    <div id="add-dosen-rules" style="margin-top:0.5rem;display:none;">
                        <div style="font-size:0.75rem;color:#6b7280;margin-bottom:0.25rem;">{{ __('Persyaratan password:') }}</div>
                        <ul style="margin:0;padding-left:1.2rem;list-style:none;">
                            <li id="dosen-rule-min"    style="font-size:0.78rem;color:#9ca3af;transition:color .2s;">✗ {{ __('Minimal 8 karakter') }}</li>
                            <li id="dosen-rule-upper"  style="font-size:0.78rem;color:#9ca3af;transition:color .2s;">✗ {{ __('Mengandung huruf BESAR') }}</li>
                            <li id="dosen-rule-lower"  style="font-size:0.78rem;color:#9ca3af;transition:color .2s;">✗ {{ __('Mengandung huruf kecil') }}</li>
                            <li id="dosen-rule-number" style="font-size:0.78rem;color:#9ca3af;transition:color .2s;">✗ {{ __('Mengandung angka') }}</li>
                            <li id="dosen-rule-symbol" style="font-size:0.78rem;color:#9ca3af;transition:color .2s;">✗ {{ __('Mengandung simbol (!@#$...)') }}</li>
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

<!-- Modal Edit Dosen -->
<div id="modal-edit" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; max-height: 90vh; overflow-y: auto;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10;">
            <h3 style="margin: 0; font-size: 1.1rem;">{{ __('Edit Data Dosen') }}</h3>
            <button onclick="document.getElementById('modal-edit').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">{{ __('NIDN') }} </label>
                    <input type="text" name="nidn" id="edit-nidn" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Nama Lengkap Dosen') }}</label>
                    <input type="text" name="nama_dosen" id="edit-nama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Gelar') }} {{ __('(Opsional)') }}</label>
                    <input type="text" name="gelar" id="edit-gelar" class="form-control">
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
            <h3 style="margin: 0; font-size: 1.1rem;">{{ __('Import Dosen via CSV') }}</h3>
            <button onclick="document.getElementById('modal-import').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <div style="background: #eff6ff; color: #1e40af; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                <strong>{{ __('Format CSV Required:') }}</strong><br>
                Column 1: <code>email</code><br>
                Column 2: <code>kode_prodi</code><br>
                Column 3: <code>nidn</code><br>
                Column 4: <code>nama_dosen</code><br>
                Column 5: <code>gelar</code><br>
                <em>{{ __('Default password will be applied to all imported users:') }} <strong>LmsHorizon$01</strong></em>
            </div>
            <form action="{{ route('dosen.import') }}" method="POST" enctype="multipart/form-data">
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
    function openEditModal(actionUrl, nidn, nama, gelar, email, prodi_id) {
        document.getElementById('edit-form').action = actionUrl;
        document.getElementById('edit-nidn').value = nidn;
        document.getElementById('edit-nama').value = nama;
        document.getElementById('edit-gelar').value = gelar;
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

    /* --- Password toggle & validation for Add Dosen modal --- */
    function toggleDosenPassword() {
        const inp = document.getElementById('add-dosen-password');
        const triggerEl = document.getElementById('add-dosen-eye');
        const icon = triggerEl.querySelector('i');
        if (inp.type === 'password') {
            inp.type = 'text';
            if (icon) icon.className = 'fa fa-eye';
            triggerEl.title = '{{ __('Sembunyikan Password') }}';
        } else {
            inp.type = 'password';
            if (icon) icon.className = 'fa fa-eye-slash';
            triggerEl.title = '{{ __('Lihat Password') }}';
        }
    }

    function setRule(id, ok) {
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

    function checkDosenPassword(val) {
        const rules = document.getElementById('add-dosen-rules');
        rules.style.display = val.length > 0 ? 'block' : 'none';
        setRule('dosen-rule-min',    val.length >= 8);
        setRule('dosen-rule-upper',  /[A-Z]/.test(val));
        setRule('dosen-rule-lower',  /[a-z]/.test(val));
        setRule('dosen-rule-number', /[0-9]/.test(val));
        setRule('dosen-rule-symbol', /[^A-Za-z0-9]/.test(val));
    }
</script>
@endsection
