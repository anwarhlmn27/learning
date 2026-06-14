<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create New Password - System Dashboard</title>
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
                            <h4 class="text-center mb-4">Create New Password</h4>
                            
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">
                                
                                <div class="form-group" style="display: none;">
                                    <input type="email" name="email" value="{{ request()->query('email') }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="otp">6-Digit OTP</label>
                                    <input type="text" id="otp" name="otp" class="form-control" required autofocus placeholder="Enter 6-digit code" maxlength="6" pattern="\d{6}">
                                </div>
                                
                                <div class="mb-4 position-relative">
                                    <label class="form-label" for="password">New Password</label>
                                    <input type="password" id="password" name="password" class="form-control" required placeholder="Create a strong password">
                                    <span class="show-pass eye">
                                        <i class="fa fa-eye-slash"></i>
                                        <i class="fa fa-eye"></i>
                                    </span>
                                    <div class="mt-2 text-muted" style="font-size: 12px;">
                                        Requirements: Min 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 special char.
                                    </div>
                                </div>
                                
                                <div class="mb-4 position-relative">
                                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Repeat your new password">
                                    <span class="show-pass eye">
                                        <i class="fa fa-eye-slash"></i>
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-block">Update Password</button>
                                </div>
                            </form>
                            
                            <div class="new-account mt-3">
                                <p><a class="text-primary" href="{{ route('login') }}">&larr; Back to Login</a></p>
                            </div>
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
</body>
</html>
