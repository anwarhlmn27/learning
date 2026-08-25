<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // 1. Cek apakah email berakhiran @horizon.ac.id
            if (!str_ends_with(strtolower($googleUser->email), '@horizon.ac.id','@krw.horizon.ac.id','@phinmaed.com')) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Hanya email dengan domain @horizon.ac.id, @krw.horizon.ac.id, dan @phinmaed.com yang diizinkan untuk login.'
                ]);
            }

            // 2. Cari user di database berdasarkan email
            $user = User::where('email', $googleUser->email)->first();

            // 3. Jika user tidak ditemukan, tolak login
            if (!$user) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Email Anda (' . $googleUser->email . ') belum didaftarkan oleh Admin. Silakan hubungi admin.'
                ]);
            }

            // 4. Jika user ditemukan, cek status
            if ($user->status === 'inactive' || $user->status === 'Inactive') {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Anda sedang non-aktif. Silakan hubungi admin.'
                ]);
            }
            
            if ($user->student && $user->student->is_frozen) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Anda belum eligible, segera selesaikan ke Bagian Finance.'
                ]);
            }

            // 5. Login user
            Auth::login($user, true); // true for remember me
            
            $request->session()->regenerate();
            
            $intendedUrl = session('url.intended');
            $isStaff = Auth::user()->hasRole(['admin', 'rektor', 'dekan', 'kaprodi', 'baak', 'finance', 'kemahasiswaan']) 
                || Auth::user()->can('view-institusi') 
                || Auth::user()->can('manage-users');

            if (!$isStaff && $intendedUrl && str_contains($intendedUrl, '/obe/')) {
                session()->forget('url.intended');
            }

            if (Auth::user()->hasRole(['admin', 'rektor', 'dekan', 'kaprodi'])) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('dashboard'));

        } catch (Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal terhubung dengan Google. Silakan coba lagi.'
            ]);
        }
    }
}
