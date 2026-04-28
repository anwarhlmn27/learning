@extends('layouts.admin')

@section('title', 'Change Password')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        Change Password
    </div>
    <div class="card-body">
        <form action="{{ route('password.update_auth') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                @error('current_password')
                    <div style="color: #dc2626; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">New Password</label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Min 8 chars, uppercase, lowercase, number, special character.</div>
                @error('password')
                    <div style="color: #dc2626; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
    </div>
</div>
@endsection
