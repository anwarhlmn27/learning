@extends('layouts.admin')

@section('title', 'Manage Curriculum Subjects')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Manage Subjects: {{ $kurikulum->nm_kurikulum }}</h1>
@endsection

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Add Subject Form -->
    <div class="card">
        <div class="card-header">
            <span>Add Subject</span>
        </div>
        <div class="card-body">
            <form action="{{ route('kurikulum.add-subject', $kurikulum->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">{{ __('Select Subject') }} </label>
                    <select name="id_subject" class="form-control" required>
                        <option value="" disabled selected>Select Subject</option>
                        @foreach($availableSubjects as $s)
                            <option value="{{ $s->id }}">[{{ $s->kode_subject }}] {{ $s->nama_subject }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Semester') }} </label>
                    <input type="number" name="semester" class="form-control" min="1" max="8" required value="1">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Add to Curriculum</button>
            </form>
        </div>
    </div>

    <!-- Current Subjects List -->
    <div class="card">
        <div class="card-header">
            <span>Curriculum Subjects</span>
            <a href="{{ route('kurikulum.index') }}" style="font-size: 0.875rem; color: var(--text-muted);">Back</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Subject Name') }}</th>
                        <th>{{ __('Semester') }}</th>
                        <th>{{ __('SKS') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $totalSks = 0;
                        $grouped = $kurikulum->subjects->groupBy('semester')->sortKeys();
                    @endphp
                    @forelse($grouped as $sem => $subs)
                        <tr style="background: #f9fafb;">
                            <td colspan="5" style="font-weight: 700; font-size: 0.75rem; color: var(--primary);">SEMESTER {{ $sem }}</td>
                        </tr>
                        @foreach($subs as $ks)
                            @php $totalSks += $ks->subject->total_sks; @endphp
                            <tr>
                                <td>{{ $ks->subject->kode_subject }}</td>
                                <td>{{ $ks->subject->nama_subject }}</td>
                                <td>{{ $ks->semester }}</td>
                                <td>{{ $ks->subject->total_sks }}</td>
                                <td>
                                    <form action="{{ route('kurikulum.remove-subject', $ks->id) }}" method="POST" onsubmit="return confirm('Remove this subject from curriculum?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted);">No subjects added yet.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($totalSks > 0)
                <tfoot>
                    <tr style="background: #f3f4f6; font-weight: 700;">
                        <td colspan="3" style="text-align: right;">Total SKS:</td>
                        <td colspan="2">{{ $totalSks }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
