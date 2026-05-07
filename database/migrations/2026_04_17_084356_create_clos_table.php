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
        // Schema::create('clos', function (Blueprint $table) {
        //     $table->uuid('id')->primary();
        //     $table->uuid('id_subject');
        //     $table->string('clo');
        //     $table->string('deskripsi');
        //     $table->timestamps();
        // });
        Schema::create('clos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subject_id'); // Relasi ke Mata Kuliah
            
            $table->string('kode_clo');      // Contoh: CLO1
            $table->text('deskripsi');       // Statement (Kata kerja aktif)
            $table->string('bloom_level');   // C1 - C6
            
            $table->timestamps();

            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clos');
    }
};
