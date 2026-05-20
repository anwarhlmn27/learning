<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['admin', 'rektor', 'dekan', 'kaprodi', 'dosen', 'baak', 'finance', 'kemahasiswaan','mahasiswa'];
        foreach ($roles as $role) {
            \App\Models\Role::firstOrCreate(['name' => $role]);
        }

        $adminUser = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin System',
                'password' => Hash::make('Password#123'),
                'status' => 'active',
                'role' => 1
            ]
        );

        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        if (!$adminUser->roles()->where('role_id', $adminRole->id)->exists()) {
            $adminUser->roles()->attach($adminRole->id, ['id' => (string) \Illuminate\Support\Str::uuid()]);
        }
    }
}
