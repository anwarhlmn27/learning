@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>{{ __('User Management') }}</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Settings') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0);">{{ __('User Management') }}</a></li>
        </ol>
    </div>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2 style="margin: 0; font-size: 1.5rem; color: #111827;">User Management</h2>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('users.template') }}" class="btn" style="background: #f3f4f6; color: #374151; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
            <i>📥</i> Download Template
        </a>
        <button class="btn btn-primary" onclick="document.getElementById('modal-add').style.display = 'flex'">
            <i>➕</i> Add User Manual
        </button>
        <button class="btn btn-success" onclick="document.getElementById('modal-import').style.display = 'flex'">
            <i>📄</i> Import CSV
        </button>
    </div>
</div>

@if(session('error'))
    <div class="alert" style="background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; margin-bottom: 1rem;">{{ session('error') }}</div>
@endif

<!-- @if(session('success'))
    <div class="alert" style="background-color: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; margin-bottom: 1rem;">{{ session('success') }}</div>
@endif -->

<!-- Filter Form -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form action="{{ route('users.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label">{{ __('Search') }} </label>
                <input type="text" name="search" class="form-control" placeholder="{{ __('Name or Email') }}" value="{{ request('search') }}">
            </div>
            <div style="width: 150px;">
                <label class="form-label">{{ __('Role') }} </label>
                <select name="role" class="form-control">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div style="width: 150px;">
                <label class="form-label">{{ __('Status') }} </label>
                <select name="status_filter" class="form-control">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status_filter') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ request('status_filter') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('users.index') }}" class="btn" style="background: #f3f4f6; text-decoration: none; color: inherit;">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Name') }}</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Email') }}</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Role') }}</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Status') }}</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                @if(!$user->roles->contains('name', 'admin'))
                <tr>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ $user->name ?? '-' }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ $user->email }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                        @foreach($user->roles as $role)
                            <span style="background: #e0e7ff; color: #3730a3; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;">{{ ucfirst($role->name) }}</span>
                        @endforeach
                    </td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                        <form action="{{ route('users.toggle-status', $user) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" style="background: {{ $user->status == 'active' ? '#dcfce7' : '#fee2e2' }}; color: {{ $user->status == 'active' ? '#166534' : '#991b1b' }}; border: none; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; cursor: pointer; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'" title="Click to toggle status">
                                {{ ucfirst($user->status) }}
                            </button>
                        </form>
                    </td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                        <button class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="openEditModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->roles->first() ? $user->roles->first()->id : '' }}')">Edit</button>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Semua data relasi role juga akan terhapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        
        <div style="padding: 1rem;">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Modal Add User -->
<div id="modal-add" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem;">Add New User</h3>
            <button onclick="document.getElementById('modal-add').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">{{ __('Name') }} <span style="color: red;">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Email') }} <span style="color: red;">*</span></label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Role') }} <span style="color: red;">*</span></label>
                    <select name="role_id" class="form-control" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Password') }} <span style="font-size: 0.75rem; color: #6b7280; font-weight: normal;">{{ __('(Kosongkan untuk default: LmsHorizon$01)') }}</span></label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="add-password" class="form-control" autocomplete="new-password" style="padding-right: 2.8rem;" oninput="checkPasswordRequirement(this.value, 'add-rules', 'add-rule-min', 'add-rule-upper', 'add-rule-lower', 'add-rule-number', 'add-rule-symbol')">
                        <span onclick="togglePasswordVisibility('add-password', this)" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6b7280; font-size: 1rem; z-index: 10;" title="{{ __('Lihat Password') }}">
                            <i class="fa fa-eye-slash" style="font-size: 1.1rem;"></i>
                        </span>
                    </div>
                    <div id="add-rules" style="margin-top: 0.5rem; display: none;">
                        <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">{{ __('Persyaratan kata sandi:') }}</div>
                        <ul style="margin: 0; padding-left: 1.2rem; list-style: none;">
                            <li id="add-rule-min"    style="font-size: 0.78rem; color: #9ca3af; transition: color .2s;">✗ {{ __('Minimal 8 karakter') }}</li>
                            <li id="add-rule-upper"  style="font-size: 0.78rem; color: #9ca3af; transition: color .2s;">✗ {{ __('Mengandung huruf BESAR') }}</li>
                            <li id="add-rule-lower"  style="font-size: 0.78rem; color: #9ca3af; transition: color .2s;">✗ {{ __('Mengandung huruf kecil') }}</li>
                            <li id="add-rule-number" style="font-size: 0.78rem; color: #9ca3af; transition: color .2s;">✗ {{ __('Mengandung angka') }}</li>
                            <li id="add-rule-symbol" style="font-size: 0.78rem; color: #9ca3af; transition: color .2s;">✗ {{ __('Mengandung simbol (!@#$...)') }}</li>
                        </ul>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn" style="background: #f3f4f6; color: #374151;" onclick="document.getElementById('modal-add').style.display = 'none'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import CSV -->
