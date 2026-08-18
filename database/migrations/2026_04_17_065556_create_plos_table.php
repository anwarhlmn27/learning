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

        Schema::create('plos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_prodi');
            
            $table->string('kode_plo'); // Contoh: PLO-01
            $table->text('plo_title');
            $table->text('rumusan_plo'); // Rumusan kompetensi
            
            // Domain & Taksonomi
            $table->text('domain')->nullable();
            $table->enum('bloom_level',['C1','C2','C3','C4','C5','C6']); // C1-C6
            $table->string('kko'); // Kata Kerja Operasional
            
            // Indikator & Target
            $table->text('indikator_ketercapaian');
            $table->text('target_capaian')->nullable(); // Contoh: 75% mencapai level Good
            $table->enum('metode_pengukuran', ['Direct', 'Indirect', 'Both'])->default('Direct');
            
            $table->enum('status', ['Draft', 'Aktif', 'Revisi'])->default('Draft');
            $table->timestamps();

            $table->foreign('id_prodi')->references('id')->on('prodis')->onDelete('restrict');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plos');
    }
};
