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
        // 1. Refactor gps table
        Schema::table('gps', function (Blueprint $table) {
            // Remove generic file column
            if (Schema::hasColumn('gps', 'file')) {
                $table->dropColumn('file');
            }
            // Ensure types are appropriate for long descriptions
            $table->text('deskripsi')->change();
            $table->text('kompetensi')->change();
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
        
        Schema::table('gps', function (Blueprint $table) {
            if (!Schema::hasColumn('gps', 'file')) {
                $table->string('file')->nullable();
            }
            $table->string('deskripsi')->change();
            $table->string('expertise')->change();
        });
    }
};
