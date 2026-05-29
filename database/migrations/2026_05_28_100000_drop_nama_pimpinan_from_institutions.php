<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('univs', function (Blueprint $table) {
            if (Schema::hasColumn('univs', 'nama_pimpinan')) {
                $table->dropColumn('nama_pimpinan');
            }
        });

        Schema::table('fakultas', function (Blueprint $table) {
            if (Schema::hasColumn('fakultas', 'nama_pimpinan')) {
                $table->dropColumn('nama_pimpinan');
            }
        });

        Schema::table('prodis', function (Blueprint $table) {
            if (Schema::hasColumn('prodis', 'nama_pimpinan')) {
                $table->dropColumn('nama_pimpinan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('univs', function (Blueprint $table) {
            $table->string('nama_pimpinan')->nullable();
        });

        Schema::table('fakultas', function (Blueprint $table) {
            $table->string('nama_pimpinan')->nullable();
        });

        Schema::table('prodis', function (Blueprint $table) {
            $table->string('nama_pimpinan')->nullable();
        });
    }
};
