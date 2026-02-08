@extends('layouts.admin')

@section('title')
    Situational Awareness 
    <span class="badge bg-secondary ms-2 align-middle" style="font-size: 0.75rem;">
        {{ strtoupper($plan) }} PLAN
    </span>
@endsection

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase small fw-bold">Total Signals (Week)</h6>
                <p class="h2 fw-bold mb-0">{{ $stats['total'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 border-start border-danger border-4">
            <div class="card-body">
                <h6 class="card-title text-danger text-uppercase small fw-bold">🚨 ACT</h6>
                <p class="h2 fw-bold mb-0">{{ $stats['act'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 border-start border-warning border-4">
            <div class="card-body">
                <h6 class="card-title text-warning text-uppercase small fw-bold">🟠 WATCH</h6>
                <p class="h2 fw-bold mb-0">{{ $stats['watch'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase small fw-bold">Domains</h6>
                <p class="h2 fw-bold mb-0">
                    {{ $domainUsage['used'] }}<span class="h5 text-muted ms-1"> / {{ $domainUsage['limit'] }}</span>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">AI Budget Utilization</div>
            <div class="card-body">
                <div class="h2 fw-bold mb-2">
                    ${{ number_format($budget['used'], 2) }} <span class="h5 text-muted ms-1">/ ${{ number_format($budget['limit'], 2) }}</span>
                </div>
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $budget['percent']) }}%;" aria-valuenow="{{ $budget['percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="text-muted small mb-0">
                    {{ round($budget['percent'], 1) }}% of monthly cap consumed.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">Plan Boundaries</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Digest Frequency</span>
                        <span class="fw-bold">{{ in_array('daily', $limits['digest_frequencies']) ? 'Daily + Weekly' : 'Weekly Only' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Analysis Depth</span>
                        <span class="fw-bold">{{ ucfirst($limits['ai_depth']) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Keyword Tuning</span>
                        <span class="badge {{ $limits['features']['custom_keywords'] ? 'bg-success' : 'bg-secondary' }}">
                            {{ $limits['features']['custom_keywords'] ? 'Enabled' : 'Pro Only' }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">Pipeline Health</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Last Fetch</span>
                        <span class="fw-bold">{{ $lastRuns['fetch']?->completed_at?->diffForHumans() ?? 'Never' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Last Analysis</span>
                        <span class="fw-bold">{{ $lastRuns['scoring']?->completed_at?->diffForHumans() ?? 'Never' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Last Briefing</span>
                        <span class="fw-bold">{{ $lastRuns['delivery']?->completed_at?->diffForHumans() ?? 'Never' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 border-start border-primary border-4">
            <div class="card-header">System Status</div>
            <div class="card-body">
                <p class="text-muted">PIMS is currently running autonomously. <br>
                    Next weekly summary is scheduled for Monday at 07:00 AM.</p>
                @if($tenant->plan === 'student')
                    <div class="alert alert-info py-2 small mb-0">
                        <strong>Student Plan Active:</strong> AI depth optimized for high-level synthesis and cost-efficiency.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
