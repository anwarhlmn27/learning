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
        Schema::create('visis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_prodi');
            $table->string('visi');
            $table->string('misi');
            $table->string('tujuan1');
            $table->string('tujuan2');
            $table->string('tujuan3');
            $table->string('tujuan4');
            $table->string('tujuan5');
            $table->string('strategi1');
            $table->string('strategi2');
            $table->string('strategi3');
            $table->string('strategi4');
            $table->string('strategi5');
            $table->string('doc_penyusunan');
            $table->string('doc_pengesahan');
            $table->string('doc_sosialisasi');
            $table->string('doc_hasil_survey');
            $table->timestamps();

            
            $table->foreign('id_prodi')->references('id')->on('prodis')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visis');
    }
};
