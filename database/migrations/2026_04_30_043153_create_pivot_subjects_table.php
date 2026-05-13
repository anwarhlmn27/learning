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
        // Mapping Mata Kuliah ke Bahan Kajian
        Schema::create('subject_bk', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subject_id');
            $table->uuid('bk_id');
            $table->timestamps();
            
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('bk_id')->references('id')->on('bahan_kajians')->onDelete('cascade');
        });

        // Mapping Mata Kuliah ke PLO
        Schema::create('subject_plo', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subject_id');
            $table->uuid('plo_id');
            // Level Mapping (I=Intro, R=Reinforce, M=Mastery)
            // Menggunakan enum atau integer
            $table->enum('mapping_level', ['I', 'R', 'M'])->default('I');
            $table->timestamps();
            
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('plo_id')->references('id')->on('plos')->onDelete('cascade');
        });

        // Mapping Assessment ke CLO (Sesuai poin nomor 4 kamu)
        // Schema::create('assessment_clo', function (Blueprint $table) {
        //     $table->uuid('assessment_id');
        //     $table->uuid('clo_id');
        //     $table->foreign('assessment_id')->references('id')->on('subject_assessments')->onDelete('cascade');
        //     $table->foreign('clo_id')->references('id')->on('clos')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_plo');
        Schema::dropIfExists('subject_bk');
    }
};
