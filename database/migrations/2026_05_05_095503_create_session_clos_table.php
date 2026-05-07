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
        Schema::create('session_clo', function (Blueprint $table) {
            $table->uuid('rps_session_id');
            $table->uuid('clo_id');
            $table->foreign('rps_session_id')->references('id')->on('rps_sessions')->onDelete('cascade');
            $table->foreign('clo_id')->references('id')->on('clos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_clos');
    }
};
