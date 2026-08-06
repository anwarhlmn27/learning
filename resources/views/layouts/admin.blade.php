<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset(get_setting('favicon') ? 'img/favicon/' . get_setting('favicon') : 'images/logo/icon_hui.png') }}">
    <title>@yield('title', 'Admin Dashboard') - OBE System</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link class="main-css" rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <style>
        /* Toast Notification System */
        #lms-toast-container {
            position: fixed; top: 1.25rem; right: 1.25rem; z-index: 9999;
            display: flex; flex-direction: column; gap: 0.6rem; pointer-events: none;
        }
        .lms-toast {
            pointer-events: all; display: flex; align-items: center; gap: 0.9rem;
            border-radius: 10px; padding: 0.85rem 1.1rem; min-width: 280px; max-width: 370px;
            position: relative; overflow: hidden; box-shadow: 0 6px 24px rgba(0,0,0,0.18);
            transform: translateX(120%); opacity: 0;
            transition: transform 0.35s cubic-bezier(.22,1,.36,1), opacity 0.28s ease;
        }
        .lms-toast.show { transform: translateX(0); opacity: 1; }
        .lms-toast.hide { transform: translateX(120%); opacity: 0; }
        .lms-toast-icon {
            flex-shrink: 0; width: 2rem; height: 2rem; border-radius: 50%;
            background: rgba(255,255,255,0.25); display: flex; align-items: center;
            justify-content: center; font-size: 1rem; font-weight: 900; color: #fff;
        }
        .lms-toast-body   { flex: 1; min-width: 0; }
        .lms-toast-title  { font-size: 0.88rem; font-weight: 700; color: #fff; margin-bottom: 0.1rem; line-height: 1.3; }
        .lms-toast-message { font-size: 0.82rem; font-weight: 400; color: rgba(255,255,255,0.88); line-height: 1.4; word-break: break-word; }
        .lms-toast-close {
            flex-shrink: 0; background: rgba(255,255,255,0.2); border: none; cursor: pointer; color: #fff;
            font-size: 1rem; width: 1.5rem; height: 1.5rem; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; padding: 0;
        }
        .lms-toast-close:hover { background: rgba(255,255,255,0.35); }
        .lms-toast-progress {
            position: absolute; bottom: 0; left: 0; height: 3px; width: 100%; transform-origin: left;
            background: rgba(255,255,255,0.4); animation: toast-progress 2s linear forwards;
        }
        @keyframes toast-progress { from { transform: scaleX(1); } to { transform: scaleX(0); } }
        .lms-toast.toast-success { background: #5aab6e; }
        .lms-toast.toast-error   { background: #e05252; }
        .lms-toast.toast-warning { background: #f59e0b; }
        .lms-toast.toast-info    { background: #3b82f6; }
        
        /* Quiz Mode */
        .quiz-mode-active .dlabnav, .quiz-mode-active .header, .quiz-mode-active .nav-header { display: none !important; }
        .quiz-mode-active .content-body { margin-left: 0 !important; padding-top: 0 !important; }

        /* Pagination & Nav SVG safeguards */
        nav svg, .pagination svg, svg.w-5, svg.h-5 {
            width: 1.25rem !important;
            height: 1.25rem !important;
            max-width: 1.25rem !important;
            max-height: 1.25rem !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }
    </style>
    @yield('styles')
    @stack('styles')
</head>
<body data-typography="poppins" data-theme-version="light" data-layout="vertical" data-nav-headerbg="color_1" data-headerbg="color_1" data-sidebar-style="full" data-sidebarbg="color_1" data-sidebar-position="fixed" data-header-position="fixed" data-container="wide" direction="ltr" class="{{ Auth::check() && Auth::user()->hasRole(['rektor', 'dekan']) ? 'role-view-only' : '' }}">

    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>

    <div id="main-wrapper">

        <!-- Nav header start -->
        <div class="nav-header">
            @php
                $dashboardLogo = \App\Models\Setting::where('key', 'dashboard_logo')->value('value');
            @endphp
            <a href="{{ route('dashboard') }}" class="brand-logo" style="display: flex; align-items: center; justify-content: center;">
                @if($dashboardLogo)
                    <img src="{{ asset('img/logo_dashboard/' . $dashboardLogo) }}" alt="Campus Logo" style="max-width: 100%; max-height: 50px; object-fit: contain;">
                @else
                    <h2 class="brand-title" style="color: var(--primary); font-weight: 700; margin: 0; font-size: 1.5rem;">Horizon</h2>
                @endif
            </a>

            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!-- Nav header end -->

        <!-- Header start -->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <h3 style="margin: 0; font-weight: 600;">@yield('header_title', 'Dashboard')</h3>
                            <div class="ms-3">@yield('header_left')</div>
                            @if(Auth::check())
                                @foreach(Auth::user()->roles as $role)
                                    <span class="badge badge-primary light mt-1" style="font-weight: 600; text-transform: uppercase;">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            @endif
                        </div>

                        <ul class="navbar-nav header-right">
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link bell dlab-theme-mode p-0" href="javascript:void(0);">
									<i id="icon-light" class="fas fa-sun"></i>
                                    <i id="icon-dark" class="fas fa-moon"></i>
                                </a>
							</li>
                            
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                    @if(Auth::check() && Auth::user()->avatar)
                                        <img src="{{ asset('img/avatars/' . Auth::user()->avatar) }}" width="20" alt=""/>
                                    @else
                                        <div style="width: 35px; height: 35px; border-radius: 50%; background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                            {{ strtoupper(substr(Auth::user()->name ?? Auth::user()->email, 0, 1)) }}
                                        </div>
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <span class="dropdown-item">
                                        <strong style="color: #333;">{{ Auth::user()->name ?? Auth::user()->email }}</strong>
                                    </span>
                                    <div class="dropdown-divider"></div>
                                    <a href="{{ route('lms.password.change') }}" class="dropdown-item ai-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-key"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path></svg>
                                        <span class="ms-2">{{ __('Change Password') }} </span>
                                    </a>
                                    <a href="{{ route('lms.settings.index') }}" class="dropdown-item ai-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                        <span class="ms-2">{{ __('Settings') }} </span>
                                    </a>
                                    
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item ai-icon text-danger" style="cursor: pointer; border: none; background: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                            <span class="ms-2">{{ __('Logout') }} </span>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Header end -->

        <!-- Sidebar start -->
        <div class="dlabnav">
            <div class="dlabnav-scroll">
                <ul class="metismenu" id="menu">
                    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'mm-active' : '' }}"><i class="la la-home"></i><span class="nav-text">{{ __('Dashboard') }}</span></a></li>
                    
                    @if(Auth::user()->can('view-institusi') || Auth::user()->can('manage-institusi') || Auth::user()->hasRole(['admin', 'rektor', 'dekan', 'kaprodi', 'baak', 'finance']))
                    <li class="nav-label">{{ __('Institution') }}</li>
                    <li><a class="has-arrow" href="javascript:void(0)" aria-expanded="false"><i class="la la-building"></i><span class="nav-text">{{ __('Institution') }}</span></a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('univ.index') }}" class="{{ request()->routeIs('univ.*') ? 'mm-active' : '' }}">{{ __('University') }}</a></li>
                            <li><a href="{{ route('fakultas.index') }}" class="{{ request()->routeIs('fakultas.*') ? 'mm-active' : '' }}">{{ __('Faculty') }}</a></li>
                            <li><a href="{{ route('prodi.index') }}" class="{{ request()->routeIs('prodi.*') ? 'mm-active' : '' }}">{{ __('Study Program') }}</a></li>
                        </ul>
                    </li>
                    @endif

                    @if(Auth::user()->can('manage-obe-curriculum') || Auth::user()->can('manage-obe-plo') || Auth::user()->can('manage-obe-visi') || Auth::user()->hasRole(['admin', 'rektor', 'dekan', 'kaprodi']))
                    <li class="nav-label">{{ __('Academic & OBE') }}</li>
                    <li><a class="has-arrow" href="javascript:void(0)" aria-expanded="false"><i class="la la-graduation-cap"></i><span class="nav-text">{{ __('Academic & OBE') }}</span></a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('visi.index') }}" class="{{ request()->routeIs('visi.*') ? 'mm-active' : '' }}">{{ __('Vision & Mission') }}</a></li>
                            <li><a href="{{ route('gp.index') }}" class="{{ request()->routeIs('gp.*') ? 'mm-active' : '' }}">{{ __('Graduate Profile (GP)') }}</a></li>
                            <li><a href="{{ route('plo.index') }}" class="{{ request()->routeIs('plo.*') ? 'mm-active' : '' }}">{{ __('CPL (PLO)') }}</a></li>
                            <li><a href="{{ route('bahan_kajian.index') }}" class="{{ request()->routeIs('bahan_kajian.*') ? 'mm-active' : '' }}">{{ __('Bahan Kajian (BK)') }}</a></li>
                            <li><a href="{{ route('subjects.index') }}" class="{{ request()->routeIs('subjects.*') ? 'mm-active' : '' }}">{{ __('Courses') }}</a></li>
                            <li><a href="{{ route('kurikulum.index') }}" class="{{ request()->routeIs('kurikulum.*') ? 'mm-active' : '' }}">{{ __('Curriculum') }}</a></li>
                            <li><a href="{{ route('clo.index') }}" class="{{ request()->routeIs('clo.*') ? 'mm-active' : '' }}">{{ __('CPMK (CLO)') }}</a></li>
                            <li><a href="{{ route('admin.rps.index') }}" class="{{ request()->routeIs('admin.rps.*') ? 'mm-active' : '' }}">{{ __('RPS') }}</a></li>
                            <li><a href="{{ route('analytics.index') }}" class="{{ request()->routeIs('analytics.*') ? 'mm-active' : '' }}">{{ __('OBE Analytics') }}</a></li>
                        </ul>
                    </li>
                    @endif

                    <li class="nav-label">{{ __('Settings') }}</li>
                    <li><a class="has-arrow" href="javascript:void(0)" aria-expanded="false"><i class="la la-cog"></i><span class="nav-text">{{ __('Settings') }}</span></a>
                        <ul aria-expanded="false">
                            @can('manage-users')
                                <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'mm-active' : '' }}">{{ __('User Management') }}</a></li>
                            @endcan
                            @can('manage-rbac')
                                <li><a href="{{ route('rbac.index') }}" class="{{ request()->routeIs('rbac.*') ? 'mm-active' : '' }}">{{ __('Hak Akses (RBAC)') }}</a></li>
                            @endcan
                            <!-- <li><a href="{{ route('assessment_types.index') }}" class="{{ request()->routeIs('assessment_types.*') ? 'mm-active' : '' }}">{{ __('Assessment Types') }}</a></li> -->
                            <li><a href="{{ route('logs.index') }}" class="{{ request()->routeIs('logs.*') ? 'mm-active' : '' }}">{{ __('System Logs') }}</a></li>
                            <li><a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'mm-active' : '' }}">{{ __('Settings') }}</a></li>
                        </ul>
                    </li>

                    <li class="nav-label">{{ __('Return') }}</li>
                    <li><a href="{{ route('dashboard') }}"><i class="la la-arrow-left"></i><span class="nav-text">{{ __('Back to LMS') }}</span></a></li>
                </ul>
            </div>
        </div>
        <!-- Sidebar end -->

        <!-- Content body start -->
        <div class="content-body">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
        <!-- Content body end -->

    </div>

    <!-- Theme Switcher / Demo Panel -->
    <div class="dlab-demo-panel">
        <div class="bg-close"></div>
        <div class="dlab-demo-trigger fas fa-cog" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: var(--primary); color: #fff; border-radius: 50% 0 0 50%; right: 0; top: 50%; position: fixed; z-index: 999; cursor: pointer; box-shadow: -2px 0 5px rgba(223, 215, 215, 0.1);">
        </div>
        <div class="dlab-demo-inner">
            <div class="dlab-demo-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid #eee;">
                <h4 style="margin: 0; font-weight: 600;">Pick your style</h4>
                <div>
                    <button class="btn btn-sm btn-primary" onclick="deleteAllCookie()" style="margin-right: 10px; font-size: 12px; padding: 5px 10px; border-radius: 6px;">Delete All Cookie</button>
                    <a href="javascript:void(0);" class="dlab-demo-close fas fa-times" style="font-size: 1.2rem; color: #333; text-decoration: none;"></a>
                </div>
            </div>
            
            <div class="dlab-demo-content" style="padding: 1rem; overflow-y: auto; height: calc(100vh - 80px);">
                <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 20px;">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#demo-theme">Theme</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#demo-header">Header</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#demo-content">Content</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="demo-theme">
                        <div class="row">
                            <div class="col-sm-12 mb-3">
                                <h6 style="color: #888; font-size: 13px; font-weight: 500;">Background</h6>
                                <select class="form-control default-select" id="theme_version">
                                    <option value="light">Light</option>
                                    <option value="dark">Dark</option>
                                </select>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <h6 style="color: #888; font-size: 13px; font-weight: 500;">Primary Color</h6>
                                <div class="color-options" data-theme-attr="data-primary">
                                    <span data-color="color_1" style="background-color: #ffffff; border: 1px solid #ddd;"></span>
                                    <span data-color="color_2" style="background-color: #143b64;"></span>
                                    <span data-color="color_3" style="background-color: #6a73fa;"></span>
                                    <span data-color="color_4" style="background-color: #4527a0;"></span>
                                    <span data-color="color_5" style="background-color: #c62828;"></span>
                                    <span data-color="color_6" style="background-color: #283593;"></span>
                                    <span data-color="color_7" style="background-color: #1e78ff;"></span>
                                    <span data-color="color_8" style="background-color: #3695eb;"></span>
                                    <span data-color="color_9" style="background-color: #00838f;"></span>
                                    <span data-color="color_10" style="background-color: #ff8f16;"></span>
                                    <span data-color="color_11" style="background-color: #6673fd;"></span>
                                    <span data-color="color_12" style="background-color: #558b2f;"></span>
                                    <span data-color="color_13" style="background-color: #2a2a2a;"></span>
                                    <span data-color="color_14" style="background-color: #1367c8;"></span>
                                    <span data-color="color_15" style="background-color: #ed0b4c;"></span>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 mb-3">
                                <h6 style="color: #888; font-size: 13px; font-weight: 500;">Navigation Header</h6>
                                <div class="color-options" data-theme-attr="data-nav-headerbg">
                                    <span data-color="color_1" style="background-color: #ffffff; border: 1px solid #ddd;"></span>
                                    <span data-color="color_2" style="background-color: #143b64;"></span>
                                    <span data-color="color_3" style="background-color: #6a73fa;"></span>
                                    <span data-color="color_4" style="background-color: #4527a0;"></span>
                                    <span data-color="color_5" style="background-color: #c62828;"></span>
                                    <span data-color="color_6" style="background-color: #283593;"></span>
                                    <span data-color="color_7" style="background-color: #1e78ff;"></span>
                                    <span data-color="color_8" style="background-color: #3695eb;"></span>
                                    <span data-color="color_9" style="background-color: #00838f;"></span>
                                    <span data-color="color_10" style="background-color: #ff8f16;"></span>
                                    <span data-color="color_11" style="background-color: #6673fd;"></span>
                                    <span data-color="color_12" style="background-color: #558b2f;"></span>
                                    <span data-color="color_13" style="background-color: #2a2a2a;"></span>
                                    <span data-color="color_14" style="background-color: #1367c8;"></span>
                                    <span data-color="color_15" style="background-color: #ed0b4c;"></span>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <h6 style="color: #888; font-size: 13px; font-weight: 500;">Header</h6>
                                <div class="color-options" data-theme-attr="data-headerbg">
                                    <span data-color="color_1" style="background-color: #ffffff; border: 1px solid #ddd;"></span>
                                    <span data-color="color_2" style="background-color: #143b64;"></span>
                                    <span data-color="color_3" style="background-color: #6a73fa;"></span>
                                    <span data-color="color_4" style="background-color: #4527a0;"></span>
                                    <span data-color="color_5" style="background-color: #c62828;"></span>
                                    <span data-color="color_6" style="background-color: #283593;"></span>
                                    <span data-color="color_7" style="background-color: #1e78ff;"></span>
                                    <span data-color="color_8" style="background-color: #3695eb;"></span>
                                    <span data-color="color_9" style="background-color: #00838f;"></span>
                                    <span data-color="color_10" style="background-color: #ff8f16;"></span>
                                    <span data-color="color_11" style="background-color: #6673fd;"></span>
                                    <span data-color="color_12" style="background-color: #558b2f;"></span>
                                    <span data-color="color_13" style="background-color: #2a2a2a;"></span>
                                    <span data-color="color_14" style="background-color: #1367c8;"></span>
                                    <span data-color="color_15" style="background-color: #ed0b4c;"></span>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <h6 style="color: #888; font-size: 13px; font-weight: 500;">Sidebar</h6>
                                <div class="color-options" data-theme-attr="data-sidebarbg">
                                    <span data-color="color_1" style="background-color: #ffffff; border: 1px solid #ddd;"></span>
                                    <span data-color="color_2" style="background-color: #143b64;"></span>
                                    <span data-color="color_3" style="background-color: #6a73fa;"></span>
                                    <span data-color="color_4" style="background-color: #4527a0;"></span>
                                    <span data-color="color_5" style="background-color: #c62828;"></span>
                                    <span data-color="color_6" style="background-color: #283593;"></span>
                                    <span data-color="color_7" style="background-color: #1e78ff;"></span>
                                    <span data-color="color_8" style="background-color: #3695eb;"></span>
                                    <span data-color="color_9" style="background-color: #00838f;"></span>
                                    <span data-color="color_10" style="background-color: #ff8f16;"></span>
                                    <span data-color="color_11" style="background-color: #6673fd;"></span>
                                    <span data-color="color_12" style="background-color: #558b2f;"></span>
                                    <span data-color="color_13" style="background-color: #2a2a2a;"></span>
                                    <span data-color="color_14" style="background-color: #1367c8;"></span>
                                    <span data-color="color_15" style="background-color: #ed0b4c;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="demo-header">
                        <p class="text-muted">Header layout settings.</p>
                    </div>
                    
                    <div class="tab-pane fade" id="demo-content">
                        <div class="mb-4">
                            <h6 style="color: #888; font-size: 13px; font-weight: 500; margin-bottom: 10px;">Body Font</h6>
                            <select class="form-control default-select" id="theme_typography">
                                <option value="poppins">Poppins</option>
                                <option value="roboto">Roboto</option>
                                <option value="opensans">Open Sans</option>
                                <option value="helvetica">Helvetica</option>
                            </select>
                        </div>
                        <div>
                            <h6 style="color: #888; font-size: 13px; font-weight: 500; margin-bottom: 10px;">Font Color</h6>
                            <div class="color-options" id="font-color-options">
                                <span data-color="" style="background-color: #6e6e6e; border: 1px solid #ddd;" title="Default Muted" class="active"></span>
                                <span data-color="#ffffff" style="background-color: #ffffff; border: 1px solid #ddd;" title="White"></span>
                                <span data-color="#111827" style="background-color: #111827;" title="Pitch Black"></span>
                                <span data-color="#334155" style="background-color: #334155;" title="Slate"></span>
                                <span data-color="#1e3a8a" style="background-color: #1e3a8a;" title="Navy Blue"></span>
                                <span data-color="#4c1d95" style="background-color: #4c1d95;" title="Deep Purple"></span>
                                <span data-color="#0f766e" style="background-color: #0f766e;" title="Dark Teal"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .color-options span {
            display: inline-block; width: 32px; height: 32px; border-radius: 4px;
            margin-right: 6px; margin-bottom: 6px; cursor: pointer;
            transition: all 0.2s ease-in-out; border: 2px solid transparent;
        }
        .color-options span:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .color-options span.active {
            border: 2px solid #fff !important;
            box-shadow: 0 0 0 3px var(--primary);
            transform: scale(1.1);
        }
        .dlab-demo-inner { width: 450px !important; }
        .dlab-demo-panel { width: 450px !important; right: -450px; }
        .dlab-demo-panel.show { right: 0; }
        @media (max-width: 575px) {
            .dlab-demo-inner { width: 300px !important; }
            .dlab-demo-panel { width: 300px !important; right: -300px; }
        }
    </style>

    <!-- GLOBAL TOAST CONTAINER -->
    <div id="lms-toast-container"></div>

    <!-- Scripts -->
    <script>var assetBaseUrl = "{{ asset('') }}";</script>
    
    <!-- Required vendors -->
    <script src="{{ asset('vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    
    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('js/dlabnav-init.js') }}"></script>

    <script>
        /* Theme Switcher Scripting */
        document.querySelector('.dlab-demo-trigger').addEventListener('click', function() {
            document.querySelector('.dlab-demo-panel').classList.add('show');
        });
        document.querySelector('.bg-close').addEventListener('click', function() {
            document.querySelector('.dlab-demo-panel').classList.remove('show');
        });
        document.querySelector('.dlab-demo-close').addEventListener('click', function() {
            document.querySelector('.dlab-demo-panel').classList.remove('show');
        });

        // Initialize from cookies or defaults
        const getDemoCookie = (name) => {
            const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
            if (match) return match[2];
            return null;
        };

        const setDemoCookie = (name, value) => {
            const d = new Date();
            d.setTime(d.getTime() + (30*24*60*60*1000)); // 30 days
            document.cookie = name + "=" + value + ";expires=" + d.toUTCString() + ";path=/";
        };

        window.deleteAllCookie = function() {
            const cookies = ['version', 'primary', 'navheaderBg', 'headerBg', 'sidebarBg', 'typography', 'fontColor'];
            cookies.forEach(c => document.cookie = c + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/");
            location.reload();
        };

        // Handle Background select
        const themeVersionSelect = document.getElementById('theme_version');
        if (themeVersionSelect) {
            themeVersionSelect.addEventListener('change', function() {
                const val = this.value;
                document.body.setAttribute('data-theme-version', val);
                setDemoCookie('version', val);
            });
            const savedVersion = getDemoCookie('version');
            if (savedVersion) themeVersionSelect.value = savedVersion;
        }

        // Handle Typography select
        const themeTypographySelect = document.getElementById('theme_typography');
        if (themeTypographySelect) {
            themeTypographySelect.addEventListener('change', function() {
                const val = this.value;
                document.body.setAttribute('data-typography', val);
                setDemoCookie('typography', val);
            });
            const savedTypo = getDemoCookie('typography');
            if (savedTypo) themeTypographySelect.value = savedTypo;
        }

        // Handle Color blocks
        document.querySelectorAll('.color-options span').forEach(function(el) {
            el.addEventListener('click', function() {
                const color = this.getAttribute('data-color');
                const attr = this.parentElement.getAttribute('data-theme-attr'); // e.g. data-primary
                
                // Remove active class from siblings
                Array.from(this.parentElement.children).forEach(c => c.classList.remove('active'));
                this.classList.add('active');

                // Set attribute and cookie
                document.body.setAttribute(attr, color);
                
                let cookieName = attr.replace('data-', '');
                if (cookieName === 'nav-headerbg') cookieName = 'navheaderBg';
                if (cookieName === 'headerbg') cookieName = 'headerBg';
                if (cookieName === 'sidebarbg') cookieName = 'sidebarBg';
                
                setDemoCookie(cookieName, color);
            });
        });

        // Apply saved colors and typography on load
        const themeMapping = {
            'primary': 'data-primary',
            'navheaderBg': 'data-nav-headerbg',
            'headerBg': 'data-headerbg',
            'sidebarBg': 'data-sidebarbg'
        };
        
        Object.keys(themeMapping).forEach(cookieName => {
            const attr = themeMapping[cookieName];
            const savedColor = getDemoCookie(cookieName) || 'color_1';
            document.body.setAttribute(attr, savedColor);
            
            // Apply active class to the correct span
            const colorOpt = document.querySelector(`.color-options[data-theme-attr="${attr}"] span[data-color="${savedColor}"]`);
            if (colorOpt) {
                // Remove active from siblings first
                Array.from(colorOpt.parentElement.children).forEach(c => c.classList.remove('active'));
                colorOpt.classList.add('active');
            }
        });
        
        const initialTypo = getDemoCookie('typography') || 'poppins';
        document.body.setAttribute('data-typography', initialTypo);

        // Font Color Logic
        const savedFontColor = getDemoCookie('fontColor') || '';
        function applyFontColor(color) {
            let styleEl = document.getElementById('dynamic-font-style');
            if(!styleEl) {
                styleEl = document.createElement('style');
                styleEl.id = 'dynamic-font-style';
                document.head.appendChild(styleEl);
            }
            if(color) {
                styleEl.innerHTML = `body, p, label, .nav-text, .text-muted, .form-control { color: ${color} !important; } :root { --text-main: ${color}; --text-muted: ${color}; }`;
            } else {
                styleEl.innerHTML = '';
            }
        }
        applyFontColor(savedFontColor);

        const fontOptions = document.querySelectorAll('#font-color-options span');
        if (fontOptions.length > 0) {
            // Setup active state
            fontOptions.forEach(c => c.classList.remove('active'));
            const activeOpt = document.querySelector(`#font-color-options span[data-color="${savedFontColor}"]`);
            if (activeOpt) activeOpt.classList.add('active');

            fontOptions.forEach(function(el) {
                el.addEventListener('click', function() {
                    Array.from(this.parentElement.children).forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    const color = this.getAttribute('data-color');
                    setDemoCookie('fontColor', color);
                    applyFontColor(color);
                });
            });
        }

        /* ====== TOAST SYSTEM ====== */
        const TOAST_META = {
            success: { icon: '&#10003;', label: 'Berhasil' },
            error:   { icon: '&#10005;', label: 'Gagal' },
            warning: { icon: '!',        label: 'Peringatan' },
            info:    { icon: 'i',        label: 'Informasi' },
        };

        function lmsToast(type, message, duration) {
            duration = duration || 2000;
            const meta    = TOAST_META[type] || TOAST_META.info;
            const container = document.getElementById('lms-toast-container');
            const toast   = document.createElement('div');
            toast.className = 'lms-toast toast-' + type;
            toast.innerHTML = `
                <span class="lms-toast-icon">${meta.icon}</span>
                <div class="lms-toast-body">
                    <div class="lms-toast-title">${meta.label}</div>
                    <div class="lms-toast-message">${message}</div>
                </div>
                <button class="lms-toast-close" onclick="lmsDismissToast(this.closest('.lms-toast'))" aria-label="Tutup">&times;</button>
                <div class="lms-toast-progress" style="animation-duration:${duration}ms"></div>
            `;
            container.appendChild(toast);
            requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.add('show')));
            setTimeout(() => lmsDismissToast(toast), duration);
        }

        function lmsDismissToast(toast) {
            if (!toast || toast.classList.contains('hide')) return;
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(() => toast && toast.remove(), 400);
        }

        @if(session('success')) lmsToast('success', @json(session('success'))); @endif
        @if(session('error')) lmsToast('error', @json(session('error'))); @endif
        @if(session('warning')) lmsToast('warning', @json(session('warning'))); @endif
        @if(session('info')) lmsToast('info', @json(session('info'))); @endif
        @if($errors->any())
            @foreach($errors->all() as $msg)
                lmsToast('error', @json($msg), 4000);
            @endforeach
        @endif

        /* Legacy alerts removal & Auto Logout */
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.flash-alert, .alert:not(.alert-success):not(.alert-danger), .alert').forEach(function(el) {
                setTimeout(function() {
                    el.style.transition = 'opacity 0.5s ease, max-height 0.5s ease, margin 0.5s ease, padding 0.5s ease';
                    el.style.opacity = '0';
                    el.style.maxHeight = '0';
                    el.style.padding = '0';
                    el.style.margin = '0';
                    el.style.overflow = 'hidden';
                    setTimeout(function() { el && el.remove(); }, 500);
                }, 2000);
            });

            // Auto Logout after 10 minutes of inactivity
            let idleTimer;
            const idleLimit = 60 * 60 * 1000;
            function resetIdleTimer() {
                clearTimeout(idleTimer);
                idleTimer = setTimeout(logoutUser, idleLimit);
            }
            function logoutUser() {
                const logoutForm = document.createElement('form');
                logoutForm.method = 'POST';
                logoutForm.action = "{{ route('logout') }}";
                const csrfInput = document.createElement('input');
                csrfInput.type  = 'hidden';
                csrfInput.name  = '_token';
                csrfInput.value = "{{ csrf_token() }}";
                logoutForm.appendChild(csrfInput);
                document.body.appendChild(logoutForm);
                alert('Sesi Anda berakhir karena tidak ada aktivitas selama 10 menit.');
                logoutForm.submit();
            }
            ['mousemove','mousedown','keydown','scroll','touchstart','click'].forEach(ev => {
                window.addEventListener(ev, resetIdleTimer);
            });
            resetIdleTimer();
        });

        // Global SweetAlert2 Interceptor
        document.addEventListener('DOMContentLoaded', function() {
            // For Forms
            document.body.addEventListener('submit', function(e) {
                if (e.target && e.target.classList.contains('swal-confirm-form')) {
                    e.preventDefault();
                    const msg = e.target.getAttribute('data-swal-msg') || 'Apakah Anda yakin ingin melanjutkan?';
                    const isDelete = e.target.querySelector('input[name="_method"][value="DELETE"]') !== null;
                    const extraWarning = isDelete ? '<br><br><span style="color: #d33; font-size: 0.9em;"><strong>Perhatian:</strong> Data ini mungkin terhubung dengan data lain (seperti sesi, nilai, atau LMS). Menghapusnya bisa menghilangkan data terkait.</span>' : '';
                    
                    Swal.fire({
                        title: 'Konfirmasi',
                        html: msg + extraWarning,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            e.target.submit();
                        }
                    });
                }
            });

            // For Buttons/Links
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.swal-confirm-btn');
                if (btn) {
                    e.preventDefault();
                    const msg = btn.getAttribute('data-swal-msg') || 'Apakah Anda yakin ingin melanjutkan?';
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: msg,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // If it's a link, navigate
                            if (btn.tagName === 'A' && btn.href) {
                                window.location.href = btn.href;
                            } 
                            // If it's a submit button, submit its form
                            else if (btn.tagName === 'BUTTON' && btn.type === 'submit' && btn.closest('form')) {
                                btn.closest('form').submit();
                            }
                        }
                    });
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
