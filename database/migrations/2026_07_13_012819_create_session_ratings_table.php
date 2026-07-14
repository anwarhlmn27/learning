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
        Schema::create('session_ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('class_room_id');
            $table->integer('session_number');
            $table->uuid('dosen_id');
            $table->uuid('student_id');
            $table->integer('rating');
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->foreign('class_room_id')->references('id')->on('class_rooms')->onDelete('cascade');
            $table->foreign('dosen_id')->references('id')->on('dosens')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            $table->unique(['student_id', 'class_room_id', 'session_number', 'dosen_id'], 'stud_sess_dos_uniq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_ratings');
    }
};
