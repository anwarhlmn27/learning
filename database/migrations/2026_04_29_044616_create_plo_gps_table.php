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
        Schema::create('plo_gps', function (Blueprint $table) {
            $table->uuid('id')->primary();
    $table->uuid('id_plo');
    $table->uuid('id_gp');
    $table->timestamps();

    $table->foreign('id_plo')->references('id')->on('plos')->onDelete('cascade');
    $table->foreign('id_gp')->references('id')->on('gps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plo_gps');
    }
};
