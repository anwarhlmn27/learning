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
        // 1. Master Kuis
        Schema::create('quizzes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('class_room_id')->nullable();
            $table->uuid('rps_assessment_id')->nullable(); // Hubungan ke bobot OBE jika kuis ini dinilai
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('duration'); // Durasi dalam menit
            $table->timestamps();

            $table->foreign('class_room_id')->references('id')->on('class_rooms')->onDelete('cascade');
            $table->foreign('rps_assessment_id')->references('id')->on('rps_assessments')->onDelete('cascade');
        });

        // 2. Bank Soal Pilihan Ganda & Essay
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quiz_id');
            $table->enum('type', ['multiple_choice', 'essay'])->default('multiple_choice');
            $table->text('question_text');
            $table->string('question_image')->nullable();
            $table->json('options')->nullable(); // Menyimpan pilihan dalam format JSON
            $table->string('correct_option')->nullable(); // Kunci jawaban index/huruf
            $table->integer('points')->default(10);
            $table->timestamps();

            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        });

        // 3. Jawaban & Nilai Otomatis Mahasiswa
        Schema::create('student_quiz_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quiz_id');
            $table->uuid('user_id'); // ID Mahasiswa
            $table->integer('score')->nullable(); // Nilai otomatis (0-100) setelah disubmit
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->integer('duration_in_seconds')->nullable();
            $table->boolean('is_submitted')->default(false);
            $table->timestamps();

            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 4. Detail Jawaban Mahasiswa per Soal
        Schema::create('student_quiz_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_quiz_attempt_id');
            $table->uuid('quiz_question_id');
            $table->text('answer_text')->nullable(); // Jawaban yang dipilih/diketik
            $table->boolean('is_correct')->nullable(); // Apakah jawaban benar
            $table->integer('score')->nullable();
            $table->timestamps();

            $table->foreign('student_quiz_attempt_id')->references('id')->on('student_quiz_attempts')->onDelete('cascade');
            $table->foreign('quiz_question_id')->references('id')->on('quiz_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_quiz_answers');
        Schema::dropIfExists('student_quiz_attempts');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
