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
        // Schema::create('subjects', function (Blueprint $table) {
        //     $table->uuid('id')->primary();
        //     $table->string('kode_subject')->unique();
        //     $table->string('nama_subject');
        //     $table->integer('sks_t');
        //     $table->integer('sks_p');
        //     $table->integer('total_sks');
        //     $table->uuid('prerequisite_id')->nullable();
        //     $table->integer('semester');
        //     $table->enum('assesment_type', [
        //         'Project', 'Prototype', 'Coding', 'Design Project', 
        //         'Essay', 'Presentation', 'Case Study', 'SQL Lab',
        //         'Quiz','Writing','Analisys', 'Problem Solving','Reflection','Investigation Report','Business Pitch','Proposal','Performance','Report'
        //     ]);
        //     $table->timestamps();

        //     $table->foreign('prerequisite_id')->references('id')->on('subjects')->onDelete('set null');
        // });
        Schema::create('subjects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_prodi');
            $table->string('kode_subject')->unique(); // Contoh: ISAD101
            $table->string('nama_subject');
            $table->integer('semester');
            $table->integer('sks_t');
            $table->integer('sks_p');
            $table->integer('total_sks');
            
            // Tambahan baru
            $table->enum('jenis_subject', ['Wajib Prodi', 'Wajib Universitas', 'Pilihan']);
            $table->text('deskripsi'); // Isi dan tujuan MK
            
            $table->uuid('prerequisite_id')->nullable(); // Prasyarat
            $table->enum('status', ['Aktif', 'Revisi', 'Tidak Aktif'])->default('Aktif');
            
            $table->timestamps();

            $table->foreign('id_prodi')->references('id')->on('prodis')->onDelete('cascade');
            $table->foreign('prerequisite_id')->references('id')->on('subjects')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
