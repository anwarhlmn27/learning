<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }



    public function login(Request $request)
    {
        // Verify Google reCAPTCHA
        if (env('APP_ENV') !== 'local') {
            $response = Http::timeout(10)->asForm()->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret' => env('RECAPTCHA_SECRET_KEY'),
                    'response' => $request->recaptcha_token,
                    'remoteip' => $request->ip(),
                ]
            );

            $result = $response->json();
            
            if (!isset($result['success']) || !$result['success']) {
                return back()->withErrors(['captcha' => 'Verifikasi Captcha gagal. Silakan coba lagi.']);
            }

            if (isset($result['score']) && $result['score'] < 0.3) {
                return back()->withErrors(['captcha' => 'Aktivitas mencurigakan terdeteksi.']);
            }
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            if (Auth::user()->status === 'Inactive') {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda sedang non-aktif. Silakan hubungi admin.']);
            }
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'email tidak terdaftar'])->withInput();
        }

        $token = Str::random(64);
        $otp = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'otp' => $otp,
                'created_at' => now()
            ]
        );

        Mail::to($request->email)->send(new \App\Mail\ResetPasswordMail($otp, $token, $request->email));

        return back()->with('status', 'Kami telah mengirimkan OTP dan link reset password ke email Anda.');
    }

    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'otp' => 'required|digits:6',
            'password' => [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->where('otp', $request->otp)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Token, OTP, atau Email tidak valid.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'email tidak terdaftar']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Password berhasil diubah. Silakan login dengan password baru.');
    }
}
