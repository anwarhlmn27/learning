@extends('layouts.lms')

@section('header_title', $assignment->title)

@section('content')
<div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1.5rem;">
    <a href="{{ route('classes.show', $assignment->classRoom) }}" class="btn btn-outline" style="padding: 0.5rem 1rem; border-radius: 9999px;">
        ← Kembali ke Kelas
    </a>
    <div>
        <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">{{ $assignment->title }}</h2>
        <span style="font-size: 0.8rem; color: var(--text-muted);">{{ optional($assignment->classRoom)->nama_kelas }} &mdash; {{ optional($assignment->classRoom->subject)->nama_subject }}</span>
    </div>
</div>

@if(session('error'))
    <div style="background-color: #fef2f2; color: #991b1b; padding: 1rem; border: 1px solid #fecaca; border-radius: var(--radius); margin-bottom: 1.5rem;">{{ session('error') }}</div>
@endif

@if(session('success'))
    <div style="background-color: #f0fdf4; color: #166534; padding: 1rem; border: 1px solid #bbf7d0; border-radius: var(--radius); margin-bottom: 1.5rem;">{{ session('success') }}</div>
@endif

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
    <!-- Assignment Details -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body">
            <h3 style="margin-top: 0;">Instruksi Tugas</h3>
            <div style="background: #f8fafc; padding: 1.5rem; border-radius: 4px; border-left: 4px solid var(--primary); font-size: 0.95rem; line-height: 1.6;">
                {!! nl2br(e($assignment->instruction)) !!}
            </div>

            <div style="margin-top: 1.5rem; display: flex; gap: 1rem; font-size: 0.875rem;">
                <div>
                    <strong style="color: var(--text-muted); display: block;">Mata Kuliah:</strong>
                    <span>{{ optional($assignment->classRoom->subject)->nama_subject ?? '-' }}</span>
                </div>
                <div>
                    <strong style="color: var(--text-muted); display: block;">Dosen:</strong>
                    <span>{{ optional($assignment->classRoom->dosens()->first())->name ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Submission Panel -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header" style="background: white; border-bottom: 1px solid var(--border);">
            <h3 style="margin: 0; font-size: 1.1rem;">Your Work</h3>
        </div>
        <div class="card-body">
            <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; font-size: 0.875rem;">
                <span style="color: var(--text-muted);">Status:</span>
                @if($submission)
                    <strong style="color: {{ $submission->status == 'Graded' ? '#166534' : '#3730a3' }};">{{ $submission->status }}</strong>
                @else
                    <strong style="color: #6b7280;">Assigned</strong>
                @endif
            </div>
            <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; font-size: 0.875rem;">
                <span style="color: var(--text-muted);">Due:</span>
                <strong style="color: {{ now()->gt($assignment->deadline) && !$submission ? '#dc2626' : 'var(--text-main)' }};">
                    {{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y, H:i') }}
                </strong>
            </div>

            @if($submission && $submission->status == 'Graded')
                <div style="background: #eff6ff; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; border: 1px solid #bfdbfe;">
                    <h4 style="margin: 0 0 0.5rem 0; color: #1e40af; font-size: 0.875rem;">Feedback Dosen:</h4>
                    <p style="margin: 0; font-size: 0.875rem;">{{ $submission->feedback ?? 'Tidak ada komentar.' }}</p>
                </div>
            @endif

            @if(!$submission || ($submission && $submission->status != 'Graded'))
                <form action="{{ route('assignments.submit', $assignment) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Teks Jawaban (Opsional)</label>
                        <textarea name="text_answer" rows="3" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">{{ optional($submission)->text_answer }}</textarea>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Unggah File (Opsional)</label>
                        @if(optional($submission)->file_path)
                            <div style="margin-bottom: 0.5rem; font-size: 0.875rem;">
                                File terkirim: <a href="{{ Storage::url($submission->file_path) }}" target="_blank" style="color: var(--primary);">Lihat File</a>
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">Unggah file baru untuk mengganti file lama.</span>
                        @endif
                        <input type="file" name="file" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                    </div>
                    
                    <button type="submit" class="btn" style="width: 100%;">
                        {{ $submission ? 'Update Submission' : 'Submit Assignment' }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
