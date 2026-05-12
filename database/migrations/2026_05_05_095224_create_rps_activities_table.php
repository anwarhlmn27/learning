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
        Schema::create('rps_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rps_session_id');
            $table->enum('type', ['Connect', 'Coach', 'Check', 'Wrap-up']);
            $table->integer('duration'); //Durasi Waktu per Aktivitas
            $table->text('content'); // Isi aktivitas
            $table->timestamps();

            $table->foreign('rps_session_id')->references('id')->on('rps_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rps_activities');
    }
};
