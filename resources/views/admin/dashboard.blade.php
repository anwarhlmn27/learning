@extends('layouts.admin')

@section('title', __('Dashboard'))

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.875rem; font-weight: 700; margin: 0 0 0.5rem 0;">{{ __('Welcome, Admin') }}</h1>
    <p style="color: var(--text-muted); margin: 0;">{{ __('Here is a summary of your Outcome-Based Education (OBE) system today.') }}</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="margin-bottom: 0; border-left: 4px solid #4f46e5;">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Fakultas') }}</div>
                    <div style="font-size: 1.75rem; font-weight: 700;">{{ $count['fakultas'] }}</div>
                </div>
                <div style="font-size: 2rem; opacity: 0.2;">🏫</div>
            </div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 0; border-left: 4px solid #10b981;">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Program Studi') }}</div>
                    <div style="font-size: 1.75rem; font-weight: 700;">{{ $count['prodi'] }}</div>
                </div>
                <div style="font-size: 2rem; opacity: 0.2;">📚</div>
            </div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 0; border-left: 4px solid #f59e0b;">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Kurikulum') }}</div>
                    <div style="font-size: 1.75rem; font-weight: 700;">{{ $count['kurikulum'] }}</div>
                </div>
                <div style="font-size: 2rem; opacity: 0.2;">📖</div>
            </div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 0; border-left: 4px solid #3b82f6;">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Mata Kuliah') }}</div>
                    <div style="font-size: 1.75rem; font-weight: 700;">{{ $count['subject'] }}</div>
                </div>
                <div style="font-size: 2rem; opacity: 0.2;">📘</div>
            </div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 0; border-left: 4px solid #ef4444;">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Users') }}</div>
                    <div style="font-size: 1.75rem; font-weight: 700;">{{ $count['user'] }}</div>
                </div>
                <div style="font-size: 2rem; opacity: 0.2;">👥</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ __('Recent Activity') }}
    </div>
    <div class="card-body">
        <p style="color: var(--text-muted); font-size: 0.875rem;">{{ __('No recent activity to display.') }}</p>
    </div>
</div>
@endsection
