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

        // 1. User Management Check
        if ($request->is('obe/users*')) {
            if (!$user->can('manage-users')) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses manajemen pengguna.');
            }
        }

        // 2. RBAC Management Check
        if ($request->is('obe/rbac*')) {
            if (!$user->can('manage-rbac')) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengontrol hak akses.');
            }
        }

        // 3. Vision & Mission Check (Visi)
        if ($request->is('obe/visi*')) {
            if (in_array($request->method(), ['GET', 'HEAD'])) {
                if (!$user->can('view-institusi') && !$user->can('manage-obe-visi')) {
                    abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat visi & misi.');
                }
            } else {
                if (!$user->can('manage-obe-visi')) {
                    abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengelola visi & misi.');
                }
            }
        }

        // 4. Graduate Profile & CPL (PLO) Check
        if ($request->is('obe/gp*') || $request->is('obe/plo*')) {
            if (in_array($request->method(), ['GET', 'HEAD'])) {
                if (!$user->can('view-institusi') && !$user->can('manage-obe-plo')) {
                    abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat CPL/PLO.');
                }
            } else {
                if (!$user->can('manage-obe-plo')) {
                    abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengelola CPL/PLO.');
                }
            }
        }

        // 5. Curriculum, Courses, CLO, Bahan Kajian Check
        if ($request->is('obe/bahan-kajian*') || $request->is('obe/subjects*') || $request->is('obe/clo*') || $request->is('obe/kurikulum*')) {
            if (in_array($request->method(), ['GET', 'HEAD'])) {
                if (!$user->can('view-institusi') && !$user->can('manage-obe-curriculum')) {
                    abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat data kurikulum.');
                }
            } else {
                if (!$user->can('manage-obe-curriculum')) {
                    abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengelola data kurikulum.');
                }
            }
        }

        // 6. Institutional Data (Univ, Fakultas, Prodi) Check
        if ($request->is('obe/univ*') || $request->is('obe/fakultas*') || $request->is('obe/prodi*')) {
            if (in_array($request->method(), ['GET', 'HEAD'])) {
                if (!$user->can('view-institusi') && !$user->can('manage-institusi')) {
                    abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat data Institusi.');
                }
            } else {
                // If editing Prodi (PUT/PATCH), allow if they have manage-institusi OR edit-prodi
                if ($request->is('obe/prodi*') && in_array($request->method(), ['PUT', 'PATCH'])) {
                    if (!$user->can('manage-institusi') && !$user->can('edit-prodi')) {
                        abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengubah data Program Studi.');
                    }
                } else {
                    // Otherwise (POST, DELETE, or PUT/PATCH on Univ/Fakultas), require manage-institusi
                    if (!$user->can('manage-institusi')) {
                        abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengelola data Institusi.');
                    }
                }
            }
        }

        return $next($request);
    }
}
