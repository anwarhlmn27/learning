@extends('layouts.lms')

@section('header_title', __('Settings'))

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            {{ __('Personal Preferences') }}
        </div>
        <div class="card-body">
            <form action="{{ route('lms.settings.personal') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <h3 style="margin-top: 0; margin-bottom: 1rem; font-size: 1rem; color: var(--text-muted);">{{ __('Language') }}</h3>
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">{{ __('System Language') }}</label>
                    <select name="language" class="form-control" style="max-width: 300px;">
                        <option value="id" {{ $user->language == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                        <option value="en" {{ $user->language == 'en' ? 'selected' : '' }}>English</option>
                    </select>
                </div>

                <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 2rem 0;">

                <h3 style="margin-top: 0; margin-bottom: 1rem; font-size: 1rem; color: var(--text-muted);">{{ __('Profile Picture') }}</h3>
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">{{ __('Avatar') }}</label>
                    @if($user->avatar)
                        <div style="margin-bottom: 1rem;">
                            <img src="{{ asset('img/avatars/' . $user->avatar) }}" style="border-radius: 50%; width: 100px; height: 100px; object-fit: cover; border: 1px solid #e5e7eb;">
                        </div>
                    @else
                        <div style="width: 100px; height: 100px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 600; margin-bottom: 1rem;">
                            {{ strtoupper(substr($user->name ?? $user->email, 0, 1)) }}
                        </div>
                    @endif
                    <input type="file" name="avatar" class="form-control" accept="image/*" style="max-width: 400px;">
                    <small style="color: #6b7280; display: block; margin-top: 0.5rem;">JPG, PNG. Max 2MB.</small>
                </div>

                <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 2rem 0;">

                <h3 style="margin-top: 0; margin-bottom: 1rem; font-size: 1rem; color: var(--text-muted);">{{ __('Theme Colors') }}</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">{{ __('Sidebar Color') }}</label>
                        <div style="border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.25rem; display: inline-block; background: #fff;">
                            <input type="color" name="sidebar_color" value="{{ $user->lms_sidebar_color ?? '#1f2937' }}" style="border: none; width: 45px; height: 35px; cursor: pointer; padding: 0;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">{{ __('Sidebar Font Color') }}</label>
                        <div style="border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.25rem; display: inline-block; background: #fff;">
                            <input type="color" name="sidebar_font_color" value="{{ $user->lms_sidebar_font_color ?? '#9ca3af' }}" style="border: none; width: 45px; height: 35px; cursor: pointer; padding: 0;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">{{ __('Navbar Color') }}</label>
                        <div style="border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.25rem; display: inline-block; background: #fff;">
                            <input type="color" name="navbar_color" value="{{ $user->lms_navbar_color ?? '#ffffff' }}" style="border: none; width: 45px; height: 35px; cursor: pointer; padding: 0;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">{{ __('Navbar Font Color') }}</label>
                        <div style="border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.25rem; display: inline-block; background: #fff;">
                            <input type="color" name="navbar_font_color" value="{{ $user->lms_navbar_font_color ?? '#111827' }}" style="border: none; width: 45px; height: 35px; cursor: pointer; padding: 0;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">{{ __('Content Background') }}</label>
                        <div style="border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.25rem; display: inline-block; background: #fff;">
                            <input type="color" name="content_color" value="{{ $user->lms_content_color ?? '#f3f4f6' }}" style="border: none; width: 45px; height: 35px; cursor: pointer; padding: 0;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">{{ __('Content Font Color') }}</label>
                        <div style="border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.25rem; display: inline-block; background: #fff;">
                            <input type="color" name="content_font_color" value="{{ $user->lms_content_font_color ?? '#1f2937' }}" style="border: none; width: 45px; height: 35px; cursor: pointer; padding: 0;">
                        </div>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 2rem 0;">

                <h3 style="margin-top: 0; margin-bottom: 1rem; font-size: 1rem; color: var(--text-muted);">{{ __('Typography') }}</h3>
                <div class="form-group" style="max-width: 400px; margin-bottom: 1.5rem;">
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">{{ __('Font Family') }}</label>
                    <select name="font_family" class="form-control">
                        <option value="'Inter', sans-serif" {{ ($user->lms_font_family == "'Inter', sans-serif") ? 'selected' : '' }}>Inter (Default)</option>
                        <option value="'Roboto', sans-serif" {{ ($user->lms_font_family == "'Roboto', sans-serif") ? 'selected' : '' }}>Roboto</option>
                        <option value="'Outfit', sans-serif" {{ ($user->lms_font_family == "'Outfit', sans-serif") ? 'selected' : '' }}>Outfit</option>
                        <option value="system-ui, sans-serif" {{ ($user->lms_font_family == "system-ui, sans-serif") ? 'selected' : '' }}>System UI</option>
                        <option value="Georgia, serif" {{ ($user->lms_font_family == "Georgia, serif") ? 'selected' : '' }}>Georgia (Serif)</option>
                        <option value="'Courier New', monospace" {{ ($user->lms_font_family == "'Courier New', monospace") ? 'selected' : '' }}>Courier New (Monospace)</option>
                    </select>
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">{{ __('Save Personal Settings') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
