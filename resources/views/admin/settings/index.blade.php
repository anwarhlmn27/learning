@extends('layouts.admin')

@section('title', __('Settings'))

@section('styles')
<link rel="stylesheet" href="{{ asset('css/settings.css') }}">
@endsection

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>{{ __('Settings') }}</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Settings') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0);">{{ __('Settings') }}</a></li>
        </ol>
    </div>
</div>

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

                    <!-- <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 2rem 0;">

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
                        <div class="form-group">
                            <label class="form-label">{{ __('Content Font Color') }}</label>
                            <div class="color-picker-wrapper">
                                <input type="color" name="content_font_color" value="{{ $user->content_font_color ?? '#111827' }}">
                            </div>
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 2rem 0;">

                    <h3 style="margin-top: 0; margin-bottom: 1rem; font-size: 1rem; color: var(--text-muted);">{{ __('Typography') }}</h3>
                    <div class="form-group" style="max-width: 400px;">
                        <label class="form-label">{{ __('Font Family') }}</label>
                        <select name="font_family" class="form-control">
                            <option value="'Inter', sans-serif" {{ ($user->font_family == "'Inter', sans-serif") ? 'selected' : '' }}>Inter (Default)</option>
                            <option value="'Roboto', sans-serif" {{ ($user->font_family == "'Roboto', sans-serif") ? 'selected' : '' }}>Roboto</option>
                            <option value="'Outfit', sans-serif" {{ ($user->font_family == "'Outfit', sans-serif") ? 'selected' : '' }}>Outfit</option>
                            <option value="system-ui, sans-serif" {{ ($user->font_family == "system-ui, sans-serif") ? 'selected' : '' }}>System UI</option>
                            <option value="Georgia, serif" {{ ($user->font_family == "Georgia, serif") ? 'selected' : '' }}>Georgia (Serif)</option>
                            <option value="'Courier New', monospace" {{ ($user->font_family == "'Courier New', monospace") ? 'selected' : '' }}>Courier New (Monospace)</option>
                        </select>
                    </div> -->

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
