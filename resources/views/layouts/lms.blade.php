<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Dashboard - {{ config('app.name', 'Laravel') }}</title>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --sidebar-bg: #1f2937;
            --navbar-bg: #ffffff;
            --bg-color: #f3f4f6;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --white: #ffffff;
            --border: #e5e7eb;
            --radius: 0.5rem;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar Placeholder */
        .lms-sidebar {
            width: 250px;
            background-color: var(--white);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }
        
        .lms-sidebar-header {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border);
        }
        
        .lms-sidebar-header h2 {
            margin: 0;
            color: var(--primary);
            font-size: 1.25rem;
            font-weight: 700;
        }

        .lms-nav {
            padding: 1rem 0;
            flex-grow: 1;
            overflow-y: auto;
        }

        .lms-nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--text-main);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .lms-nav-item:hover, .lms-nav-item.active {
            background-color: #e0e7ff;
            color: var(--primary);
            border-left-color: var(--primary);
        }

        .lms-nav-item i {
            margin-right: 1rem;
            font-size: 1.25rem;
        }

        /* Main Content */
        .lms-main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        header {
            background-color: var(--white);
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .content-area {
            flex-grow: 1;
            padding: 2rem;
            overflow-y: auto;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Cards */
        .card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-body {
            padding: 1.5rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .btn:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-outline {
            background-color: transparent;
            color: var(--text-main);
            border: 1px solid var(--border);
        }

    </style>
</head>
<body>

    <aside class="lms-sidebar">
        <div class="lms-sidebar-header">
            <h2>LMS Learning</h2>
        </div>
        <nav class="lms-nav">
            <a href="{{ route('dashboard') }}" class="lms-nav-item active">
                <i>🏠</i> {{ __('Dashboard') }}
            </a>
            <a href="#" class="lms-nav-item">
                <i>📚</i> {{ __('My Classes') }}
            </a>
            <a href="#" class="lms-nav-item">
                <i>📝</i> {{ __('Assignments') }}
            </a>
            @if(Auth::user()->hasRole(['admin', 'kaprodi', 'rektor', 'dekan']))
            <a href="{{ route('admin.dashboard') }}" class="lms-nav-item" style="margin-top: auto; border-top: 1px solid #e5e7eb; background-color: #f8fafc;">
                <i>⚙️</i> {{ __('OBE Administration') }}
            </a>
            @endif
        </nav>
    </aside>

    <main class="lms-main">
        <header>
            <div>
                <h1 style="font-size: 1.25rem; font-weight: 600; margin: 0;">@yield('header_title', 'Dashboard')</h1>
            </div>
            <div class="user-profile">
                <span style="font-size: 0.875rem; font-weight: 500;">{{ Auth::user()->name ?? Auth::user()->email }}</span>
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? Auth::user()->email, 0, 1)) }}
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Logout</button>
                </form>
            </div>
        </header>

        <div class="content-area">
            @yield('content')
        </div>
    </main>

</body>
</html>
