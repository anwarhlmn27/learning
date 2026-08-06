<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\StudentQuizAttempt;
use App\Models\StudentQuizAnswer;
use App\Models\ClassRoom;
use App\Models\StudentGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    // Teacher: Lihat detail quiz & kelola pertanyaan
    public function show(Quiz $quiz)
    {
        $quiz->load(['questions', 'rpsAssessment', 'attempts.user.student']);
        return view('lms.quizzes.show', compact('quiz'));
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $request->validate([
            'type' => 'required|in:multiple_choice,essay',
            'question_text' => 'required|string',
            'points' => 'required|integer|min:1',
            'question_image' => 'nullable|image|max:2048',
        ]);

        $questionImage = null;
        if ($request->hasFile('question_image')) {
            $questionImage = $request->file('question_image')->store('quiz_images', 'public');
        }

        if ($request->type == 'multiple_choice') {
            $optionsData = [];
            $validOptionsCount = 0;
            
            // Pastikan options ada (mungkin form beda / terpotong)
            $rawOptions = $request->options ?? [];
            
            foreach ($rawOptions as $index => $opt) {
                $hasText = !empty($opt['text']);
                $hasImage = $request->hasFile("options.{$index}.image");
                
                if ($hasText || $hasImage) {
                    $optImage = null;
                    if ($hasImage) {
                        $optImage = $request->file("options.{$index}.image")->store('quiz_images', 'public');
                    }
                    
                    $optionsData[$index] = [
                        'text' => $opt['text'] ?? '',
                        'image' => $optImage
                    ];
                    $validOptionsCount++;
                }
            }

            if ($validOptionsCount < 2) {
                return back()->with('error', 'Pilihan ganda harus memiliki minimal 2 opsi (teks atau gambar).');
            }
            
            if (!isset($optionsData[$request->correct_option])) {
                return back()->with('error', 'Opsi yang dipilih sebagai jawaban benar tidak valid atau kosong.');
            }
            
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'type' => 'multiple_choice',
                'question_text' => $request->question_text,
                'question_image' => $questionImage,
                'options' => json_encode($optionsData),
                'correct_option' => (string) $request->correct_option,
                'points' => $request->points,
            ]);
        } else {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'type' => 'essay',
                'question_text' => $request->question_text,
                'question_image' => $questionImage,
                'points' => $request->points,
            ]);
        }

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    // Teacher: Hapus soal
    public function destroyQuestion(QuizQuestion $question)
    {
        $question->delete();
        return back()->with('success', 'Soal berhasil dihapus.');
    }

    // Student: Halaman ambil kuis
    public function take(ClassRoom $class, Quiz $quiz)
    {
        $user = Auth::user();
        
        // Cek apakah sudah pernah submit
        $attempt = StudentQuizAttempt::firstOrCreate(
            ['quiz_id' => $quiz->id, 'user_id' => $user->id],
            ['is_submitted' => false, 'score' => null, 'started_at' => now()]
        );

        if (!$attempt->started_at) {
            $attempt->update(['started_at' => now()]);
        }

        if ($attempt->is_submitted) {
            return redirect()->route('classes.show', $class)->with('error', 'Anda sudah mengerjakan kuis ini.');
        }

        $quiz->load('questions');
        return view('lms.quizzes.take', compact('class', 'quiz'));
    }

    // Student: Submit kuis
    public function submit(Request $request, ClassRoom $class, Quiz $quiz)
    {
        $user = Auth::user();
        $quiz->load('questions');

        DB::beginTransaction();
        try {
            $now = now();
            $attempt = StudentQuizAttempt::firstOrCreate(
                ['quiz_id' => $quiz->id, 'user_id' => $user->id],
                ['is_submitted' => false, 'score' => null, 'started_at' => $now]
            );

            if ($attempt->is_submitted) {
                DB::rollBack();
                return redirect()->route('classes.show', $class)->with('error', 'Kuis sudah dikumpulkan sebelumnya.');
            }

            $startedAt = $attempt->started_at ?? $now;
            $durationInSeconds = (int) $startedAt->diffInSeconds($now);

            $totalScore = 0;
            $hasEssay = false;

            foreach ($quiz->questions as $q) {
                $answerText = $request->input('q_' . $q->id);
                $isCorrect = null;
                $score = null;

                if ($q->type == 'multiple_choice') {
                    $isCorrect = ($answerText === $q->correct_option);
                    $score = $isCorrect ? $q->points : 0;
                    $totalScore += $score;
                } else {
                    $hasEssay = true;
                }

                StudentQuizAnswer::updateOrCreate(
                    ['student_quiz_attempt_id' => $attempt->id, 'quiz_question_id' => $q->id],
                    [
                        'answer_text' => $answerText,
                        'is_correct' => $isCorrect,
                        'score' => $score
                    ]
                );
            }

            $updateData = [
                'is_submitted' => true,
                'score'        => $hasEssay ? null : $totalScore,
            ];
            if (\Illuminate\Support\Facades\Schema::hasColumn('student_quiz_attempts', 'submitted_at')) {
                $updateData['submitted_at'] = $now;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('student_quiz_attempts', 'duration_in_seconds')) {
                $updateData['duration_in_seconds'] = $durationInSeconds;
            }
            $attempt->update($updateData);

            // Jika tidak ada essay, bisa langsung update OBE/Grade
            if (!$hasEssay && $quiz->rps_assessment_id) {
                $this->updateStudentGrade($quiz, $attempt, $user);
            }

            DB::commit();
            return redirect()->route('classes.show', $class)->with('success', 'Kuis berhasil disubmit!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal submit kuis: ' . $e->getMessage());
        }
    }

    // Teacher: Grading essay form
    public function gradeForm(Quiz $quiz, StudentQuizAttempt $attempt)
    {
        $attempt->load(['answers.question', 'user.student']);
        return view('lms.quizzes.grade', compact('quiz', 'attempt'));
    }

    // Teacher: Simpan nilai essay
    public function saveGrade(Request $request, Quiz $quiz, StudentQuizAttempt $attempt)
    {
        $attempt->load('answers.question');
        $totalScore = 0;

        foreach ($attempt->answers as $ans) {
            if ($ans->question->type == 'essay') {
                $score = $request->input('score_' . $ans->id);
                $ans->update([
                    'score' => $score,
                    'is_correct' => $score > 0
                ]);
                $totalScore += $score;
            } else {
                $totalScore += $ans->score;
            }
        }

        $attempt->update(['score' => $totalScore]);

        if ($quiz->rps_assessment_id) {
            $this->updateStudentGrade($quiz, $attempt, $attempt->user);
        }

        return redirect()->route('quizzes.show', $quiz)->with('success', 'Nilai essay berhasil disimpan dan masuk ke capaian pembelajaran otomatis.');
    }

    // Helper: Update Capaian Pembelajaran (StudentGrade)
    private function updateStudentGrade(Quiz $quiz, StudentQuizAttempt $attempt, $user)
    {
        if ($user->student) {
            $totalMaxPoints = $quiz->questions->sum('points');
            $finalScore100 = $totalMaxPoints > 0 ? ($attempt->score / $totalMaxPoints) * 100 : 0;
            
            $enrollment = \App\Models\Enrollment::where('student_id', $user->student->id)->where('class_room_id', $quiz->class_room_id)->first();
            if ($enrollment) {
                StudentGrade::updateOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'rps_assessment_id' => $quiz->rps_assessment_id
                    ],
                    ['score' => $finalScore100]
                );
            }
        }
    }
}
