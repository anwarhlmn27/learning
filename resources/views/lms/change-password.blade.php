@extends('layouts.lms')

@section('header_title', __('Change Password'))

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        {{ __('Change Password') }}
    </div>
    <div class="card-body">

        <form action="{{ route('lms.password.update_auth') }}" method="POST">
            @csrf
            
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="current_password" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Current Password <span style="color:red;">*</span></label>
                <div style="position: relative;">
                    <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required style="padding-right: 40px;">
                    <button type="button" onclick="togglePassword('current_password', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6b7280;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('current_password')
                    <div style="color: #dc2626; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="password" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">New Password <span style="color:red;">*</span></label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required style="padding-right: 40px;">
                    <button type="button" onclick="togglePassword('password', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6b7280;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                
                <div id="password-criteria" style="margin-top: 0.75rem; font-size: 0.85rem; background: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0;">
                    <div style="margin-bottom: 0.25rem; font-weight: bold; color: #475569;">Kriteria Password:</div>
                    <div id="crit-length" style="color: #dc2626; margin-bottom: 0.2rem;"><i class="fas fa-times" style="width: 15px;"></i> Minimal 8 karakter</div>
                    <div id="crit-upper" style="color: #dc2626; margin-bottom: 0.2rem;"><i class="fas fa-times" style="width: 15px;"></i> Minimal 1 huruf kapital (A-Z)</div>
                    <div id="crit-lower" style="color: #dc2626; margin-bottom: 0.2rem;"><i class="fas fa-times" style="width: 15px;"></i> Minimal 1 huruf kecil (a-z)</div>
                    <div id="crit-number" style="color: #dc2626; margin-bottom: 0.2rem;"><i class="fas fa-times" style="width: 15px;"></i> Minimal 1 angka (0-9)</div>
                    <div id="crit-special" style="color: #dc2626;"><i class="fas fa-times" style="width: 15px;"></i> Minimal 1 karakter spesial (@$!%*?&)</div>
                </div>

                @error('password')
                    <div style="color: #dc2626; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="password_confirmation" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Confirm New Password <span style="color:red;">*</span></label>
                <div style="position: relative;">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required style="padding-right: 40px;">
                    <button type="button" onclick="togglePassword('password_confirmation', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6b7280;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">Update Password</button>
        </form>
    </div>
</div>

<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    document.getElementById('password').addEventListener('input', function() {
        const val = this.value;
        
        const checkCriteria = (id, regex) => {
            const el = document.getElementById(id);
            const icon = el.querySelector('i');
            if (regex.test(val)) {
                el.style.color = '#16a34a';
                icon.classList.remove('fa-times');
                icon.classList.add('fa-check');
            } else {
                el.style.color = '#dc2626';
                icon.classList.remove('fa-check');
                icon.classList.add('fa-times');
            }
        };
        
        checkCriteria('crit-length', /.{8,}/);
        checkCriteria('crit-upper', /[A-Z]/);
        checkCriteria('crit-lower', /[a-z]/);
        checkCriteria('crit-number', /[0-9]/);
        checkCriteria('crit-special', /[^A-Za-z0-9]/);
    });
</script>
@endsection
