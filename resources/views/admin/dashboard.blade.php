@extends('layouts.admin')

@section('title', 'Situational Awareness')

@section('content')
<div class="grid">
    <div class="card stat-card">
        <h3>Total Signals (This Week)</h3>
        <p>{{ $stats['total'] }}</p>
    </div>
    <div class="card stat-card">
        <h3>🚨 ACT (Critical)</h3>
        <p style="color: var(--danger)">{{ $stats['act'] }}</p>
    </div>
    <div class="card stat-card">
        <h3>🟠 WATCH (Priority)</h3>
        <p style="color: var(--warning)">{{ $stats['watch'] }}</p>
    </div>
</div>

<div class="grid">
    <div class="card">
        <h3>AI Budget Utilization</h3>
        <div style="font-size: 2rem; font-weight: 700; margin: 1rem 0;">
            ${{ number_format($budget['used'], 2) }} / <span style="color: var(--text-muted)">${{ number_format($budget['limit'], 2) }}</span>
        </div>
        <div style="background: #e2e8f0; height: 10px; border-radius: 5px; overflow: hidden; margin-bottom: 1rem;">
            <div style="background: var(--accent-color); width: {{ min(100, $budget['percent']) }}%; height: 100%;"></div>
        </div>
        <p style="color: var(--text-muted); font-size: 0.875rem;">
            {{ round($budget['percent'], 1) }}% of monthly budget consumed.
        </p>
    </div>

    <div class="card">
        <h3>System Health</h3>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="margin-bottom: 1rem; display: flex; justify-content: space-between;">
                <span>Latest Content Fetch</span>
                <strong>{{ $lastRuns['fetch']?->completed_at?->diffForHumans() ?? 'Never' }}</strong>
            </li>
            <li style="margin-bottom: 1rem; display: flex; justify-content: space-between;">
                <span>Last Intelligence Scan</span>
                <strong>{{ $lastRuns['scoring']?->completed_at?->diffForHumans() ?? 'Never' }}</strong>
            </li>
            <li style="display: flex; justify-content: space-between;">
                <span>Last Briefing Sent</span>
                <strong>{{ $lastRuns['delivery']?->completed_at?->diffForHumans() ?? 'Never' }}</strong>
            </li>
        </ul>
    </div>
</div>

<div class="card">
    <header style="margin-bottom: 1rem;">
        <h3>Pipeline Status</h3>
    </header>
    <p style="color: var(--text-muted)">PIMS is currently running autonomously. Your next digest is scheduled for 06:00 AM.</p>
</div>
@endsection
