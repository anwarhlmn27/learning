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
        Schema::create('class_lecturer_feedbacks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('class_room_id');
            $table->uuid('user_id');
            $table->uuid('dosen_id')->nullable();
            $table->integer('rating_lms')->default(5);
            $table->integer('rating_materi')->default(5);
            $table->text('kendala')->nullable();
            $table->text('saran')->nullable();
            $table->timestamps();

            $table->foreign('class_room_id')->references('id')->on('class_rooms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('dosen_id')->references('id')->on('dosens')->onDelete('set null');

            $table->unique(['class_room_id', 'user_id'], 'class_user_feedback_uniq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_lecturer_feedbacks');
    }
};
