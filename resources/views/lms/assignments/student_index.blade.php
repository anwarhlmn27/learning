@extends('layouts.lms')

@section('header_title', 'My To-Do List (Assignments)')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">To-Do List</h2>
</div>

@if(session('error'))
    <div style="background-color: #fef2f2; color: #991b1b; padding: 1rem; border: 1px solid #fecaca; border-radius: var(--radius); margin-bottom: 1.5rem;">{{ session('error') }}</div>
@endif

@if(session('success'))
    <div style="background-color: #f0fdf4; color: #166534; padding: 1rem; border: 1px solid #bbf7d0; border-radius: var(--radius); margin-bottom: 1.5rem;">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body" style="padding: 0;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--border); background: #f8fafc; text-align: left;">Mata Kuliah / Kelas</th>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--border); background: #f8fafc; text-align: left;">Tugas</th>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--border); background: #f8fafc; text-align: left;">Deadline</th>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--border); background: #f8fafc; text-align: center;">Status Pengumpulan</th>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--border); background: #f8fafc; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $assignment)
                    @php
                        $submission = $assignment->submissions->first();
                        $isLate = now()->gt($assignment->deadline);
                    @endphp
                <tr>
                    <td style="padding: 1rem; border-bottom: 1px solid var(--border);">
                        <strong>{{ optional(optional($assignment->classRoom)->subject)->nama_subject ?? 'Unknown' }}</strong><br>
                        <span style="font-size: 0.875rem; color: var(--text-muted);">{{ optional($assignment->classRoom)->nama_kelas ?? 'Unknown' }}</span>
                    </td>
                    <td style="padding: 1rem; border-bottom: 1px solid var(--border);">
                        <strong>{{ $assignment->title }}</strong>
                    </td>
                    <td style="padding: 1rem; border-bottom: 1px solid var(--border);">
                        <span style="color: {{ $isLate && !$submission ? '#dc2626' : 'var(--text-main)' }}; font-weight: {{ $isLate && !$submission ? '600' : 'normal' }};">
                            {{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y, H:i') }}
                        </span>
                    </td>
                    <td style="padding: 1rem; border-bottom: 1px solid var(--border); text-align: center;">
                        @if($submission)
                            <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 9999px; background: {{ $submission->status == 'Graded' ? '#dcfce7' : '#e0e7ff' }}; color: {{ $submission->status == 'Graded' ? '#166534' : '#3730a3' }};">
                                {{ $submission->status }}
                            </span>
                        @else
                            @if($isLate)
                                <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 9999px; background: #fee2e2; color: #991b1b;">Missing</span>
                            @else
                                <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 9999px; background: #f1f5f9; color: #475569;">To Do</span>
                            @endif
                        @endif
                    </td>
                    <td style="padding: 1rem; border-bottom: 1px solid var(--border); text-align: right;">
                        <a href="{{ route('assignments.show', $assignment) }}" class="btn {{ $submission ? 'btn-outline' : '' }}" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                            {{ $submission ? 'View Submission' : 'Submit Now' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                        <span style="font-size: 3rem;">🎉</span><br>
                        Hore! Tidak ada tugas yang menunggu.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 1.5rem;">
    {{ $assignments->links() }}
</div>
@endsection
