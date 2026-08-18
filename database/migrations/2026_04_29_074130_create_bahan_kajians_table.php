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
        Schema::create('bahan_kajians', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_prodi');
            $table->uuid('id_kategori_bk')->nullable(); // Foreign key ke tabel kategori_bk // Core Computing, Software Engineering, dll.
            
            // Kode & Nama
            $table->string('kode_bk')->unique(); // BK-01
            $table->string('nm_bahan_kajian');   // System Analysis and Design
            
            // Penjelasan
            $table->text('deskripsi'); 
            $table->text('sub_bk')->nullable(); // List sub-materi (bisa dipisah koma atau format text)
            
            // Klasifikasi
            $table->enum('tingkat_kedalaman', ['Introductory', 'Intermediate', 'Advanced'])->nullable();
            
            // Referensi & Status
            $table->text('sumber_acuan');
            $table->enum('status', ['Aktif', 'Revisi', 'Tidak Aktif'])->default('Aktif');
            
            $table->timestamps();

            $table->foreign('id_prodi')->references('id')->on('prodis')->onDelete('restrict');
            $table->foreign('id_kategori_bk')->references('id')->on('kategori_bk')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_kajians');
    }
};
