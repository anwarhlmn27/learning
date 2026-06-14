@extends('layouts.lms')

@section('header_title', __('Settings'))

@section('content')
<div class="row">
    @if(Auth::user()->hasRole('admin'))
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Global Settings (Admin Only)') }}</h4>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="{{ route('settings.global') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-dark font-w600">{{ __('Campus Logo (Dashboard)') }}</label>
                            @php $dashboardLogo = \App\Models\Setting::where('key', 'dashboard_logo')->value('value'); @endphp
                            @if($dashboardLogo)
                                <div class="mb-3 p-3 bg-light rounded d-inline-block">
                                    <img src="{{ asset('img/logo_dashboard/' . $dashboardLogo) }}" style="max-height: 50px;">
                                </div>
                            @endif
                            <input type="file" name="dashboard_logo" class="form-control" accept="image/*">
                            <div class="form-text mt-2 text-muted">JPG, PNG, SVG. Max 2MB. This logo replaces the default system text logo.</div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">{{ __('Save Global Settings') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Personal Preferences') }}</h4>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="{{ route('lms.settings.personal') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label text-dark font-w600">{{ __('System Language') }}</label>
                            <select name="language" class="default-select form-control wide">
                                <option value="id" {{ $user->language == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                <option value="en" {{ $user->language == 'en' ? 'selected' : '' }}>English</option>
                            </select>
                        </div>

                        <div class="mb-3 mt-4">
                            <label class="form-label text-dark font-w600">{{ __('Profile Picture / Avatar') }}</label>
                            <div class="d-flex align-items-center mb-3">
                                @if($user->avatar)
                                    <img src="{{ asset('img/avatars/' . $user->avatar) }}" class="rounded-circle img-fluid" style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #eee;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white bg-primary font-w600" style="width: 80px; height: 80px; font-size: 2rem;">
                                        {{ strtoupper(substr($user->name ?? $user->email, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <input type="file" name="avatar" class="form-control" accept="image/*">
                            <div class="form-text mt-2 text-muted">JPG, PNG. Max 2MB. Recommendation: 1:1 Square aspect ratio.</div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">{{ __('Save Personal Settings') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
