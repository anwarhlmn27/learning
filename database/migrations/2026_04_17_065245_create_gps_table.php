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

        Schema::create('gps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_prodi');
            
            // Kode Profil (Contoh: GP-01)
            $table->string('kode_profil')->unique();
            
            // Nama Profil (Contoh: System Analyst)
            $table->string('nm_profil');
            
            // Deskripsi lengkap
            $table->text('deskripsi');
            
            // Bidang Kerja / Career Pathway
            $table->text('career_pathway');
            
            // Kompetensi Utama
            $table->text('kompetensi');
            
            // Sumber Acuan (Visi Misi, SKKNI, dll)
            $table->text('sumber_acuan');
            
            // Stakeholder Terkait
            $table->text('stakeholders');
            
            // Status Profil
            $table->enum('status', ['Draft', 'Aktif', 'Revisi', 'Tidak Aktif'])->default('Draft');
            
            $table->timestamps();

            // Relasi ke tabel prodi
            $table->foreign('id_prodi')->references('id')->on('prodis')->onDelete('cascade');
        });
        
        // 2. Create gp_attachments table for flexible document storage
        Schema::create('gp_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_prodi');
            $table->string('nm_dokumen'); // FGD Report, Alumni Survey, etc.
            $table->string('file_path');
            $table->timestamps();

            $table->foreign('id_prodi')->references('id')->on('prodis')->onDelete('cascade');
        });
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gp_attachments');
        Schema::dropIfExists('gps');
    }
};
