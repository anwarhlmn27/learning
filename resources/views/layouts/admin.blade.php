<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - OBE System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #f9fafb;
            --sidebar: #111827;
            --sidebar-text: #9ca3af;
            --sidebar-hover: #1f2937;
            --card-bg: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --success: #10b981;
            --danger: #ef4444;
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
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            color: white;
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
            color: #4b5563;
            letter-spacing: 0.05em;
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
            color: #4b5563;
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

        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid #1f2937;
        }

        .main-content {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .content-padding {
            padding: 2rem;
        }

        .card {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 2rem;
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
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">OBE System</div>
        <nav class="sidebar-nav">
            <div class="nav-label">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>

            @php 
                $isLembagaActive = request()->routeIs('univ.*') || request()->routeIs('fakultas.*') || request()->routeIs('prodi.*'); 
            @endphp
            <button class="nav-group-btn {{ $isLembagaActive ? 'open' : '' }}" onclick="toggleDropdown('dropdown-lembaga')">
                Institution
                <svg class="dropdown-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </button>
            <div class="nav-dropdown {{ $isLembagaActive ? 'show' : '' }}" id="dropdown-lembaga">
                <a href="{{ route('univ.index') }}" class="nav-item {{ request()->routeIs('univ.*') ? 'active' : '' }}">University</a>
                <a href="{{ route('fakultas.index') }}" class="nav-item {{ request()->routeIs('fakultas.*') ? 'active' : '' }}">Faculty</a>
                <a href="{{ route('prodi.index') }}" class="nav-item {{ request()->routeIs('prodi.*') ? 'active' : '' }}">Study Program</a>
            </div>

            @php 
                $isAkademikActive = request()->routeIs('visi.*'); 
            @endphp
            <button class="nav-group-btn {{ $isAkademikActive ? 'open' : '' }}" onclick="toggleDropdown('dropdown-akademik')">
                Academic & OBE
                <svg class="dropdown-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </button>
            <div class="nav-dropdown {{ $isAkademikActive ? 'show' : '' }}" id="dropdown-akademik">
                <a href="{{ route('visi.index') }}" class="nav-item {{ request()->routeIs('visi.*') ? 'active' : '' }}">Vision & Mission</a>
                <a href="#" class="nav-item">Graduate Profile (GP)</a>
                <a href="#" class="nav-item">CPL (PLO)</a>
                <a href="#" class="nav-item">Curriculum</a>
                <a href="#" class="nav-item">Courses</a>
                <a href="#" class="nav-item">CPMK (CLO)</a>
            </div>

            <button class="nav-group-btn" onclick="toggleDropdown('dropdown-pemetaan')">
                Mapping
                <svg class="dropdown-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </button>
            <div class="nav-dropdown" id="dropdown-pemetaan">
                <a href="#" class="nav-item">Curriculum Mapping</a>
            </div>

            <button class="nav-group-btn" onclick="toggleDropdown('dropdown-pengaturan')">
                Settings
                <svg class="dropdown-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </button>
            <div class="nav-dropdown" id="dropdown-pengaturan">
                <a href="#" class="nav-item">User Management</a>
            </div>
        </nav>
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <header>
            <div>@yield('header_left')</div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 0.875rem; font-weight: 500;">{{ Auth::user()->email }}</span>
                <div style="width: 32px; height: 32px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">A</div>
            </div>
        </header>

        <div class="content-padding">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @yield('content')
        </div>
    </div>
    
    <script>
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const btn = dropdown.previousElementSibling;
            
            // Toggle classes
            dropdown.classList.toggle('show');
            btn.classList.toggle('open');
        }

        // Auto-hide alerts after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
    @yield('scripts')
</body>
</html>
