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
        Schema::create('kurikulums', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nm_kurikulum');
            $table->uuid('id_prodi');
            $table->integer('tahun_akademik');
            // Document Attachments
            $table->string('berita_acara_fgd')->nullable();
            $table->string('daftar_hadir')->nullable();
            $table->string('notulensi_diskusi')->nullable();
            $table->string('laporan_penyusunan')->nullable();
            $table->string('laporan_sosialisasi')->nullable();
            $table->string('dokumentasi')->nullable();
            $table->timestamps();

            $table->foreign('id_prodi')->references('id')->on('prodis')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kurikulums');
    }
};
