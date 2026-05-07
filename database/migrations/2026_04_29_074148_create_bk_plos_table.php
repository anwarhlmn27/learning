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
        Schema::create('bk_plos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_bk');
            $table->uuid('id_plo');
            $table->timestamps();

            $table->foreign('id_bk')->references('id')->on('bahan_kajians')->onDelete('cascade');
            $table->foreign('id_plo')->references('id')->on('plos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bk_plos');
    }
};
