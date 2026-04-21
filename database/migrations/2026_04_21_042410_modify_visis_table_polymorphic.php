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
            // Drop index if it exists to avoid duplication error
            try {
                $table->dropIndex('visis_visible_id_visible_type_index');
            } catch (\Exception $e) {
                // Ignore if not exists
            }

            if (!Schema::hasColumn('visis', 'visible_id')) {
                $table->uuid('visible_id');
            }
            if (!Schema::hasColumn('visis', 'visible_type')) {
                $table->string('visible_type');
            }
            
            $table->index(['visible_id', 'visible_type']);

            $table->text('visi')->change();
            $table->text('misi')->change();
            $table->text('tujuan1')->nullable()->change();
            $table->text('tujuan2')->nullable()->change();
            $table->text('tujuan3')->nullable()->change();
            $table->text('tujuan4')->nullable()->change();
            $table->text('tujuan5')->nullable()->change();
            $table->text('strategi1')->nullable()->change();
            $table->text('strategi2')->nullable()->change();
            $table->text('strategi3')->nullable()->change();
            $table->text('strategi4')->nullable()->change();
            $table->text('strategi5')->nullable()->change();
            
            if (!Schema::hasColumn('visis', 'doc_penyusunan')) {
                $table->string('doc_penyusunan')->nullable();
            } else {
                $table->string('doc_penyusunan')->nullable()->change();
            }
            
            if (!Schema::hasColumn('visis', 'doc_pengesahan')) {
                $table->string('doc_pengesahan')->nullable();
            } else {
                $table->string('doc_pengesahan')->nullable()->change();
            }
            
            if (!Schema::hasColumn('visis', 'doc_sosialisasi')) {
                $table->string('doc_sosialisasi')->nullable();
            } else {
                $table->string('doc_sosialisasi')->nullable()->change();
            }
            
            if (!Schema::hasColumn('visis', 'doc_hasil_survey')) {
                $table->string('doc_hasil_survey')->nullable();
            } else {
                $table->string('doc_hasil_survey')->nullable()->change();
            }

            if (Schema::hasColumn('visis', 'id_prodi')) {
                $table->dropColumn('id_prodi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visis', function (Blueprint $table) {
            $table->dropColumn(['visible_id', 'visible_type']);
            if (!Schema::hasColumn('visis', 'id_prodi')) {
                $table->uuid('id_prodi')->after('id')->nullable();
            }
        });
    }
};
