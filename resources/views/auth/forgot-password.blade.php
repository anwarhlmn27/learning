<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Sistem OBE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --surface: transparent;
            --surface-glass: rgba(255, 255, 255, 0.1);
            --text-main: #ffffff;
            --text-muted: #e5e7eb;
            --border: rgba(255, 255, 255, 0.2);
            --error-bg: rgba(239, 68, 68, 0.15);
            --error-text: #fca5a5;
            --error-border: rgba(239, 68, 68, 0.3);
            --success-bg: rgba(16, 185, 129, 0.15);
            --success-text: #6ee7b7;
            --success-border: rgba(16, 185, 129, 0.3);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            background: linear-gradient(rgba(17, 24, 39, 0.75), rgba(17, 24, 39, 0.75)), url('{{ asset("img/gedung.jpeg") }}') center center/cover no-repeat fixed;
            position: relative;
            -webkit-font-smoothing: antialiased;
        }

        .login-wrapper {
            position: relative; z-index: 1; width: 100%; display: flex; justify-content: center; align-items: center; padding: 1.5rem;
        }

        .login-card {
            background: var(--surface-glass); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border); padding: 2.5rem 2rem; border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255,255,255,0.05) inset;
            width: 100%; max-width: 380px; transform: translateY(20px); opacity: 0;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideUp { to { transform: translateY(0); opacity: 1; } }

        .login-header { text-align: center; margin-bottom: 2rem; }
        .login-header h2 { color: var(--text-main); font-size: 1.5rem; font-weight: 700; letter-spacing: -0.025em; margin-bottom: 0.35rem; }
        .login-header p { color: var(--text-muted); font-size: 0.85rem; }

        .form-group { margin-bottom: 1.25rem; position: relative; }
        label { display: block; margin-bottom: 0.4rem; color: var(--text-main); font-size: 0.8rem; font-weight: 500; }
        .input-wrapper { position: relative; }
        .input-icon { position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: rgba(255, 255, 255, 0.6); transition: color 0.3s ease; pointer-events: none; }
        .input-icon svg { width: 18px; height: 18px; }

        input[type="email"] {
            width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 0.9rem;
            color: var(--text-main); transition: all 0.3s ease; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        input::placeholder { color: rgba(255, 255, 255, 0.5); font-weight: 400; }
        input:focus { outline: none; border-color: var(--primary); background: rgba(255, 255, 255, 0.12); box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25); }
        input:focus + .input-icon { color: #ffffff; }

        button[type="submit"] {
            width: 100%; padding: 0.85rem; background: var(--primary); color: white; border: none; border-radius: 10px;
            font-family: inherit; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -2px rgba(0, 0, 0, 0.2); position: relative; overflow: hidden; margin-top: 0.5rem;
        }

        button[type="submit"]::after {
            content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: all 0.5s ease;
        }

        button[type="submit"]:hover { background: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 8px 12px -1px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2); }
        button[type="submit"]:hover::after { left: 100%; }

        .alert {
            padding: 0.85rem; border-radius: 10px; margin-bottom: 1.25rem; font-size: 0.85rem;
            display: flex; align-items: flex-start; gap: 0.5rem; animation: slideUp 0.3s ease forwards; backdrop-filter: blur(4px);
        }

        .alert-error { background-color: var(--error-bg); color: var(--error-text); border: 1px solid var(--error-border); }
        .alert-success { background-color: var(--success-bg); color: var(--success-text); border: 1px solid var(--success-border); }
        
        .alert svg { width: 18px; height: 18px; flex-shrink: 0; margin-top: 0.125rem; }

        .back-link {
            display: block; text-align: center; margin-top: 1.5rem; color: #a5b4fc; font-size: 0.85rem; text-decoration: none; font-weight: 500; transition: color 0.2s ease;
        }
        .back-link:hover { color: #ffffff; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div style="margin-bottom: 1rem;">
                    <img src="{{ asset('img/logo_hui.png') }}" alt="Logo HUI" width="200">
                </div>
                <h2>Reset Password</h2>
                <p>Enter your email to receive a password reset link and OTP</p>
            </div>
            
            @if (session('status'))
                <div class="alert alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div style="margin-bottom: {{ $loop->last ? '0' : '0.25rem' }}">{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your registered email">
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        </span>
                    </div>
                </div>
                
                <button type="submit">Send Reset Link & OTP</button>
            </form>

            <a href="{{ route('login') }}" class="back-link">
                &larr; Back to Login
            </a>
        </div>
    </div>
</body>
</html>
