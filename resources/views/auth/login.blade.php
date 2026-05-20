<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem OBE</title>
    <link rel="icon" type="image/png" href="{{ asset(get_setting('favicon') ? 'img/favicon/' . get_setting('favicon') : 'img/icon_hui.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        /* Login page: background image set dynamically from settings */
        body {
            background: url('{{ asset("img/gedung2.jpeg") }}') center center/cover no-repeat fixed;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="login">
                    <img src="{{ asset(get_setting('login_logo') ? 'img/logo_login/' . get_setting('login_logo') : 'img/logo_hui.png') }}" alt="Logo" width="250">
                </div>
                <!-- <h2>Welcome</h2> -->
                <p>Please log in to your LMS account</p>
            </div>
            
            @if (session('status'))
                <div class="alert-success" style="background-color: rgba(16, 185, 129, 0.15); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.85rem; border-radius: 10px; margin-bottom: 1.25rem; font-size: 0.85rem; display: flex; align-items: flex-start; gap: 0.5rem; backdrop-filter: blur(4px);">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 18px; height: 18px; flex-shrink: 0; margin-top: 0.125rem;">
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

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">{{ __('Email Address') }}</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@institution.ac.id">
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">{{ __('Password') }}</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" required placeholder="{{ __('Password') }}">
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </span>
                    </div>
                </div>

                <div class="form-options" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <label class="remember-me" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 0;">
                        <input type="checkbox" name="remember" id="remember" style="width: 1rem; height: 1rem; accent-color: var(--primary);">
                        <span style="font-weight: 400; color: var(--text-muted);">{{ __('Remember me') }}</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-password" style="font-size: 0.8rem; color: #a5b4fc; text-decoration: none; font-weight: 500; transition: color 0.2s ease;">{{ __('Forgot Password?') }}</a>
                </div>
                <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                <button type="submit">{{ __('Sign In') }}</button>
            </form>

            <div class="footer">
                &copy; {{ date('Y') }} <a href="#" class="footer-link">Outcome-Based Education System</a>
            </div>
        </div>
    </div>
    @if(env('APP_ENV') !== 'local')
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ env("RECAPTCHA_SITE_KEY") }}', {action: 'login'}).then(function(token) {
                    document.getElementById('recaptcha_token').value = token;
                    form.submit();
                });
            });
        });
    </script>
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
    @endif
</body>
</html>
