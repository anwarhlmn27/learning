<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - System Dashboard</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset(get_setting('favicon') ? 'img/favicon/' . get_setting('favicon') : 'images/logo/icon_hui.png') }}">
	
	<!-- STYLESHEETS -->
	<link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link class="main-css" rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body {
            background: url('{{ asset("images/logo/gedung2.jpeg") }}') center center/cover no-repeat fixed;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.5) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="fix-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-6">
                    <div class="card mb-0 h-auto">
                        <div class="card-body">
                            <div class="text-center mb-2">
                                <a href="{{ url('/') }}">
                                    <img src="{{ asset(get_setting('login_logo') ? 'img/logo_login/' . get_setting('login_logo') : 'images/logo/logo_hui.png') }}" alt="Logo" width="250">
                                </a>
                            </div>
                            <h4 class="text-center mb-4">Sign in your account</h4>
                            
                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    {{ session('status') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <label class="form-label" for="email">{{ __('Email Address') }}</label>
                                    <input type="email" name="email" class="form-control" placeholder="name@institution.ac.id" id="email" value="{{ old('email') }}" required autofocus>
                                </div>
                                <div class="mb-4 position-relative">
                                    <label class="form-label" for="dlabPassword">{{ __('Password') }}</label>
                                    <input type="password" name="password" id="dlabPassword" class="form-control" required placeholder="{{ __('Password') }}">
                                    <span class="show-pass eye">
                                        <i class="fa fa-eye-slash"></i>
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                                <div class="form-row d-flex flex-wrap justify-content-between mt-4 mb-2">
                                    <div class="form-group">
                                        <div class="form-check custom-checkbox ms-1">
                                            <input type="checkbox" name="remember" class="form-check-input" id="basic_checkbox_1">
                                            <label class="form-check-label" for="basic_checkbox_1">{{ __('Remember me') }}</label>
                                        </div>
                                    </div>
                                    <div class="form-group ms-2">
                                        @if (Route::has('password.request'))
                                            <a class="btn-link" href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-center">
                                    <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                                    <button type="submit" class="btn btn-primary btn-block">{{ __('Sign In') }}</button>
                                </div>
                            </form>
                            @if(Route::has('register'))
                            <div class="new-account mt-3">
                                <p>Don't have an account? <a class="text-primary" href="{{ route('register') }}">Sign up</a></p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="{{ asset('vendor/global/global.min.js') }}"></script>
	<script src="{{ asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    
    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('js/dlabnav-init.js') }}"></script>

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
