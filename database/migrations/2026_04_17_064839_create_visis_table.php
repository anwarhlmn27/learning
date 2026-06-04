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
            $table->uuid('visible_id');
            $table->string('visible_type');
            $table->text('visi');
            $table->string('doc_penyusunan')->nullable();
            $table->string('doc_pengesahan')->nullable();
            $table->string('doc_sosialisasi')->nullable();
            $table->string('doc_hasil_survey')->nullable();
            $table->timestamps();

            $table->index(['visible_id', 'visible_type']);
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
