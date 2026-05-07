<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add leader columns to institution tables
        Schema::table('univs', function (Blueprint $table) {
            $table->foreignUuid('rektor_id')->nullable()->constrained('user')->nullOnDelete();
        });

        Schema::table('fakultas', function (Blueprint $table) {
            $table->foreignUuid('dekan_id')->nullable()->constrained('user')->nullOnDelete();
        });

        Schema::table('prodis', function (Blueprint $table) {
            $table->foreignUuid('kaprodi_id')->nullable()->constrained('user')->nullOnDelete();
        });

        // Ensure id_prodi is dropped if it somehow still exists
        if (Schema::hasColumn('user', 'id_prodi')) {
            Schema::table('user', function (Blueprint $table) {
                // Drop foreign key first
                $table->dropForeign(['id_prodi']);
                $table->dropColumn('id_prodi');
            });
        }
    }

    public function down(): void
    {
        Schema::table('univs', function (Blueprint $table) {
            $table->dropForeign(['rektor_id']);
            $table->dropColumn('rektor_id');
        });

        Schema::table('fakultas', function (Blueprint $table) {
            $table->dropForeign(['dekan_id']);
            $table->dropColumn('dekan_id');
        });

        Schema::table('prodis', function (Blueprint $table) {
            $table->dropForeign(['kaprodi_id']);
            $table->dropColumn('kaprodi_id');
        });
    }
};
