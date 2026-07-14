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
        Schema::create('plo_terms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('plo_id')->constrained('plos')->onDelete('cascade');
            $table->text('description'); // Isian Terms & Conditions
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plo_terms');
    }
};
