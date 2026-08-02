<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Silakan ganti value di bawah ini dengan nama file logo Anda jika ingin di-set secara default saat fresh migrate
            // Pastikan file tersebut sudah ada di folder public/img/logo_login, public/img/logo_dashboard, dan public/img/favicon
            ['key' => 'login_logo', 'value' => ''], 
            ['key' => 'dashboard_logo', 'value' => ''],
            ['key' => 'favicon', 'value' => ''],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
