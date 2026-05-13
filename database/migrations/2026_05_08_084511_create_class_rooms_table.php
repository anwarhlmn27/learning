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
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subject_id');
            $table->uuid('dosen_id');
            
            $table->string('nama_kelas'); // Contoh: Kelas A, Kelas B
            
            // Penambahan kolom periode
            $table->string('tahun_akademik'); // Contoh: 2024/2025, 2025/2026
            $table->enum('semester', ['Ganjil', 'Genap', 'Antara']); 
            
            $table->boolean('is_active')->default(true); // Untuk menandai semester yang sedang berjalan
            $table->timestamps();

            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('dosen_id')->references('id')->on('dosens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_rooms');
    }
};
