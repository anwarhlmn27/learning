<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Kaprodi restriction: cannot access user management
        if ($user->hasRole('kaprodi') && $request->is('obe/users*')) {
            abort(403, 'Akses ditolak. Kaprodi tidak dapat mengakses manajemen pengguna.');
        }

        // Restriksi untuk Rektor dan Dekan: View Only untuk SEMUA menu
        if ($user->hasRole(['rektor', 'dekan'])) {
            if (!in_array($request->method(), ['GET', 'HEAD'])) {
                abort(403, 'Akses ditolak. Anda hanya memiliki hak akses untuk melihat data (View Only).');
            }
        }

        // Restriksi khusus Kaprodi pada menu Institusi (Univ, Fakultas, Prodi)
        if ($user->hasRole('kaprodi')) {
            if ($request->is('obe/univ*') || $request->is('obe/fakultas*')) {
                // Univ dan Fakultas: View Only
                if (!in_array($request->method(), ['GET', 'HEAD'])) {
                    abort(403, 'Akses ditolak. Kaprodi hanya dapat melihat data Universitas dan Fakultas.');
                }
            } elseif ($request->is('obe/prodi*')) {
                // Prodi: View & Edit (PUT/PATCH) diperbolehkan, tapi Create(POST) dan Delete(DELETE) ditolak
                if (in_array($request->method(), ['POST', 'DELETE'])) {
                    abort(403, 'Akses ditolak. Kaprodi hanya dapat mengubah data Prodi, bukan menambah atau menghapusnya.');
                }
            }
        }

        return $next($request);
    }
}
