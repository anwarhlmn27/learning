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
        // Tabel Utama Kelas
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subject_id');
            $table->string('nama_kelas');
            $table->string('tahun_akademik');
            $table->enum('semester', ['Ganjil', 'Genap', 'Antara']);
            $table->enum('status', ['active', 'archived', 'deleted'])->default('active');
            $table->timestamps();
            
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('restrict');
        });

        // Tabel Pivot Peserta Kelas (Dosen, Mahasiswa, BAAK)
        Schema::create('class_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('class_room_id');
            $table->uuid('user_id'); // Merujuk ke tabel user kamu
            $table->timestamps();

            $table->foreign('class_room_id')->references('id')->on('class_rooms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_users');
        Schema::dropIfExists('class_rooms');
    }
};
