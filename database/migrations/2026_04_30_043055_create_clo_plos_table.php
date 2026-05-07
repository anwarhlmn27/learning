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
        Schema::create('clo_plo', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clo_id');
            $table->uuid('plo_id');
            $table->timestamps();

            $table->foreign('clo_id')->references('id')->on('clos')->onDelete('cascade');
            $table->foreign('plo_id')->references('id')->on('plos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clo_plos');
    }
};
