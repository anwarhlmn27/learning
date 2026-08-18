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
        Schema::create('rps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subject_id'); // Relasi ke Mata Kuliah
            $table->uuid('kurikulum_id'); // Relasi ke Kurikulum
            $table->string('nomor_rps')->nullable(); // Contoh: RPS-INF-2024-001
            $table->date('tanggal_penyusunan')->nullable();
            $table->text('referensi')->nullable();
            $table->string('media_pembelajaran')->nullable();
            $table->string('pengembang_rps')->nullable();
            $table->string('dosen_pengampu')->nullable();
            $table->integer('versi')->default(1); // Untuk menangani perubahan konten
            $table->enum('status', ['Draft', 'Aktif', 'Arsip'])->default('Draft');
            $table->timestamps();

            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('restrict');
            $table->foreign('kurikulum_id')->references('id')->on('kurikulums')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('rps');
        Schema::enableForeignKeyConstraints();
    }
};
