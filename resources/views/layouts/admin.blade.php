<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - OBE System</title>
    <link rel="icon" type="image/png" href="{{ asset(get_setting('favicon') ? 'img/favicon/' . get_setting('favicon') : 'img/icon_hui.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        /* Dynamic CSS vars based on user preferences */
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --sidebar-hover: #1f2937;
            --card-bg: #ffffff;
            --text-main: {{ Auth::check() && Auth::user()->content_font_color ? Auth::user()->content_font_color : '#111827' }};
            --text-muted: #6b7280;
            --success: #10b981;
            --danger: #ef4444;
            --bg: {{ Auth::check() && Auth::user()->content_color ? Auth::user()->content_color : '#f9fafb' }};
            --sidebar: {{ Auth::check() && Auth::user()->sidebar_color ? Auth::user()->sidebar_color : '#111827' }};
            --sidebar-text: {{ Auth::check() && Auth::user()->sidebar_font_color ? Auth::user()->sidebar_font_color : '#9ca3af' }};
            --header-bg: {{ Auth::check() && Auth::user()->navbar_color ? Auth::user()->navbar_color : '#ffffff' }};
            --navbar-text: {{ Auth::check() && Auth::user()->navbar_font_color ? Auth::user()->navbar_font_color : '#111827' }};
        }
        body {
            font-family: {{ Auth::check() && Auth::user()->font_family ? Auth::user()->font_family : "'Inter', sans-serif" }} !important;
        }
    </style>
    @yield('styles')
</head>
<body class="{{ Auth::check() && Auth::user()->hasRole(['rektor', 'dekan']) ? 'role-view-only' : '' }}">
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
                <a href="{{ route('analytics.index') }}" class="nav-item {{ request()->routeIs('analytics.*') ? 'active' : '' }}" data-title="{{ __('OBE Analytics') }}"><i>📊</i><span>{{ __('OBE Analytics') }}</span></a>
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
            <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                <button class="toggle-btn" onclick="toggleSidebar()">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                @yield('header_left')
                @if(Auth::check())
                    @foreach(Auth::user()->roles as $role)
                        <span style="background: rgba(79, 70, 229, 0.1); color: var(--primary); padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; border: 1px solid rgba(79, 70, 229, 0.2); letter-spacing: 0.05em; display: inline-block;">
                            {{ $role->name }} Dashboard
                        </span>
                    @endforeach
                @endif
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
                        <a href="{{ route('settings.index') }}" style="display: block; padding: 0.75rem 1rem; color: #374151; text-decoration: none; font-size: 0.875rem; border-bottom: 1px solid #e5e7eb; transition: background 0.2s;">
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
