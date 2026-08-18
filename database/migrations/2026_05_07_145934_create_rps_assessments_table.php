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
        Schema::create('rps_assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rps_session_id'); // Relasi ke Pertemuan 1-14
            $table->uuid('clo_id')->nullable();        // Langsung tunjuk ke CLO mana
            // $table->uuid('assessment_type_id'); // Relasi ke master data assessment_types
            $table->text('assessment_type'); // Teks: Lisan, Tertulis, Kinerja, Portofolio, Proyek
            
            // Assessment per Session
            $table->text('assignment_activities')->nullable(); //Aktivitas Penugasan
            $table->text('assessment_scope')->nullable(); //Ruang Lingkup Tugas
            $table->text('how_worked')->nullable(); //Cara Pengerjaan Tugas (Individu, kelompok, dll)
            $table->integer('time_worked')->nullable(); //Waktu Pengerjaan Tugas
            $table->text('assessment_output')->nullable(); //Luaran Tugas   
            $table->integer('weight')->nullable();      // Bobot (misal: 5, 10, 20)
            $table->timestamps();

            $table->foreign('rps_session_id')->references('id')->on('rps_sessions')->onDelete('cascade');
            $table->foreign('clo_id')->references('id')->on('clos')->onDelete('restrict');
            // $table->foreign('assessment_type_id')->references('id')->on('assessment_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rps_assessments');
    }
};
