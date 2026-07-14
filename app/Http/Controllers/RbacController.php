<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class RbacController extends Controller
{
    /**
     * Display the permissions matrix grid.
     */
    public function index()
    {
        // Authorize access
        if (!Gate::allows('manage-rbac')) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola RBAC.');
        }

        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        // Group permissions manually for clean UI presentation
        $permissionGroups = [
            'Kelas & Pembelajaran' => [
                'view-classes' => 'Melihat daftar kelas',
                'create-classes' => 'Membuat kelas baru',
                'edit-classes' => 'Mengubah konfigurasi kelas',
                'delete-classes' => 'Menghapus kelas',
                'enroll-students' => 'Mendaftarkan mahasiswa ke kelas',
            ],
            'Rating Pertemuan Dosen' => [
                'rate-session' => 'Memberikan rating/ulasan sesi pembelajaran',
                'view-ratings-anonymous' => 'Melihat ulasan rating secara anonim (Dosen)',
                'view-ratings-transparent' => 'Melihat ulasan rating secara transparan dengan nama (Admin/Kaprodi)',
            ],
            'Data Institusi (Universitas, Fakultas, Prodi)' => [
                'view-institusi' => 'Melihat data Institusi',
                'manage-institusi' => 'Mengelola (CRUD) data Institusi',
                'edit-prodi' => 'Mengubah data Program Studi saja',
            ],
            'Kurikulum OBE (Outcome-Based Education)' => [
                'manage-obe-curriculum' => 'Mengelola kurikulum OBE',
                'manage-obe-plo' => 'Mengelola PLO (Program Learning Outcome)',
                'manage-obe-visi' => 'Mengelola Visi Misi institusi',
            ],
            'Sistem & Administrator' => [
                'manage-users' => 'Mengelola data user (Dosen, Mahasiswa, BAAK)',
                'manage-rbac' => 'Mengontrol hak akses role & permission (RBAC Matrix)',
            ],
        ];

        // Eager load permissions for each role to easily check state in blade
        $rolesWithPermissions = Role::with('permissions')->orderBy('name')->get();

        return view('obe.rbac.index', compact('roles', 'permissions', 'permissionGroups', 'rolesWithPermissions'));
    }

    /**
     * Toggle permission on or off for a specific role.
     */
    public function togglePermission(Request $request)
    {
        if (!Gate::allows('manage-rbac')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'role_id'       => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
            'status'        => 'required|boolean',
        ]);

        $role = Role::findOrFail($request->role_id);
        $permissionId = $request->permission_id;

        if ($request->status) {
            // Check if relationship already exists
            $exists = $role->permissions()->where('permission_id', $permissionId)->exists();
            if (!$exists) {
                // Manually generate UUID for pivot ID column
                $role->permissions()->attach($permissionId, ['id' => (string) Str::uuid()]);
            }
        } else {
            $role->permissions()->detach($permissionId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hak akses berhasil diperbarui secara real-time.'
        ]);
    }
}
