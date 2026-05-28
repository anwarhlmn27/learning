@extends('layouts.lms')
@section('content')
<div class="container" style="max-width: 800px; margin: 0 auto; padding: 2rem 1rem;">
    <h2>Grade Essay: {{ $attempt->user->name }}</h2>
    <p>Kuis: {{ $quiz->title }}</p>

    <form action="{{ route('quizzes.save_grade', [$quiz, $attempt]) }}" method="POST">
        @csrf
        @foreach($attempt->answers as $index => $ans)
            @if($ans->question->type == 'essay')
                <div style="margin-bottom: 2rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
                    <p><strong>Soal Essay:</strong> {{ $ans->question->question_text }}</p>
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #e2e8f0;">
                        <strong>Jawaban Mahasiswa:</strong><br>
                        {{ $ans->answer_text ?? '(Kosong)' }}
                    </div>
                    <div>
                        <label>Beri Nilai (Max: {{ $ans->question->points }})</label><br>
                        <input type="number" name="score_{{ $ans->id }}" max="{{ $ans->question->points }}" min="0" value="{{ $ans->score ?? 0 }}" required style="padding: 0.5rem; width: 150px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                </div>
            @endif
        @endforeach

        <button type="submit" class="btn" style="width: 100%; padding: 1rem; font-size: 1.1rem;">Simpan Nilai & Kalkulasi Total</button>
    </form>
</div>
@endsection
