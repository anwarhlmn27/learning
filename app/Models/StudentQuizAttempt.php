<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class StudentQuizAttempt extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'is_submitted' => 'boolean',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function answers()
    {
        return $this->hasMany(StudentQuizAnswer::class, 'student_quiz_attempt_id');
    }

    /**
     * Perform auto-grading and sync to student_grades.
     */
    public function gradeAttempt()
    {
        $quiz = $this->quiz;
        if (!$quiz) {
            return;
        }

        $questions = $quiz->questions()->get()->keyBy('id');
        $answers = $this->answers()->get();

        $correctCount = 0;
        $totalQuestions = $questions->count();

        foreach ($answers as $answer) {
            $question = $questions->get($answer->quiz_question_id);
            if ($question) {
                // Check if chosen option is correct (case-insensitive comparison)
                $isCorrect = strtolower(trim($answer->selected_option)) === strtolower(trim($question->correct_option));
                
                // Save correctness state for this individual answer
                $answer->update([
                    'is_correct' => $isCorrect
                ]);

                if ($isCorrect) {
                    $correctCount++;
                }
            }
        }

        // Calculate score on a scale of 0-100
        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0.00;

        // Save total score and mark attempt as submitted
        $this->update([
            'score' => $score,
            'is_submitted' => true,
        ]);

        // Sync score directly to student_grades (OBE System) if quiz is graded
        if ($quiz->rps_assessment_id) {
            // 1. Find the student record associated with this user
            $student = Student::where('user_id', $this->user_id)->first();

            if ($student) {
                // 2. Find classrooms that have this quiz in their topics/timeline
                $classRoomIds = ClassTopic::where('content_id', $quiz->id)
                    ->where('type', 'quiz')
                    ->pluck('class_room_id');

                // 3. Find the student's enrollment in any of those classrooms
                $enrollment = Enrollment::where('student_id', $student->id)
                    ->whereIn('class_room_id', $classRoomIds)
                    ->first();

                // 4. Update or create the grade record in student_grades
                if ($enrollment) {
                    StudentGrade::updateOrCreate(
                        [
                            'enrollment_id' => $enrollment->id,
                            'rps_assessment_id' => $quiz->rps_assessment_id,
                        ],
                        [
                            'score' => $score,
                        ]
                    );
                }
            }
        }

        return $score;
    }
}
