<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('univs', 'rector_sign') && !Schema::hasColumn('univs', 'sign')) {
            Schema::table('univs', function (Blueprint $table) {
                $table->renameColumn('rector_sign', 'sign');
            });
        }

        if (Schema::hasColumn('fakultas', 'dekan_sign') && !Schema::hasColumn('fakultas', 'sign')) {
            Schema::table('fakultas', function (Blueprint $table) {
                $table->renameColumn('dekan_sign', 'sign');
            });
        }

        Schema::table('prodis', function (Blueprint $table) {
            if (Schema::hasColumn('prodis', 'kaprodi_sign') && !Schema::hasColumn('prodis', 'sign')) {
                $table->renameColumn('kaprodi_sign', 'sign');
            } elseif (!Schema::hasColumn('prodis', 'sign')) {
                $table->string('sign')->nullable();
            }

            if (!Schema::hasColumn('prodis', 'nama_pimpinan')) {
                $table->string('nama_pimpinan')->after('short_name')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('univs', 'sign')) {
            Schema::table('univs', function (Blueprint $table) {
                $table->renameColumn('sign', 'rector_sign');
            });
        }

        if (Schema::hasColumn('fakultas', 'sign')) {
            Schema::table('fakultas', function (Blueprint $table) {
                $table->renameColumn('sign', 'dekan_sign');
            });
        }

        Schema::table('prodis', function (Blueprint $table) {
            if (Schema::hasColumn('prodis', 'sign')) {
                // We don't necessarily want to rename back if it was added new, 
                // but for consistency with the plan:
                $table->renameColumn('sign', 'kaprodi_sign');
            }
            if (Schema::hasColumn('prodis', 'nama_pimpinan')) {
                $table->dropColumn('nama_pimpinan');
            }
        });
    }
};
