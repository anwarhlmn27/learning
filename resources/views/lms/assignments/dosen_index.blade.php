@extends('layouts.lms')

@section('header_title', 'Grading Center')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Grading Center</h2>
</div>



<div class="card">
    <div class="card-body" style="padding: 0;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--border); background: #f8fafc; text-align: left;">Mata Kuliah / Kelas</th>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--border); background: #f8fafc; text-align: left;">Tugas</th>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--border); background: #f8fafc; text-align: left;">Deadline</th>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--border); background: #f8fafc; text-align: center;">Status</th>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--border); background: #f8fafc; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $assignment)
                <tr>
                    <td style="padding: 1rem; border-bottom: 1px solid var(--border);">
                        <strong>{{ optional(optional($assignment->classRoom)->subject)->nama_subject ?? 'Unknown' }}</strong><br>
                        <span style="font-size: 0.875rem; color: var(--text-muted);">{{ optional($assignment->classRoom)->nama_kelas ?? 'Unknown' }}</span>
                    </td>
                    <td style="padding: 1rem; border-bottom: 1px solid var(--border);">
                        <strong>{{ $assignment->title }}</strong>
                    </td>
                    <td style="padding: 1rem; border-bottom: 1px solid var(--border);">
                        {{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y, H:i') }}
                    </td>
                    <td style="padding: 1rem; border-bottom: 1px solid var(--border); text-align: center;">
                        <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 9999px; background: {{ $assignment->status == 'Published' ? '#dcfce7' : '#f3f4f6' }}; color: {{ $assignment->status == 'Published' ? '#166534' : '#4b5563' }};">
                            {{ $assignment->status }}
                        </span>
                    </td>
                    <td style="padding: 1rem; border-bottom: 1px solid var(--border); text-align: right;">
                        @if($assignment->status == 'Draft')
                            <form action="{{ route('assignments.publish', $assignment) }}" method="POST" style="display: inline-block; margin: 0;">
                                @csrf
                                <button type="submit" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Publish</button>
                            </form>
                        @endif
                        <a href="{{ route('assignments.show', $assignment) }}" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Manage / Grade</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-muted);">Belum ada tugas yang di-generate. Gunakan menu My Classes untuk melakukan generate.</td>
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
