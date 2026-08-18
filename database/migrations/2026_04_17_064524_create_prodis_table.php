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
        Schema::create('prodis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_fakultas');
            $table->integer('kode_prodi');
            $table->string('nama_prodi');
            $table->foreignUuid('kaprodi_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('short_name');
            $table->string('sign')->nullable();
            $table->timestamps();

            $table->foreign('id_fakultas')->references('id')->on('fakultas')->onDelete('restrict');
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prodis');
    }
};
