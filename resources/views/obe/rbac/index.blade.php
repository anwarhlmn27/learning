@extends('layouts.admin')

@section('title', __('Hak Akses (RBAC)'))

@section('content')
<div class="row">
    <div class="col-xl-12 col-xxl-12">
        <div class="welcome-section mb-4">
            <h1 style="font-size: 1.875rem; font-weight: 700; margin: 0 0 0.5rem 0;">{{ __('Pengaturan Hak Akses (RBAC)') }}</h1>
            <p style="color: var(--text-muted); margin: 0;">{{ __('Kelola perizinan akses fitur sistem secara dinamis untuk tiap program studi dan jabatan.') }}</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card" style="border: 1px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            <div class="card-header" style="background: white; border-bottom: 1px solid var(--border-color); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 class="card-title" style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--text-primary);">{{ __('Matriks Hak Akses') }}</h4>
                    <p style="margin: 0.25rem 0 0 0; font-size: 0.8rem; color: var(--text-muted);">{{ __('Catatan: Hak akses untuk role Admin dikunci secara bawaan (Super Admin)') }}</p>
                </div>
            </div>
            <div class="card-body" style="padding: 0; overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; margin: 0; font-size: 0.875rem; min-width: 800px;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 1rem 1.5rem; text-align: left; font-weight: 700; color: var(--text-primary); width: 350px;">{{ __('Fitur / Otoritas Akses') }}</th>
                            @foreach($roles as $role)
                                <th style="padding: 1rem 0.5rem; text-align: center; font-weight: 700; color: var(--text-primary); text-transform: capitalize;">
                                    {{ $role->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissionGroups as $groupName => $groupPermissions)
                            {{-- Group Header Row --}}
                            <tr style="background: #f1f5f9; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
                                <td colspan="{{ count($roles) + 1 }}" style="padding: 0.75rem 1.5rem; font-weight: 700; color: #1e293b; font-size: 0.85rem;">
                                    📁 {{ $groupName }}
                                </td>
                            </tr>
                            
                            {{-- Permissions under Group --}}
                            @foreach($groupPermissions as $permName => $permDesc)
                                @php
                                    $permModel = $permissions->where('name', $permName)->first();
                                @endphp
                                @if($permModel)
                                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                                        <td style="padding: 1rem 1.5rem;">
                                            <strong style="display: block; color: var(--text-primary); font-size: 0.875rem; margin-bottom: 0.15rem;">{{ $permDesc }}</strong>
                                            <code style="font-size: 0.75rem; color: #6366f1; background: #f5f3ff; padding: 0.15rem 0.35rem; border-radius: var(--radius-sm);">{{ $permName }}</code>
                                        </td>
                                        @foreach($roles as $role)
                                            @php
                                                $roleModel = $rolesWithPermissions->where('id', $role->id)->first();
                                                $hasPerm = $roleModel ? $roleModel->permissions->contains('id', $permModel->id) : false;
                                                $isAdmin = $role->name === 'admin';
                                            @endphp
                                            <td style="padding: 1rem 0.5rem; text-align: center; vertical-align: middle;">
                                                <div style="display: inline-flex; align-items: center; justify-content: center;">
                                                    <input 
                                                        type="checkbox" 
                                                        class="permission-toggle" 
                                                        data-role-id="{{ $role->id }}" 
                                                        data-permission-id="{{ $permModel->id }}"
                                                        {{ $isAdmin || $hasPerm ? 'checked' : '' }}
                                                        {{ $isAdmin ? 'disabled' : '' }}
                                                        style="width: 18px; height: 18px; accent-color: var(--primary); cursor: {{ $isAdmin ? 'not-allowed' : 'pointer' }};"
                                                    >
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Toast Notification container --}}
<div id="toast-container" style="position: fixed; top: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.75rem; max-width: 350px;"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggles = document.querySelectorAll('.permission-toggle');

        toggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const roleId = this.dataset.roleId;
                const permissionId = this.dataset.permissionId;
                const isChecked = this.checked;

                // Disable checkbox temporarily during AJAX call to prevent double clicks
                this.disabled = true;

                fetch("{{ route('rbac.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        role_id: roleId,
                        permission_id: permissionId,
                        status: isChecked ? 1 : 0
                    })
                })
                .then(response => response.json())
                .then(data => {
                    this.disabled = false;
                    if (data.success) {
                        showToast(data.message, 'success');
                    } else {
                        // Revert check state on error
                        this.checked = !isChecked;
                        showToast(data.message || 'Gagal memperbarui hak akses.', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.disabled = false;
                    // Revert check state on error
                    this.checked = !isChecked;
                    showToast('Terjadi kesalahan koneksi internet atau server.', 'error');
                });
            });
        });

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.style.padding = '0.85rem 1.25rem';
            toast.style.borderRadius = '8px';
            toast.style.color = 'white';
            toast.style.fontSize = '0.85rem';
            toast.style.fontWeight = '600';
            toast.style.boxShadow = '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)';
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            toast.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            
            if (type === 'success') {
                toast.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                toast.innerHTML = '✨ ' + message;
            } else {
                toast.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
                toast.innerHTML = '❌ ' + message;
            }

            container.appendChild(toast);

            // Trigger animation
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
            }, 50);

            // Auto remove toast
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3500);
        }
    });
</script>
@endsection
