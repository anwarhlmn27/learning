@extends('layouts.lms')

@section('header_title', 'Grading - ' . $assignment->title)

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



<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div>
                <strong style="display: block; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Mata Kuliah</strong>
                <span style="font-size: 1rem; color: var(--text-main); font-weight: 600;">{{ optional($assignment->classRoom->subject)->nama_subject ?? '-' }}</span>
            </div>
            <div>
                <strong style="display: block; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Kelas</strong>
                <span style="font-size: 1rem; color: var(--text-main); font-weight: 600;">{{ optional($assignment->classRoom)->nama_kelas ?? '-' }}</span>
            </div>
            <div>
                <strong style="display: block; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Tenggat Waktu (Deadline)</strong>
                <span style="font-size: 1rem; color: #dc2626; font-weight: 600;">{{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y, H:i') }}</span>
            </div>
        </div>
        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
            <strong style="display: block; font-size: 0.875rem; margin-bottom: 0.5rem;">Instruksi Tugas:</strong>
            <div style="background: #f8fafc; padding: 1rem; border-radius: 4px; font-size: 0.875rem;">
                {!! nl2br(e($assignment->instruction)) !!}
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="background: white; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.25rem;">Daftar Pengumpulan & Penilaian</h3>
        <button type="button" id="btn-bulk-save" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem;">
            💾 Simpan Semua Nilai
        </button>
    </div>
    <div class="card-body" style="padding: 0;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--border); background: #f8fafc; text-align: left; width: 50px;">No</th>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--border); background: #f8fafc; text-align: left;">Mahasiswa</th>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--border); background: #f8fafc; text-align: left;">Status / File</th>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--border); background: #f8fafc; text-align: left; width: 300px;">Nilai & Feedback (OBE)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollments as $index => $enrollment)
                    @php
                        $submission = $assignment->submissions->where('student_id', $enrollment->student_id)->first();
                        $grade = $grades->get($enrollment->id);
                    @endphp
                <tr>
                    <td style="padding: 1rem; border-bottom: 1px solid var(--border); vertical-align: top;">{{ $index + 1 }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid var(--border); vertical-align: top;">
                        <strong>{{ optional($enrollment->student)->nama_student ?? '-' }}</strong><br>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">NIM: {{ optional($enrollment->student)->nim ?? '-' }}</span>
                    </td>
                    <td style="padding: 1rem; border-bottom: 1px solid var(--border); vertical-align: top;">
                        @if($submission)
                            <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 9999px; background: {{ $submission->status == 'Late' ? '#fef08a' : '#dcfce7' }}; color: {{ $submission->status == 'Late' ? '#a16207' : '#166534' }};">
                                {{ $submission->status }}
                            </span>
                            <div style="margin-top: 0.5rem; font-size: 0.875rem;">
                                @if($submission->file_path)
                                    <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" style="color: var(--primary); text-decoration: none;">📄 View File</a><br>
                                @endif
                                @if($submission->text_answer)
                                    <button class="btn btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; margin-top: 0.5rem;" onclick="alert(`{{ htmlspecialchars($submission->text_answer) }}`)">Lihat Teks</button>
                                @endif
                            </div>
                        @else
                            <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 9999px; background: #fee2e2; color: #991b1b;">
                                Belum Mengumpulkan
                            </span>
                        @endif
                    </td>
                    <td style="padding: 1rem; border-bottom: 1px solid var(--border); vertical-align: top; background: #f8fafc;">
                        @if(optional($enrollment->student)->is_frozen)
                            <div style="padding: 1rem; background: #fee2e2; color: #991b1b; border-radius: 4px; font-size: 0.85rem; text-align: center; border: 1px solid #f87171;">
                                <strong>Belum Eligible</strong><br>
                                Mahasiswa harus menyelesaikan administrasi ke Bagian Finance.
                            </div>
                        @else
                            <form action="{{ route('assignments.grade', [$assignment, $enrollment]) }}" method="POST" class="grade-form" data-enrollment-id="{{ $enrollment->id }}">
                                @csrf
                                <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem;">
                                    <input type="number" name="score" value="{{ optional($grade)->score ?? '' }}" placeholder="Skor (0-100) *" required min="0" max="100" style="width: 100px; padding: 0.25rem; border: 1px solid var(--border); border-radius: 4px;">
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">/ 100</span>
                                </div>
                                <textarea name="feedback" placeholder="Feedback / Komentar" rows="2" style="width: 100%; padding: 0.25rem; border: 1px solid var(--border); border-radius: 4px; margin-bottom: 0.5rem; font-size: 0.875rem;">{{ optional($submission)->feedback ?? '' }}</textarea>
                                <button type="submit" class="btn" style="width: 100%; padding: 0.25rem; font-size: 0.875rem;">Simpan Nilai</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnBulkSave = document.getElementById('btn-bulk-save');
    if (btnBulkSave) {
        btnBulkSave.addEventListener('click', function () {
            // Create a dynamic form to submit bulk grades
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('assignments.bulk_grade', $assignment) }}";

            // Add CSRF Token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = "{{ csrf_token() }}";
            form.appendChild(csrfInput);

            // Find all individual grade forms
            const gradeForms = document.querySelectorAll('.grade-form');
            let hasData = false;

            gradeForms.forEach((individualForm) => {
                const scoreInput = individualForm.querySelector('input[name="score"]');
                const feedbackTextarea = individualForm.querySelector('textarea[name="feedback"]');
                const enrollmentId = individualForm.dataset.enrollmentId;

                // We only include rows that have a score filled in
                if (scoreInput && scoreInput.value !== '') {
                    hasData = true;

                    // Add score input
                    const scoreHidden = document.createElement('input');
                    scoreHidden.type = 'hidden';
                    scoreHidden.name = `grades[${enrollmentId}][score]`;
                    scoreHidden.value = scoreInput.value;
                    form.appendChild(scoreHidden);

                    // Add feedback input
                    const feedbackHidden = document.createElement('input');
                    feedbackHidden.type = 'hidden';
                    feedbackHidden.name = `grades[${enrollmentId}][feedback]`;
                    feedbackHidden.value = feedbackTextarea ? feedbackTextarea.value : '';
                    form.appendChild(feedbackHidden);
                }
            });

            if (!hasData) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Harap isi setidaknya satu nilai mahasiswa sebelum menyimpan secara bulk.'
                });
                return;
            }

            Swal.fire({
                title: 'Simpan Semua Nilai?',
                text: "Semua nilai yang telah diisi akan disimpan secara massal.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    }
});
</script>
@endsection
