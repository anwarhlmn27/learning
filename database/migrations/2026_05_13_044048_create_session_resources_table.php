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
        Schema::create('session_resources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rps_session_id'); // Menempel ke pertemuan 1-14
            $table->string('nm_resource');  // Judul materi
            $table->enum('type', ['Modul', 'Materi Tambahan', 'Video']);
            $table->string('file_path');    // Lokasi file PDF di storage
            $table->foreign('rps_session_id')->references('id')->on('rps_sessions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_resources');
    }
};
