<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - OBE System</title>
    <link rel="icon" type="image/png" href="{{ asset(get_setting('favicon') ? 'img/favicon/' . get_setting('favicon') : 'img/icon_hui.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: {{ Auth::check() && Auth::user()->content_color ? Auth::user()->content_color : '#f9fafb' }};
            --sidebar: {{ Auth::check() && Auth::user()->sidebar_color ? Auth::user()->sidebar_color : '#111827' }};
            --sidebar-text: {{ Auth::check() && Auth::user()->sidebar_font_color ? Auth::user()->sidebar_font_color : '#9ca3af' }};
            --sidebar-hover: #1f2937;
            --card-bg: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --success: #10b981;
            --danger: #ef4444;
            --header-bg: {{ Auth::check() && Auth::user()->navbar_color ? Auth::user()->navbar_color : '#ffffff' }};
            --navbar-text: {{ Auth::check() && Auth::user()->navbar_font_color ? Auth::user()->navbar_font_color : '#111827' }};
        }

        html {
            font-size: 14px; /* Memperkecil base font dari 16px ke 14px */
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            margin: 0;
            display: flex;
            height: 100vh;
            color: var(--text-main);
        }

        .sidebar {
            width: 260px;
            background-color: var(--sidebar);
            color: white;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            flex-shrink: 0;
            overflow-x: hidden;
            position: relative;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 70px;
            box-sizing: border-box;
            border-bottom: 1px solid #1f2937;
            overflow: hidden;
        }

        .sidebar-header img {
            max-width: 180px;
            height: auto;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .sidebar-header img {
            max-width: 40px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 0 0.75rem;
            overflow-y: auto;
        }

        .nav-label {
            padding: 1.5rem 1rem 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--sidebar-text);
            opacity: 0.8;
            letter-spacing: 0.05em;
            white-space: nowrap;
            transition: opacity 0.2s;
        }

        .sidebar.collapsed .nav-label,
        .sidebar.collapsed .nav-item span,
        .sidebar.collapsed .nav-group-btn span,
        .sidebar.collapsed .nav-group-btn .dropdown-icon {
            display: none !important;
        }

        .sidebar.collapsed .nav-item,
        .sidebar.collapsed .nav-group-btn {
            justify-content: center;
            padding: 0.75rem 0;
            width: 100%;
        }

        .sidebar.collapsed .nav-item i,
        .sidebar.collapsed .nav-group-btn i {
            margin-right: 0;
            font-size: 1.5rem;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        /* Hover tooltip effect for collapsed state */
        .sidebar.collapsed .nav-item:hover::after,
        .sidebar.collapsed .nav-group-btn:hover::after {
            content: attr(data-title);
            position: fixed;
            left: 75px;
            padding: 0.5rem 0.75rem;
            background: #1f2937;
            color: white;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            white-space: nowrap;
            z-index: 1000;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            pointer-events: none;
        }

        /* Styling untuk tombol dropdown */
        .nav-group-btn {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 1rem 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--sidebar-text);
            letter-spacing: 0.05em;
            background: none;
            border: none;
            cursor: pointer;
            text-align: left;
            font-family: inherit;
        }

        .nav-group-btn:hover {
            color: #9ca3af;
        }

        .dropdown-icon {
            width: 1.25rem;
            height: 1.25rem;
            transition: transform 0.2s ease;
        }

        .nav-dropdown {
            display: none;
            flex-direction: column;
            margin-top: 0.25rem;
        }

        .nav-dropdown.show {
            display: flex;
        }

        .nav-group-btn.open .dropdown-icon {
            transform: rotate(-180deg);
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            font-weight: 500;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .nav-item i, .nav-group-btn i {
            width: 20px;
            margin-right: 0.75rem;
            flex-shrink: 0;
            text-align: center;
            font-style: normal;
        }

        .nav-item:hover, .nav-item.active {
            background-color: var(--sidebar-hover);
            color: white;
        }

        /* Indentasi khusus menu di dalam dropdown */
        .nav-dropdown .nav-item {
            padding-left: 2rem;
            font-size: 0.95em;
        }

        .sidebar.collapsed .nav-dropdown {
            display: none !important;
        }

        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid #1f2937;
            overflow: hidden;
        }

        .sidebar.collapsed .sidebar-footer {
            padding: 1.5rem 0;
            display: flex;
            justify-content: center;
        }

        .sidebar.collapsed .logout-btn span {
            display: none;
        }

        .sidebar.collapsed .logout-btn::before {
            content: '🚪';
            font-size: 1.25rem;
        }

        .main-content {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: var(--header-bg);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: var(--navbar-text);
            cursor: pointer;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            transition: background 0.2s;
            margin-right: 1rem;
        }

        .toggle-btn:hover {
            background: #f3f4f6;
        }

        .content-padding {
            padding: 2rem;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .sidebar {
                width: 70px;
            }
            .sidebar .nav-label,
            .sidebar .nav-item span,
            .sidebar .nav-group-btn span,
            .sidebar .nav-group-btn .dropdown-icon {
                display: none !important;
            }
            .sidebar .sidebar-header img {
                max-width: 40px;
            }
            .sidebar-footer .logout-btn span {
                display: none;
            }
            .sidebar-footer .logout-btn::before {
                content: '🚪';
                font-size: 1.25rem;
            }
        }

        @media (max-width: 768px) {
            body {
                position: relative;
            }
            .sidebar {
                position: fixed;
                height: 100vh;
                z-index: 50;
                left: -260px;
                width: 260px;
            }
            .sidebar.active {
                left: 0;
                box-shadow: 0 0 20px rgba(0,0,0,0.3);
            }
            .sidebar .nav-label,
            .sidebar .nav-item span,
            .sidebar .nav-group-btn span,
            .sidebar .nav-group-btn .dropdown-icon {
                display: block !important;
            }
            .sidebar .sidebar-header img {
                max-width: 180px;
            }
            .sidebar-footer .logout-btn span {
                display: inline;
            }
            .sidebar-footer .logout-btn::before {
                content: '';
            }
            .content-padding {
                padding: 1rem;
            }
            header {
                padding: 1rem;
            }
            .header-user-email {
                display: none;
            }
            /* Overlay when sidebar is open on mobile */
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 40;
            }
            .sidebar-overlay.active {
                display: block;
            }
        }

        .card {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-body {
            padding: 1.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 0.75rem 1rem;
            background-color: #f9fafb;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .form-control {
            width: 100%;
            padding: 0.625rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc2626;
            background-color: #fef2f2;
        }

        .form-control.is-invalid:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .logout-btn {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            font-family: inherit;
            font-weight: 500;
            font-size: 0.875rem;
        }
    </style>
    @yield('styles')
    @if(Auth::check() && Auth::user()->hasRole(['rektor', 'dekan']))
    <style>
        /* Sembunyikan tombol aksi untuk view-only roles */
        .btn:not([href]), button[type="submit"], form button {
            display: none !important;
        }
        /* Kecuali tombol filter/pencarian */
        form[method="GET"] button {
            display: inline-flex !important;
        }
        /* Tetap tampilkan toggle sidebar */
        .toggle-btn {
            display: inline-flex !important;
        }
    </style>
    @endif
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset(get_setting('dashboard_logo') ? 'img/logo_dashboard/' . get_setting('dashboard_logo') : 'img/logo_hui.png') }}" id="sidebar-logo" alt="Logo">
        </div>
        <nav class="sidebar-nav">
            <div class="nav-label">{{ __('Main') }}</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-title="{{ __('Dashboard') }}">
                <i>🏠</i><span>{{ __('Dashboard') }}</span>
            </a>

            @php 
                $isLembagaActive = request()->routeIs('univ.*') || request()->routeIs('fakultas.*') || request()->routeIs('prodi.*'); 
            @endphp
            <button class="nav-group-btn {{ $isLembagaActive ? 'open' : '' }}" onclick="toggleDropdown('dropdown-lembaga')" data-title="{{ __('Institution') }}">
                <div style="display: flex; align-items: center;">
                    <i>🏢</i><span>{{ __('Institution') }}</span>
                </div>
                <svg class="dropdown-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </button>
            <div class="nav-dropdown {{ $isLembagaActive ? 'show' : '' }}" id="dropdown-lembaga">
                <a href="{{ route('univ.index') }}" class="nav-item {{ request()->routeIs('univ.*') ? 'active' : '' }}" data-title="{{ __('University') }}">
                    <i>🏛️</i><span>{{ __('University') }}</span>
                </a>
                <a href="{{ route('fakultas.index') }}" class="nav-item {{ request()->routeIs('fakultas.*') ? 'active' : '' }}" data-title="{{ __('Faculty') }}">
                    <i>🏫</i><span>{{ __('Faculty') }}</span>
                </a>
                <a href="{{ route('prodi.index') }}" class="nav-item {{ request()->routeIs('prodi.*') ? 'active' : '' }}" data-title="{{ __('Study Program') }}">
                    <i>📚</i><span>{{ __('Study Program') }}</span>
                </a>
            </div>

            @php 
                $isAkademikActive = request()->routeIs('visi.*') || request()->routeIs('gp.*') || request()->routeIs('plo.*') || request()->routeIs('bahan_kajian.*') || request()->routeIs('kurikulum.*') || request()->routeIs('subjects.*') || request()->routeIs('clo.*') || request()->routeIs('admin.rps.*'); 
            @endphp
            <button class="nav-group-btn {{ $isAkademikActive ? 'open' : '' }}" onclick="toggleDropdown('dropdown-akademik')" data-title="{{ __('Academic & OBE') }}">
                <div style="display: flex; align-items: center;">
                    <i>🎓</i><span>{{ __('Academic & OBE') }}</span>
                </div>
                <svg class="dropdown-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </button>
            <div class="nav-dropdown {{ $isAkademikActive ? 'show' : '' }}" id="dropdown-akademik">
                <a href="{{ route('visi.index') }}" class="nav-item {{ request()->routeIs('visi.*') ? 'active' : '' }}" data-title="{{ __('Vision & Mission') }}">
                    <i>👁️</i><span>{{ __('Vision & Mission') }}</span>
                </a>
                <a href="{{ route('gp.index') }}" class="nav-item {{ request()->routeIs('gp.*') ? 'active' : '' }}" data-title="{{ __('Graduate Profile (GP)') }}">
                    <i>👤</i><span>{{ __('Graduate Profile (GP)') }}</span>
                </a>
                <a href="{{ route('plo.index') }}" class="nav-item {{ request()->routeIs('plo.index') || request()->routeIs('plo.manage') ? 'active' : '' }}" data-title="{{ __('CPL (PLO)') }}">
                    <i>📝</i><span>{{ __('CPL (PLO)') }}</span>
                </a>
                <a href="{{ route('bahan_kajian.index') }}" class="nav-item {{ request()->routeIs('bahan_kajian.*') ? 'active' : '' }}" data-title="{{ __('Bahan Kajian (BK)') }}">
                    <i>📚</i><span>{{ __('Bahan Kajian (BK)') }}</span>
                </a>
                <a href="{{ route('subjects.index') }}" class="nav-item {{ request()->routeIs('subjects.*') ? 'active' : '' }}" data-title="{{ __('Courses') }}"><i>📘</i><span>{{ __('Courses') }}</span></a>
                <a href="{{ route('kurikulum.index') }}" class="nav-item {{ request()->routeIs('kurikulum.*') ? 'active' : '' }}" data-title="{{ __('Curriculum') }}"><i>📖</i><span>{{ __('Curriculum') }}</span></a>
                <a href="{{ route('clo.index') }}" class="nav-item {{ request()->routeIs('clo.*') ? 'active' : '' }}" data-title="{{ __('CPMK (CLO)') }}"><i>🎯</i><span>{{ __('CPMK (CLO)') }}</span></a>
                <a href="{{ route('admin.rps.index') }}" class="nav-item {{ request()->routeIs('admin.rps.*') ? 'active' : '' }}" data-title="{{ __('RPS') }}"><i>📑</i><span>{{ __('RPS') }}</span></a>
            </div>


            <button class="nav-group-btn" onclick="toggleDropdown('dropdown-pengaturan')" data-title="{{ __('Settings') }}">
                <div style="display: flex; align-items: center;">
                    <i>⚙️</i><span>{{ __('Settings') }}</span>
                </div>
                <svg class="dropdown-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </button>
            <div class="nav-dropdown" id="dropdown-pengaturan">
                @if(!Auth::user()->hasRole('kaprodi'))
                    <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}" data-title="{{ __('User Management') }}"><i>👥</i><span>{{ __('User Management') }}</span></a>
                @endif
                <a href="{{ route('assessment_types.index') }}" class="nav-item {{ request()->routeIs('assessment_types.*') ? 'active' : '' }}" data-title="{{ __('Assessment Types') }}"><i>📝</i><span>{{ __('Assessment Types') }}</span></a>
                <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}" data-title="{{ __('Settings') }}"><i>🛠️</i><span>{{ __('Settings') }}</span></a>
            </div>

            <a href="{{ route('dashboard') }}" class="nav-item" data-title="{{ __('Back to LMS') }}">
                <i>🎓</i><span>{{ __('Back to LMS') }}</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <!-- Logout moved to avatar dropdown -->
        </div>
    </div>

    <div class="main-content">
        <header>
            <div style="display: flex; align-items: center;">
                <button class="toggle-btn" onclick="toggleSidebar()">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                @yield('header_left')
            </div>
            <div style="display: flex; align-items: center; gap: 1rem; position: relative; color: var(--navbar-text);">
                <span class="header-user-email" style="font-size: 0.875rem; font-weight: 500;">{{ Auth::user()->name ?? Auth::user()->email }}</span>
                <div class="avatar-dropdown-wrapper" style="position: relative;">
                    <div style="width: 32px; height: 32px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; cursor: pointer; user-select: none; overflow: hidden;" onclick="toggleAvatarDropdown()">
                        @if(Auth::check() && Auth::user()->avatar)
                            <img src="{{ asset('img/avatars/' . Auth::user()->avatar) }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            {{ strtoupper(substr(Auth::user()->name ?? Auth::user()->email, 0, 1)) }}
                        @endif
                    </div>
                    <div id="avatar-dropdown" style="display: none; position: absolute; right: 0; top: 120%; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); width: 180px; z-index: 100; overflow: hidden;">
                        <a href="{{ route('password.change') }}" style="display: block; padding: 0.75rem 1rem; color: #374151; text-decoration: none; font-size: 0.875rem; border-bottom: 1px solid #e5e7eb; transition: background 0.2s;">
                            <i>🔑</i> {{ __('Change Password') }}
                        </a>
                        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" style="width: 100%; text-align: left; background: none; border: none; padding: 0.75rem 1rem; color: #ef4444; font-size: 0.875rem; font-family: inherit; cursor: pointer; transition: background 0.2s;">
                                <i>🚪</i> {{ __('Logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>

        <div class="content-padding">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" style="background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
    
    <script>
        function toggleAvatarDropdown() {
            const dropdown = document.getElementById('avatar-dropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }
        
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.avatar-dropdown-wrapper')) {
                const dropdown = document.getElementById('avatar-dropdown');
                if (dropdown) dropdown.style.display = 'none';
            }
        });

        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const btn = dropdown.previousElementSibling;
            
            if (document.getElementById('sidebar').classList.contains('collapsed')) {
                toggleSidebar();
            }

            dropdown.classList.toggle('show');
            btn.classList.toggle('open');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const logo = document.getElementById('sidebar-logo');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                if (sidebar.classList.contains('collapsed')) {
                    logo.src = "{{ asset(get_setting('favicon') ? 'img/favicon/' . get_setting('favicon') : 'img/icon_hui.png') }}";
                } else {
                    logo.src = "{{ asset(get_setting('dashboard_logo') ? 'img/logo_dashboard/' . get_setting('dashboard_logo') : 'img/logo_hui.png') }}";
                }
                // Save state to localStorage
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            }
        }

        // Restore sidebar state and Auto-hide alerts after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const logo = document.getElementById('sidebar-logo');
            
            // Restore sidebar state
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                logo.src = "{{ asset(get_setting('favicon') ? 'img/favicon/' . get_setting('favicon') : 'img/icon_hui.png') }}";
            }

            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 3000);
            });

            // Auto Logout after 10 seconds of inactivity
            let idleTimer;
            const idleLimit = 3600000; // 1 hour

            function resetIdleTimer() {
                clearTimeout(idleTimer);
                idleTimer = setTimeout(logoutUser, idleLimit);
            }

            function logoutUser() {
                const logoutForm = document.createElement('form');
                logoutForm.method = 'POST';
                logoutForm.action = "{{ route('logout') }}";
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = "{{ csrf_token() }}";
                
                logoutForm.appendChild(csrfInput);
                document.body.appendChild(logoutForm);
                
                alert('Sesi Anda berakhir karena tidak ada aktivitas selama 10 detik.');
                logoutForm.submit();
            }

            ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click'].forEach(event => {
                window.addEventListener(event, resetIdleTimer);
            });

            resetIdleTimer();
        });
    </script>
    @yield('scripts')
</body>
</html>
