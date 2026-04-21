@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.875rem; font-weight: 700; margin: 0 0 0.5rem 0;">Welcome, Admin</h1>
    <p style="color: var(--text-muted); margin: 0;">Here is a summary of your Outcome-Based Education (OBE) system today.</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body">
            <div style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Total Users</div>
            <div style="font-size: 1.5rem; font-weight: 700;">1,284</div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body">
            <div style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Active Courses</div>
            <div style="font-size: 1.5rem; font-weight: 700;">42</div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body">
            <div style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Learning Outcomes</div>
            <div style="font-size: 1.5rem; font-weight: 700;">85%</div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body">
            <div style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">New Notifications</div>
            <div style="font-size: 1.5rem; font-weight: 700;">12</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Recent Activity
    </div>
    <div class="card-body">
        <p style="color: var(--text-muted); font-size: 0.875rem;">No recent activity to display.</p>
    </div>
</div>
@endsection
