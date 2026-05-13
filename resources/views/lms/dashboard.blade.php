@extends('layouts.lms')

@section('header_title', __('Dashboard') . ' - ' . ucfirst($data['view_type']))

@section('content')

@if(Auth::user()->hasRole(['admin', 'kaprodi', 'rektor', 'dekan']))
<div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 1rem 1.5rem; border-radius: 0.25rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
    <div>
        <h4 style="margin: 0 0 0.25rem 0; color: #1e3a8a;">Akses Manajemen OBE</h4>
        <p style="margin: 0; font-size: 0.875rem; color: #3b82f6;">Anda masuk sebagai level pimpinan/admin. Anda dapat mengakses pengaturan kurikulum dan pemetaan OBE.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn" style="background-color: #3b82f6;">Masuk ke Sistem OBE &rarr;</a>
</div>
@endif

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Stat Card 1 -->
    <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 4px solid var(--primary);">
        <h3 style="margin: 0 0 0.5rem 0; font-size: 0.875rem; color: var(--text-muted); text-transform: uppercase;">{{ __('Total Classes') }}</h3>
        <p style="margin: 0; font-size: 2rem; font-weight: 700; color: var(--text-main);">{{ $data['total_classes'] }}</p>
    </div>
    
    <!-- Stat Card 2 -->
    <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 4px solid #f59e0b;">
        <h3 style="margin: 0 0 0.5rem 0; font-size: 0.875rem; color: var(--text-muted); text-transform: uppercase;">{{ __('Pending Assignments') }}</h3>
        <p style="margin: 0; font-size: 2rem; font-weight: 700; color: var(--text-main);">{{ $data['total_assignments'] }}</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Left Column: Classes -->
    <div>
        <div class="card">
            <div class="card-header">
                {{ $data['view_type'] == 'dosen' ? __('Classes I Teach') : __('My Enrolled Classes') }}
            </div>
            <div class="card-body" style="padding: 0;">
                @if(count($data['classes']) > 0)
                    <div style="display: grid; grid-template-columns: 1fr; gap: 1px; background: var(--border);">
                        @foreach($data['classes'] as $class)
                            <div style="background: white; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h4 style="margin: 0 0 0.25rem 0; color: var(--primary);">{{ $class->nama_kelas ?? 'Class Name' }}</h4>
                                    <p style="margin: 0; font-size: 0.875rem; color: var(--text-muted);">
                                        {{ $class->subject->nama_subject ?? 'Subject Name' }} ({{ $class->subject->kode_subject ?? '' }})<br>
                                        Semester: {{ $class->semester ?? '-' }} {{ $class->tahun_akademik ?? '-' }}
                                    </p>
                                </div>
                                <div>
                                    <button class="btn">Enter Course</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
                        <p><i>📚</i></p>
                        <p>{{ __('No active classes found.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Assignments -->
    <div>
        <div class="card">
            <div class="card-header" style="background-color: #fffbeb;">
                {{ __('Upcoming Assignments') }}
            </div>
            <div class="card-body">
                @if(count($data['assignments']) > 0)
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        @foreach($data['assignments'] as $assignment)
                            <div style="border-left: 3px solid #f59e0b; padding-left: 1rem;">
                                <h4 style="margin: 0 0 0.25rem 0; font-size: 0.875rem;">{{ $assignment->title }}</h4>
                                <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted);">
                                    Due: {{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y, H:i') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; color: var(--text-muted); padding: 1rem 0;">
                        <p>{{ __('No upcoming assignments.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
