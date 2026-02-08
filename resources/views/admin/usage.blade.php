@extends('layouts.admin')

@section('title', 'AI Usage & Cost Tracking')

@section('content')
<div class="grid">
    <div class="card stat-card">
        <h3>Monthly AI Spend</h3>
        <p>${{ number_format($stats['total_cost'], 2) }}</p>
    </div>
    <div class="card stat-card">
        <h3>Token Consumption</h3>
        <p>{{ number_format($stats['total_tokens']) }}</p>
    </div>
    <div class="card stat-card">
        <h3>Budget Remaining</h3>
        <p style="color: {{ $tenant->ai_monthly_budget_usd - $stats['total_cost'] < 1 ? 'var(--danger)' : 'var(--success)' }}">
            ${{ number_format(max(0, $tenant->ai_monthly_budget_usd - $stats['total_cost']), 2) }}
        </p>
    </div>
</div>

@if($stats['blocked_attempts'] > 0)
<div class="card" style="background-color: #fef2f2; border-color: #fee2e2; border-left: 4px solid var(--danger); margin-bottom: 2rem;">
    <h3 style="color: #991b1b; margin-top: 0;">Budget Breach Detected</h3>
    <p>AI processing has been **temporarily blocked** {{ $stats['blocked_attempts'] }} times this month due to your spending limit. Qualified signals are being deferred.</p>
</div>
@endif

<div class="card">
    <h3>Daily Analysis Trend</h3>
    <table style="margin-top: 1rem;">
        <thead>
            <tr>
                <th>Date</th>
                <th>AI Analyses Performed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailyTrend as $row)
            <tr>
                <td>{{ Carbon\Carbon::parse($row->date)->format('F j, Y') }}</td>
                <td><strong>{{ $row->count }}</strong> executions</td>
            </tr>
            @endforeach
            @if($dailyTrend->isEmpty())
            <tr>
                <td colspan="2" style="text-align: center; color: var(--text-muted); padding: 2rem;">No AI analysis data available for this period.</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection
