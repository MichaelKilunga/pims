@extends('layouts.admin')

@section('title', 'AI Usage & Cost Tracking')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card h-100 border-start border-primary border-4">
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase small fw-bold">Monthly AI Spend</h6>
                <p class="h2 fw-bold mb-0">${{ number_format($stats['total_cost'], 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase small fw-bold">Token Consumption</h6>
                <p class="h2 fw-bold mb-0">{{ number_format($stats['total_tokens']) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-start border-4 {{ $tenant->ai_monthly_budget_usd - $stats['total_cost'] < 1 ? 'border-danger' : 'border-success' }}">
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase small fw-bold">Budget Remaining</h6>
                <p class="h2 fw-bold mb-0 text-{{ $tenant->ai_monthly_budget_usd - $stats['total_cost'] < 1 ? 'danger' : 'success' }}">
                    ${{ number_format(max(0, $tenant->ai_monthly_budget_usd - $stats['total_cost']), 2) }}
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 bg-light border-0">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="fw-bold mb-2">Budget Philosophy</h5>
                <p class="text-muted small mb-0">We use a "Safe Cap" model to ensure your AI costs are always predictable. Once your plan's monthly subsidy is consumed, deep analysis is paused until your budget resets.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0 border-start ps-4">
                <h6 class="text-uppercase small fw-bold text-muted mb-1">Next Reset</h6>
                <div class="h5 fw-bold text-primary mb-0">{{ now()->addMonth()->startOfMonth()->format('F j, Y') }}</div>
            </div>
        </div>
    </div>
</div>

@if($stats['blocked_attempts'] > 0)
    <div class="alert alert-danger border-start border-4 p-4 mb-4">
        <div class="d-flex align-items-center">
            <div class="flex-grow-1">
                <h5 class="alert-heading fw-bold">Resource Throttling Active</h5>
                <p class="mb-0">AI processing has been <strong>temporarily paused</strong> {{ $stats['blocked_attempts'] }} times this month. Qualified signals are being stored but not analyzed to maintain cost predictability.</p>
            </div>
            <div class="ms-3">
                <span class="badge bg-danger fs-6">{{ $stats['blocked_attempts'] }} BLOCKS</span>
            </div>
        </div>
    </div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daily Analysis Trend</span>
    </div>
    <div class="card-body">
        <table class="table table-hover datatable align-middle">
            <thead>
                <tr>
                    <th class="text-uppercase small fw-bold">Date</th>
                    <th class="text-uppercase small fw-bold">Analyses Performed</th>
                    <th class="text-uppercase small fw-bold">Cost (Est.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailyTrend as $row)
                <tr>
                    <td class="fw-medium">{{ Carbon\Carbon::parse($row->date)->format('F j, Y') }}</td>
                    <td>
                        <span class="badge bg-light text-dark fw-bold border">{{ $row->count }} executions</span>
                    </td>
                    <td class="text-muted small">
                        ${{ number_format($row->count * 0.05, 2) }} <!-- Calculation based on average cost per analysis -->
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($dailyTrend->isEmpty())
            <div class="text-center py-5">
                <div class="text-muted">No AI analysis data available for this period.</div>
            </div>
        @endif
    </div>
</div>
@endsection
