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
        Schema::create('plo_indicators', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('plo_id')->constrained('plos')->onDelete('cascade');
            $table->string('indicator_code'); // Contoh: PI-01
            $table->text('indicator_description'); // Deskripsi indikator
            // $table->string('measurement_method'); // Direct/Indirect
            // $table->integer('target_value'); // Contoh: 75
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plo_indicators');
    }
};
