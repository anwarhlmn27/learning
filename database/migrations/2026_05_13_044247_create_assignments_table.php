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
        Schema::create('assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('class_room_id');
            $table->uuid('rps_assessment_id'); // PENTING: Menghubungkan tugas ke Bobot & CLO di RPS
            $table->string('title');
            $table->text('instruction');
            $table->dateTime('deadline');

            $table->foreign('class_room_id')->references('id')->on('class_rooms')->onDelete('cascade');
            $table->foreign('rps_assessment_id')->references('id')->on('rps_assessments')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
