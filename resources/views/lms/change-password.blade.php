@extends('layouts.lms')

@section('header_title', __('Change Password'))

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        {{ __('Change Password') }}
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success" style="background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('lms.password.update_auth') }}" method="POST">
            @csrf
            
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="current_password" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                @error('current_password')
                    <div style="color: #dc2626; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="password" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">New Password</label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Min 8 chars, uppercase, lowercase, number, special character.</div>
                @error('password')
                    <div style="color: #dc2626; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="password_confirmation" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">Update Password</button>
        </form>
    </div>
</div>
@endsection
