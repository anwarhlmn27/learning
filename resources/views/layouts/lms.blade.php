<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horizon - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lms.css') }}?v={{ filemtime(public_path('css/lms.css')) }}">
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

        /* ====== TOAST NOTIFICATION ====== */
        #lms-toast-container {
            position: fixed;
            top: 1.25rem;
            right: 1.25rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            pointer-events: none;
        }
        .lms-toast {
            pointer-events: all;
            display: flex;
            align-items: center;
            gap: 0.9rem;
            border-radius: 10px;
            padding: 0.85rem 1.1rem;
            min-width: 280px;
            max-width: 370px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 24px rgba(0,0,0,0.18);
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.35s cubic-bezier(.22,1,.36,1), opacity 0.28s ease;
        }
        .lms-toast.show { transform: translateX(0); opacity: 1; }
        .lms-toast.hide { transform: translateX(120%); opacity: 0; }
        .lms-toast-icon {
            flex-shrink: 0;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 900;
            color: #fff;
        }
        .lms-toast-body   { flex: 1; min-width: 0; }
        .lms-toast-title  {
            font-size: 0.88rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.1rem;
            line-height: 1.3;
        }
        .lms-toast-message {
            font-size: 0.82rem;
            font-weight: 400;
            color: rgba(255,255,255,0.88);
            line-height: 1.4;
            word-break: break-word;
        }
        .lms-toast-close {
            flex-shrink: 0;
            background: rgba(255,255,255,0.2);
            border: none;
            cursor: pointer;
            color: #fff;
            font-size: 1rem;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            transition: background 0.2s;
            padding: 0;
        }
        .lms-toast-close:hover { background: rgba(255,255,255,0.35); }
        .lms-toast-progress {
            position: absolute;
            bottom: 0; left: 0;
            height: 3px;
            width: 100%;
            transform-origin: left;
            background: rgba(255,255,255,0.4);
            animation: toast-progress 2s linear forwards;
        }
        @keyframes toast-progress { from { transform: scaleX(1); } to { transform: scaleX(0); } }
        /* type colours — solid backgrounds */
        .lms-toast.toast-success { background: #5aab6e; }
        .lms-toast.toast-error   { background: #e05252; }
        /* Menu button always visible now */
        .mobile-menu-btn { display: block; }
        .sidebar-overlay { display: none; }
        
        @media (max-width: 768px) {
            .lms-sidebar {
                position: absolute;
                height: 100%;
                z-index: 1000;
                margin-left: -250px;
                transition: margin-left 0.3s;
            }
            .lms-sidebar.mobile-open {
                margin-left: 0;
            }
            .sidebar-overlay {
                display: none;
                position: absolute;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            .sidebar-overlay.active {
                display: block;
            }
            header { padding: 0 1rem; }
            .content-area { padding: 1rem; }
        }

        /* Quiz Mode */
        .quiz-mode-active .lms-sidebar, 
        .quiz-mode-active header { display: none !important; }
        .quiz-mode-active .content-area { padding: 0 !important; }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>

    <aside class="lms-sidebar" id="lms-sidebar">
        <div class="lms-sidebar-header">
            <h2>Horizon LMS</h2>
        </div>
        <nav class="lms-nav">
            @php
                // Resolve active prodi context for sidebar links
                $sidebarUser = Auth::user();
                $sidebarProdiId = request()->prodi_id ?? session('selected_prodi_id');
                $sidebarProdi   = null;

                if ($sidebarUser->hasRole('kaprodi')) {
                    $sidebarProdi   = \App\Models\Prodi::where('kaprodi_id', $sidebarUser->id)->first();
                    $sidebarProdiId = $sidebarProdi?->id;
                } elseif ($sidebarProdiId) {
                    $sidebarProdi = \App\Models\Prodi::find($sidebarProdiId);
                }

                $prodiParam = $sidebarProdiId ? ['prodi_id' => $sidebarProdiId] : [];
            @endphp

            <a href="{{ route('dashboard') }}" class="lms-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i>🏠</i> {{ __('Dashboard') }}
            </a>

            {{-- Show selected prodi context badge --}}
            @if($sidebarProdi)
            <div style="padding: 0.4rem 1rem 0.6rem; margin-bottom: 0.25rem;">
                <div style="background: rgba(79,70,229,0.08); border-radius: 8px; padding: 0.5rem 0.75rem;">
                    <p style="margin:0; font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#6b7280;">Prodi Aktif</p>
                    <p style="margin:0.1rem 0 0; font-size:0.8rem; font-weight:600; color:var(--primary); line-height:1.3;">{{ $sidebarProdi->nama_prodi }}</p>
                    @if(!$sidebarUser->hasRole('kaprodi'))
                    <a href="{{ route('dashboard') }}" onclick="sessionStorage.setItem('clear_prodi','1')" style="font-size:0.7rem; color:#6b7280; text-decoration:underline;">← Ganti Prodi</a>
                    @endif
                </div>
            </div>
            @endif

            <a href="{{ route('classes.index', $prodiParam) }}" class="lms-nav-item {{ request()->routeIs('classes.*') && !request()->routeIs('classes.archived') ? 'active' : '' }}">
                <i>📚</i> {{ __('Kelas Aktif') }}
            </a>
            <a href="{{ route('classes.archived', $prodiParam) }}" class="lms-nav-item {{ request()->routeIs('classes.archived') ? 'active' : '' }}">
                <i>🗃️</i> {{ __('Kelas Arsip') }}
            </a>
            @if($sidebarUser->hasRole(['admin', 'kaprodi']))
            <a href="{{ route('dosen.index', $prodiParam) }}" class="lms-nav-item {{ request()->routeIs('dosen.*') ? 'active' : '' }}">
                <i>👨‍🏫</i> {{ __('Data Dosen') }}
            </a>
            <a href="{{ route('mahasiswa.index', $prodiParam) }}" class="lms-nav-item {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}">
                <i>🎓</i> {{ __('Data Mahasiswa') }}
            </a>
            @endif
            @if($sidebarUser->hasRole(['admin', 'kaprodi', 'rektor', 'dekan']))
            <a href="{{ route('admin.dashboard') }}" class="lms-nav-item" style="margin-top: auto; border-top: 1px solid #e5e7eb; background-color: #f8fafc;">
                <i>⚙️</i> {{ __('OBE Administration') }}
            </a>
            @endif
        </nav>
    </aside>

    <main class="lms-main">
        <header>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <button class="mobile-menu-btn" onclick="toggleSidebar()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-main); padding: 0; margin-right: 0.5rem;">☰</button>
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

    <!-- ====== GLOBAL TOAST CONTAINER ====== -->
    <div id="lms-toast-container"></div>

    @if(session('success'))
    <script>document.addEventListener('DOMContentLoaded',function(){lmsToast('success',@json(session('success')));});</script>
    @endif
    @if(session('error'))
    <script>document.addEventListener('DOMContentLoaded',function(){lmsToast('error',@json(session('error')));});</script>
    @endif
    @if(session('warning'))
    <script>document.addEventListener('DOMContentLoaded',function(){lmsToast('warning',@json(session('warning')));});</script>
    @endif
    @if(session('info'))
    <script>document.addEventListener('DOMContentLoaded',function(){lmsToast('info',@json(session('info')));});</script>
    @endif

    {{-- Laravel $errors bag (from $request->validate()) → fire as error toasts --}}
    @if($errors->any())
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($errors->all() as $msg)
            lmsToast('error', @json($msg), 4000);
        @endforeach
    });
    </script>
    @endif

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

        function toggleSidebar() {
            const sidebar = document.getElementById('lms-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (window.innerWidth <= 768) {
                if (sidebar) sidebar.classList.toggle('mobile-open');
                if (overlay) overlay.classList.toggle('active');
            } else {
                if (sidebar) sidebar.classList.toggle('collapsed');
            }
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
            // trigger slide-in
            requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.add('show')));
            // auto dismiss
            setTimeout(() => lmsDismissToast(toast), duration);
        }

        function lmsDismissToast(toast) {
            if (!toast || toast.classList.contains('hide')) return;
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(() => toast && toast.remove(), 400);
        }

        /* Legacy: auto-dismiss old inline .flash-alert / .alert elements after 2s */
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

            // Auto Logout after 10 seconds of inactivity
            let idleTimer;
            const idleLimit = 100000;
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
                alert('Sesi Anda berakhir karena tidak ada aktivitas selama 10 detik.');
                logoutForm.submit();
            }
            ['mousemove','mousedown','keydown','scroll','touchstart','click'].forEach(ev => {
                window.addEventListener(ev, resetIdleTimer);
            });
            resetIdleTimer();
        });
    </script>
</body>
</html>
