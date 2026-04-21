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
        Schema::table('visis', function (Blueprint $table) {
            if (!Schema::hasColumn('visis', 'doc_penyusunan')) {
                $table->string('doc_penyusunan')->nullable();
            }
            if (!Schema::hasColumn('visis', 'doc_pengesahan')) {
                $table->string('doc_pengesahan')->nullable();
            }
            if (!Schema::hasColumn('visis', 'doc_sosialisasi')) {
                $table->string('doc_sosialisasi')->nullable();
            }
            if (!Schema::hasColumn('visis', 'doc_hasil_survey')) {
                $table->string('doc_hasil_survey')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visis', function (Blueprint $table) {
            $table->dropColumn(['doc_penyusunan', 'doc_pengesahan', 'doc_sosialisasi', 'doc_hasil_survey']);
        });
    }
};
