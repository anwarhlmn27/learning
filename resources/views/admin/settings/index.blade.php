@extends('layouts.admin')

@section('title', __('Settings'))

@section('styles')
<style>
    .settings-container {
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 2rem;
    }
    .settings-menu {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    .settings-menu-item {
        display: block;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        color: var(--text-main);
        text-decoration: none;
        font-weight: 500;
        transition: background 0.2s;
        cursor: pointer;
    }
    .settings-menu-item:last-child {
        border-bottom: none;
    }
    .settings-menu-item:hover {
        background: #f9fafb;
    }
    .settings-menu-item.active {
        background: #eff6ff;
        color: var(--primary);
        border-left: 4px solid var(--primary);
    }
    .settings-content-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
        display: none;
    }
    .settings-content-card.active {
        display: block;
    }
    .settings-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        font-size: 1.1rem;
    }
    .settings-body {
        padding: 1.5rem;
    }
    .preview-img {
        max-width: 200px;
        max-height: 100px;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
        object-fit: contain;
    }
    .color-picker-wrapper {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .color-picker-wrapper input[type="color"] {
        height: 40px;
        width: 60px;
        padding: 0;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        cursor: pointer;
    }
    @media (max-width: 768px) {
        .settings-container {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="settings-container">
    <div class="settings-menu">
        <a class="settings-menu-item active" onclick="switchTab('personal')"><i>👤</i> {{ __('Personal Preferences') }}</a>
        @if(Auth::user()->hasRole('admin'))
        <a class="settings-menu-item" onclick="switchTab('global')"><i>🌍</i> {{ __('Global App Settings') }}</a>
        @endif
    </div>

    <div class="settings-content">
        <!-- Personal Settings -->
        <div class="settings-content-card active" id="tab-personal">
            <div class="settings-header">{{ __('Personal Preferences') }}</div>
            <div class="settings-body">
                <form action="{{ route('settings.personal') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <h3 style="margin-top: 0; margin-bottom: 1rem; font-size: 1rem; color: var(--text-muted);">{{ __('Language') }}</h3>
                    <div class="form-group">
                        <label class="form-label">{{ __('System Language') }}</label>
                        <select name="language" class="form-control" style="max-width: 300px;">
                            <option value="id" {{ $user->language == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                            <option value="en" {{ $user->language == 'en' ? 'selected' : '' }}>English</option>
                        </select>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 2rem 0;">

                    <h3 style="margin-top: 0; margin-bottom: 1rem; font-size: 1rem; color: var(--text-muted);">{{ __('Profile Picture') }}</h3>
                    <div class="form-group">
                        <label class="form-label">{{ __('Avatar') }}</label>
                        @if($user->avatar)
                            <div><img src="{{ asset('img/avatars/' . $user->avatar) }}" class="preview-img" style="border-radius: 50%; width: 100px; height: 100px; object-fit: cover;"></div>
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
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">{{ __('Sidebar Color') }}</label>
                            <div class="color-picker-wrapper">
                                <input type="color" name="sidebar_color" value="{{ $user->sidebar_color ?? '#111827' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Sidebar Font Color') }}</label>
                            <div class="color-picker-wrapper">
                                <input type="color" name="sidebar_font_color" value="{{ $user->sidebar_font_color ?? '#9ca3af' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Navbar Color') }}</label>
                            <div class="color-picker-wrapper">
                                <input type="color" name="navbar_color" value="{{ $user->navbar_color ?? '#ffffff' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Navbar Font Color') }}</label>
                            <div class="color-picker-wrapper">
                                <input type="color" name="navbar_font_color" value="{{ $user->navbar_font_color ?? '#111827' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Content Background') }}</label>
                            <div class="color-picker-wrapper">
                                <input type="color" name="content_color" value="{{ $user->content_color ?? '#f9fafb' }}">
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary">{{ __('Save Personal Settings') }}</button>
                    </div>
                </form>
            </div>
        </div>

        @if(Auth::user()->hasRole('admin'))
        <!-- Global Settings -->
        <div class="settings-content-card" id="tab-global">
            <div class="settings-header">{{ __('Global App Settings') }}</div>
            <div class="settings-body">
                <form action="{{ route('settings.global') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">{{ __('Login Page Logo') }}</label>
                        @if(isset($settings['login_logo']))
                            <div><img src="{{ asset('img/logo_login/' . $settings['login_logo']) }}" class="preview-img"></div>
                        @else
                            <div><img src="{{ asset('img/logo_hui.png') }}" class="preview-img"></div>
                        @endif
                        <input type="file" name="login_logo" class="form-control" accept="image/*" style="max-width: 400px;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('Dashboard Sidebar Logo') }}</label>
                        @if(isset($settings['dashboard_logo']))
                            <div><img src="{{ asset('img/logo_dashboard/' . $settings['dashboard_logo']) }}" class="preview-img"></div>
                        @else
                            <div><img src="{{ asset('img/logo_hui.png') }}" class="preview-img"></div>
                        @endif
                        <input type="file" name="dashboard_logo" class="form-control" accept="image/*" style="max-width: 400px;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('Favicon') }}</label>
                        @if(isset($settings['favicon']))
                            <div><img src="{{ asset('img/favicon/' . $settings['favicon']) }}" class="preview-img" style="max-width: 50px;"></div>
                        @else
                            <div><img src="{{ asset('img/icon_hui.png') }}" class="preview-img" style="max-width: 50px;"></div>
                        @endif
                        <input type="file" name="favicon" class="form-control" accept=".png,.ico" style="max-width: 400px;">
                    </div>

                    <div style="margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary">{{ __('Save Global Settings') }}</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function switchTab(tabId) {
        document.querySelectorAll('.settings-menu-item').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.settings-content-card').forEach(el => el.classList.remove('active'));
        
        event.currentTarget.classList.add('active');
        document.getElementById('tab-' + tabId).classList.add('active');
    }
</script>
@endsection
