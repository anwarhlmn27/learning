<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Daftar Permissions
        $permissions = [
            'view-classes'             => 'Melihat daftar kelas',
            'create-classes'           => 'Membuat kelas baru',
            'edit-classes'             => 'Mengubah konfigurasi kelas',
            'delete-classes'           => 'Menghapus kelas',
            'enroll-students'          => 'Mendaftarkan mahasiswa ke kelas',
            'rate-session'             => 'Memberikan rating/ulasan sesi pembelajaran',
            'view-ratings-anonymous'   => 'Melihat ulasan rating secara anonim (Dosen)',
            'view-ratings-transparent' => 'Melihat ulasan rating secara transparan dengan nama (Admin/Kaprodi)',
            'manage-obe-curriculum'    => 'Mengelola kurikulum OBE',
            'manage-obe-plo'           => 'Mengelola PLO (Program Learning Outcome)',
            'manage-obe-visi'          => 'Mengelola Visi Misi institusi',
            'manage-users'             => 'Mengelola data user',
            'manage-rbac'              => 'Mengontrol hak akses role & permission (RBAC Settings)',
            'view-institusi'           => 'Melihat data Institusi (Universitas, Fakultas, Prodi)',
            'manage-institusi'         => 'Mengelola (CRUD) data Institusi (Universitas, Fakultas, Prodi)',
            'edit-prodi'               => 'Mengubah data Program Studi saja',
        ];

        $permissionModels = [];
        foreach ($permissions as $name => $description) {
            $permissionModels[$name] = Permission::firstOrCreate(['name' => $name]);
        }

        // Helper function to prepare sync data with manual UUID for primary key 'id' in pivot
        $prepareSyncData = function ($permissionNames) use ($permissionModels) {
            $syncData = [];
            foreach ($permissionNames as $name) {
                if (isset($permissionModels[$name])) {
                    $id = $permissionModels[$name]->id;
                    $syncData[$id] = ['id' => (string) Str::uuid()];
                }
            }
            return $syncData;
        };

        // 2. Petakan Permissions ke Roles bawaan
        
        // Admin: Dapatkan semua permission
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminPermissions = array_keys($permissions);
            $adminRole->permissions()->sync($prepareSyncData($adminPermissions));
        }

        // Rektor: Melihat kelas, OBE, & Institusi (View Only)
        $rektorRole = Role::where('name', 'rektor')->first();
        if ($rektorRole) {
            $rektorPermissions = ['view-classes', 'view-ratings-transparent', 'manage-obe-curriculum', 'manage-obe-plo', 'manage-obe-visi', 'view-institusi'];
            $rektorRole->permissions()->sync($prepareSyncData($rektorPermissions));
        }

        // Dekan: Melihat kelas, OBE, & Institusi (View Only)
        $dekanRole = Role::where('name', 'dekan')->first();
        if ($dekanRole) {
            $dekanPermissions = ['view-classes', 'view-ratings-transparent', 'manage-obe-curriculum', 'manage-obe-plo', 'manage-obe-visi', 'view-institusi'];
            $dekanRole->permissions()->sync($prepareSyncData($dekanPermissions));
        }

        // Kaprodi: Edit kelas, OBE, Rating transparan, Visi Misi, melihat institusi & edit prodi miliknya
        $kaprodiRole = Role::where('name', 'kaprodi')->first();
        if ($kaprodiRole) {
            $kaprodiPermissions = [
                'view-classes', 'create-classes', 'edit-classes', 'enroll-students',
                'view-ratings-transparent', 'manage-obe-curriculum', 'manage-obe-plo', 'manage-obe-visi',
                'view-institusi', 'edit-prodi'
            ];
            $kaprodiRole->permissions()->sync($prepareSyncData($kaprodiPermissions));
        }

        // Dosen: Melihat kelas, mengisi RPS (implisit), & melihat rating anonim
        $dosenRole = Role::where('name', 'dosen')->first();
        if ($dosenRole) {
            $dosenPermissions = ['view-classes', 'view-ratings-anonymous'];
            $dosenRole->permissions()->sync($prepareSyncData($dosenPermissions));
        }

        // BAAK: Mengelola kelas, enroll mahasiswa, user, & institusi
        $baakRole = Role::where('name', 'baak')->first();
        if ($baakRole) {
            $baakPermissions = ['view-classes', 'create-classes', 'edit-classes', 'enroll-students', 'manage-users', 'view-institusi', 'manage-institusi'];
            $baakRole->permissions()->sync($prepareSyncData($baakPermissions));
        }

        // Finance: Hanya melihat kelas & institusi
        $financeRole = Role::where('name', 'finance')->first();
        if ($financeRole) {
            $financePermissions = ['view-classes', 'view-institusi'];
            $financeRole->permissions()->sync($prepareSyncData($financePermissions));
        }

        // Mahasiswa: Melihat kelas & memberikan rating
        $mahasiswaRole = Role::where('name', 'mahasiswa')->first();
        if (!$mahasiswaRole) {
            // Sesuai seeder lama, kadang memakai nama 'student' atau 'mahasiswa'
            $mahasiswaRole = Role::where('name', 'student')->first();
        }
        if ($mahasiswaRole) {
            $mahasiswaPermissions = ['view-classes', 'rate-session'];
            $mahasiswaRole->permissions()->sync($prepareSyncData($mahasiswaPermissions));
        }
    }
}
