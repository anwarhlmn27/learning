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
        if (Schema::hasTable('assignment_submissions')) {
            Schema::table('assignment_submissions', function (Blueprint $table) {
                if (!Schema::hasColumn('assignment_submissions', 'score')) {
                    $table->decimal('score', 5, 2)->nullable()->after('text_answer');
                }
                if (!Schema::hasColumn('assignment_submissions', 'submitted_at')) {
                    $table->dateTime('submitted_at')->nullable()->after('status');
                }
            });
        }

        if (Schema::hasTable('student_quiz_attempts')) {
            Schema::table('student_quiz_attempts', function (Blueprint $table) {
                if (!Schema::hasColumn('student_quiz_attempts', 'started_at')) {
                    $table->dateTime('started_at')->nullable()->after('score');
                }
                if (!Schema::hasColumn('student_quiz_attempts', 'submitted_at')) {
                    $table->dateTime('submitted_at')->nullable()->after('started_at');
                }
                if (!Schema::hasColumn('student_quiz_attempts', 'duration_in_seconds')) {
                    $table->integer('duration_in_seconds')->nullable()->after('submitted_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safety migration, no drop required
    }
};
