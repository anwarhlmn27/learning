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
        Schema::create('visi_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_visi'); // Foreign key ke tabel visis
            $table->enum('type', ['misi', 'tujuan', 'strategi']);
            $table->integer('urutan'); // Untuk menentukan nomor urut (1, 2, 3...)
            $table->text('konten'); // Isi teksnya
            $table->timestamps();

            $table->foreign('id_visi')->references('id')->on('visis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visi_details');
    }
};