<div id="modal-import" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem;">Import Users via CSV</h3>
            <button onclick="document.getElementById('modal-import').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <div style="background: #eff6ff; color: #1e40af; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                <strong>Format CSV Required:</strong><br>
                Column 1: <code>name</code><br>
                Column 2: <code>email</code><br>
                Column 3: <code>role</code> (admin, rektor, dekan, kaprodi, dosen, baak, finance, kemahasiswaan)<br>
                <em>Default password will be applied to all imported users: <strong>LmsHorizon$01</strong></em>
            </div>
            <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">{{ __('Upload CSV File') }} <span style="color: red;">*</span></label>
                    <input type="file" name="file" accept=".csv" class="form-control" required style="padding: 0.5rem;">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn" style="background: #f3f4f6; color: #374151;" onclick="document.getElementById('modal-import').style.display = 'none'">Cancel</button>
                    <button type="submit" class="btn btn-success">Import Users</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Edit User -->
<div id="modal-edit" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem;">Edit User</h3>
            <button onclick="document.getElementById('modal-edit').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <form id="form-edit" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">{{ __('Name') }} <span style="color: red;">*</span></label>
                    <input type="text" name="name" id="edit-name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Email') }} <span style="color: red;">*</span></label>
                    <input type="email" name="email" id="edit-email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Role') }} <span style="color: red;">*</span></label>
                    <select name="role_id" id="edit-role_id" class="form-control" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Password') }} <span style="font-size: 0.75rem; color: #6b7280; font-weight: normal;">{{ __('(Tinggalkan kosong untuk mempertahankan password saat ini)') }}</span></label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="edit-password" class="form-control" autocomplete="new-password" style="padding-right: 2.8rem;" oninput="checkPasswordRequirement(this.value, 'edit-rules', 'edit-rule-min', 'edit-rule-upper', 'edit-rule-lower', 'edit-rule-number', 'edit-rule-symbol')">
                        <span onclick="togglePasswordVisibility('edit-password', this)" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6b7280; font-size: 1rem; z-index: 10;" title="{{ __('Lihat Password') }}">
                            <i class="fa fa-eye-slash" style="font-size: 1.1rem;"></i>
                        </span>
                    </div>
                    <div id="edit-rules" style="margin-top: 0.5rem; display: none;">
                        <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">{{ __('Persyaratan kata sandi:') }}</div>
                        <ul style="margin: 0; padding-left: 1.2rem; list-style: none;">
                            <li id="edit-rule-min"    style="font-size: 0.78rem; color: #9ca3af; transition: color .2s;">✗ {{ __('Minimal 8 karakter') }}</li>
                            <li id="edit-rule-upper"  style="font-size: 0.78rem; color: #9ca3af; transition: color .2s;">✗ {{ __('Mengandung huruf BESAR') }}</li>
                            <li id="edit-rule-lower"  style="font-size: 0.78rem; color: #9ca3af; transition: color .2s;">✗ {{ __('Mengandung huruf kecil') }}</li>
                            <li id="edit-rule-number" style="font-size: 0.78rem; color: #9ca3af; transition: color .2s;">✗ {{ __('Mengandung angka') }}</li>
                            <li id="edit-rule-symbol" style="font-size: 0.78rem; color: #9ca3af; transition: color .2s;">✗ {{ __('Mengandung simbol (!@#$...)') }}</li>
                        </ul>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn" style="background: #f3f4f6; color: #374151;" onclick="document.getElementById('modal-edit').style.display = 'none'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(id, name, email, roleId) {
        document.getElementById('form-edit').action = "{{ route('users.index') }}/" + id;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-email').value = email;
        
        var selectEl = document.getElementById('edit-role_id');
        if (window.jQuery) {
            window.jQuery(selectEl).val(roleId).trigger('change');
        } else {
            selectEl.value = String(roleId).trim();
            var event = new Event('change', { bubbles: true });
            selectEl.dispatchEvent(event);
        }
        
        document.getElementById('modal-edit').style.display = 'flex';
    }

    function togglePasswordVisibility(inputId, triggerEl) {
        var input = document.getElementById(inputId);
        var icon = triggerEl.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) icon.className = 'fa fa-eye';
            triggerEl.title = '{{ __('Sembunyikan Password') }}';
        } else {
            input.type = 'password';
            if (icon) icon.className = 'fa fa-eye-slash';
            triggerEl.title = '{{ __('Lihat Password') }}';
        }
    }

    function setPasswordRule(id, ok) {
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

    function checkPasswordRequirement(val, rulesId, rMin, rUpper, rLower, rNumber, rSymbol) {
        const rules = document.getElementById(rulesId);
        if (rules) {
            rules.style.display = val.length > 0 ? 'block' : 'none';
        }
        setPasswordRule(rMin,    val.length >= 8);
        setPasswordRule(rUpper,  /[A-Z]/.test(val));
        setPasswordRule(rLower,  /[a-z]/.test(val));
        setPasswordRule(rNumber, /[0-9]/.test(val));
        setPasswordRule(rSymbol, /[^A-Za-z0-9]/.test(val));
    }
</script>
@endsection
