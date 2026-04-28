<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->status === 'Inactive') {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Akun Anda sedang non-aktif.']);
        }

        if ($request->user() && $request->user()->hasRole('admin')) {
            return $next($request);
        }

        abort(403, 'Akses ditolak. Hanya Admin yang dapat mengakses halaman ini.');
    }
}
