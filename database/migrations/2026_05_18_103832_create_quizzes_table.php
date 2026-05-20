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
            $table->uuid('rps_assessment_id')->nullable(); // Hubungan ke bobot OBE jika kuis ini dinilai
            $table->string('title');
            $table->integer('duration'); // Durasi dalam menit
            $table->timestamps();

            $table->foreign('rps_assessment_id')->references('id')->on('rps_assessments')->onDelete('cascade');
        });

        // 2. Bank Soal Pilihan Ganda
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quiz_id');
            $table->text('question_text');
            $table->json('options'); // Menyimpan pilihan A, B, C, D, E dalam format JSON
            $table->string('correct_option'); // Kunci jawaban (misal: "A")
            $table->timestamps();

            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        });

        // 3. Jawaban & Nilai Otomatis Mahasiswa
        Schema::create('student_quiz_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quiz_id');
            $table->uuid('user_id'); // ID Mahasiswa
            $table->integer('score')->nullable(); // Nilai otomatis (0-100) setelah disubmit
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
            $table->string('selected_option'); // Pilihan yang dipilih (misal: "A")
            $table->boolean('is_correct'); // Apakah jawaban benar
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
