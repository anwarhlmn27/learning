@extends('layouts.lms')

@section('header_title', 'Take Quiz')

@section('content')
<style>
    :root {
        --primary: #4f46e5;
        --radius-lg: 12px;
        --radius-md: 8px;
        --border-color: #e2e8f0;
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }

    .quiz-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .quiz-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 2rem;
        border-radius: var(--radius-lg);
        margin-bottom: 2rem;
        box-shadow: var(--shadow-md);
    }

    .question-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .option-label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        margin-bottom: 0.75rem;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .option-label:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .option-input:checked + .option-text {
        color: var(--primary);
        font-weight: 700;
    }

    .option-input:checked {
        border-color: var(--primary);
        background-color: var(--primary);
    }
</style>

<div class="quiz-container">
    <!-- Quiz Header Banner -->
    <div class="quiz-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span style="background: rgba(255,255,255,0.2); padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                    Kuis Sesi Kelas
                </span>
                <h1 style="margin: 0.5rem 0; font-size: 1.75rem; font-weight: 800;">{{ $quiz->title }}</h1>
                <p style="margin: 0; font-size: 1rem; opacity: 0.9;">{{ $quiz->description }}</p>
            </div>
            <div style="background: rgba(0,0,0,0.15); padding: 0.75rem 1.25rem; border-radius: var(--radius-md); text-align: center;">
                <span style="font-size: 0.75rem; display: block; text-transform: uppercase; font-weight: 700; opacity: 0.8;">Waktu Pengerjaan</span>
                <strong style="font-size: 1.5rem; font-weight: 800;">{{ $quiz->duration_minutes }} Mins</strong>
            </div>
        </div>
    </div>

    <!-- Take Quiz Form -->
    <form action="{{ route('classes.submit_quiz', [$class, $quiz]) }}" method="POST">
        @csrf

        @foreach($quiz->questions as $index => $question)
            <div class="question-card">
                <div style="display: flex; gap: 0.75rem; align-items: flex-start; margin-bottom: 1.5rem;">
                    <span style="background: #e0e7ff; color: var(--primary); width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem; flex-shrink: 0;">
                        {{ $index + 1 }}
                    </span>
                    <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #1e293b; line-height: 1.4;">
                        {{ $question->question_text }}
                    </h3>
                </div>

                <div>
                    @php
                        // Cast JSON options to PHP Array if it is a string
                        $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                    @endphp
                    @if(is_array($options))
                        @foreach($options as $opt)
                            <label class="option-label">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $opt }}" required class="option-input" style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary);">
                                <span class="option-text" style="color: #334155;">{{ $opt }}</span>
                            </label>
                        @endforeach
                    @else
                        <p style="color: red; font-style: italic;">Opsi pertanyaan tidak valid.</p>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Submit Panel -->
        <div style="display: flex; justify-content: space-between; align-items: center; background: white; border: 1px solid var(--border-color); padding: 1.5rem; border-radius: var(--radius-lg); margin-top: 2rem;">
            <p style="margin: 0; color: #64748b; font-size: 0.9rem; font-weight: 500;">
                <i>⚠️</i> Harap periksa kembali semua jawaban Anda sebelum menyerahkan kuis.
            </p>
            <button type="submit" class="btn" style="background-color: #f59e0b; color: white; padding: 0.75rem 1.75rem; font-weight: 700;" onclick="return confirm('Apakah Anda yakin ingin mengumpulkan kuis ini?')">
                Submit Quiz Attempt
            </button>
        </div>
    </form>
</div>
@endsection
