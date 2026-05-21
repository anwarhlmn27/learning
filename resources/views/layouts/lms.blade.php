<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horizon - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lms.css') }}">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg-color: {{ Auth::check() && Auth::user()->lms_content_color ? Auth::user()->lms_content_color : '#f3f4f6' }};
            --text-main: {{ Auth::check() && Auth::user()->lms_content_font_color ? Auth::user()->lms_content_font_color : '#1f2937' }};
            --sidebar-bg: {{ Auth::check() && Auth::user()->lms_sidebar_color ? Auth::user()->lms_sidebar_color : '#1f2937' }};
            --sidebar-text: {{ Auth::check() && Auth::user()->lms_sidebar_font_color ? Auth::user()->lms_sidebar_font_color : '#9ca3af' }};
            --navbar-bg: {{ Auth::check() && Auth::user()->lms_navbar_color ? Auth::user()->lms_navbar_color : '#ffffff' }};
            --navbar-text: {{ Auth::check() && Auth::user()->lms_navbar_font_color ? Auth::user()->lms_navbar_font_color : '#111827' }};
        }
        body {
            font-family: {{ Auth::check() && Auth::user()->lms_font_family ? Auth::user()->lms_font_family : "'Inter', sans-serif" }} !important;
            background-color: var(--bg-color);
            color: var(--text-main);
        }
    </style>
</head>
<body>

    <aside class="lms-sidebar">
        <div class="lms-sidebar-header">
            <h2>Horizon LMS</h2>
        </div>
        <nav class="lms-nav">
            <a href="{{ route('dashboard') }}" class="lms-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i>🏠</i> {{ __('Dashboard') }}
            </a>
            <a href="{{ route('classes.index') }}" class="lms-nav-item {{ request()->routeIs('classes.*') && !request()->routeIs('classes.archived') ? 'active' : '' }}">
                <i>📚</i> {{ __('My Classes') }}
            </a>
            <a href="{{ route('classes.archived') }}" class="lms-nav-item {{ request()->routeIs('classes.archived') ? 'active' : '' }}">
                <i>🗃️</i> {{ __('Kelas Arsip') }}
            </a>
            @if(Auth::user()->hasRole(['admin', 'kaprodi']))
            <a href="{{ route('dosen.index') }}" class="lms-nav-item {{ request()->routeIs('dosen.*') ? 'active' : '' }}">
                <i>👨‍🏫</i> {{ __('Data Dosen') }}
            </a>
            <a href="{{ route('mahasiswa.index') }}" class="lms-nav-item {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}">
                <i>🎓</i> {{ __('Data Mahasiswa') }}
            </a>
            @endif
            @if(Auth::user()->hasRole(['admin', 'kaprodi', 'rektor', 'dekan']))
            <a href="{{ route('admin.dashboard') }}" class="lms-nav-item" style="margin-top: auto; border-top: 1px solid #e5e7eb; background-color: #f8fafc;">
                <i>⚙️</i> {{ __('OBE Administration') }}
            </a>
            @endif
        </nav>
    </aside>

    <main class="lms-main">
        <header>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <h1 style="font-size: 1.25rem; font-weight: 600; margin: 0;">@yield('header_title', 'Dashboard')</h1>
                @if(Auth::check())
                    @foreach(Auth::user()->roles as $role)
                        <span style="background: rgba(79, 70, 229, 0.1); color: var(--primary); padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; border: 1px solid rgba(79, 70, 229, 0.2); letter-spacing: 0.05em; display: inline-block;">
                            {{ $role->name }}
                        </span>
                    @endforeach
                @endif
            </div>
            <div class="user-profile" style="display: flex; align-items: center; gap: 1rem; position: relative;">
                <span style="font-size: 0.875rem; font-weight: 500;">{{ Auth::user()->name ?? Auth::user()->email }}</span>
                <div class="avatar-dropdown-wrapper" style="position: relative;">
                    <div class="user-avatar" style="width: 36px; height: 36px; border-radius: 50%; background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer; user-select: none; overflow: hidden;" onclick="toggleAvatarDropdown()">
                        @if(Auth::check() && Auth::user()->avatar)
                            <img src="{{ asset('img/avatars/' . Auth::user()->avatar) }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            {{ strtoupper(substr(Auth::user()->name ?? Auth::user()->email, 0, 1)) }}
                        @endif
                    </div>
                    <div id="avatar-dropdown" style="display: none; position: absolute; right: 0; top: 120%; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); width: 180px; z-index: 100; overflow: hidden;">
                        <a href="{{ route('lms.password.change') }}" style="display: block; padding: 0.75rem 1rem; color: #374151; text-decoration: none; font-size: 0.875rem; border-bottom: 1px solid #e5e7eb; transition: background 0.2s;">
                            <i>🔑</i> {{ __('Change Password') }}
                        </a>
                        <a href="{{ route('lms.settings.index') }}" style="display: block; padding: 0.75rem 1rem; color: #374151; text-decoration: none; font-size: 0.875rem; border-bottom: 1px solid #e5e7eb; transition: background 0.2s;">
                            <i>⚙️</i> {{ __('Settings') }}
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

        <div class="content-area">
            @yield('content')
        </div>
    </main>

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

        // Auto-dismiss flash notifications after 2 seconds
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.flash-alert').forEach(function(el) {
                setTimeout(function() {
                    el.style.transition = 'opacity 0.5s ease, max-height 0.5s ease, margin 0.5s ease, padding 0.5s ease';
                    el.style.opacity = '0';
                    el.style.maxHeight = '0';
                    el.style.padding = '0';
                    el.style.margin = '0';
                    el.style.overflow = 'hidden';
                    setTimeout(function() { el.remove(); }, 500);
                }, 2000);
            });
        });
    </script>
</body>
</html>
