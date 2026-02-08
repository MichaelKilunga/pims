@extends('layouts.admin')

@section('title', 'Tenant Settings')

@section('content')
@php
    $plan = $tenant->plan ?? 'free';
    $limits = config("plans.{$plan}");
    $isProEligible = $limits['features']['daily_digest'];
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Digest & Delivery</span>
                    @if(!$isProEligible)
                        <span class="badge bg-warning text-dark small">Limited by Student Plan</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Delivery Cadence</label>
                        <select name="digest_frequency" {{ $isProEligible ? '' : 'disabled' }} class="form-select">
                            <option value="weekly" selected>Weekly Summary Only</option>
                            @if($isProEligible)
                                <option value="daily" {{ data_get($tenant->settings, 'digest_frequency') == 'daily' ? 'selected' : '' }}>Daily Briefing Only</option>
                                <option value="both" {{ data_get($tenant->settings, 'digest_frequency') == 'both' ? 'selected' : '' }}>Both Daily & Weekly</option>
                            @endif
                        </select>
                        @if(!$isProEligible)
                            <div class="form-text text-primary">Frequency locked to Weekly. Upgrade to Pro for daily intelligence.</div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Relevance Threshold</label>
                        <div class="input-group" style="width: 150px;">
                            <input type="number" name="relevance_threshold" value="{{ data_get($tenant->settings, 'relevance_threshold', 40) }}" class="form-control">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text">Only signals scoring above this value will be sent for AI analysis.</div>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="delivery_enabled" id="delivery_enabled" {{ data_get($tenant->settings, 'delivery_enabled', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="delivery_enabled">Enable Email Intelligence Reports</label>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Intelligence Domain Subscriptions</div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Toggle which data domains your system should monitor. <strong>Student limit: 3 domains.</strong></p>
                    <div class="row g-3">
                        @foreach($domains as $domain)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="domain_subscriptions[{{ $domain->id }}]" id="dom_{{ $domain->id }}" 
                                        {{ data_get($tenant->settings, "domain_subscriptions.{$domain->name}", true) ? 'checked' : '' }} 
                                        class="form-check-input">
                                    <label class="form-check-label" for="dom_{{ $domain->id }}">{{ $domain->name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary px-5 py-2">Save All Settings</button>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">Plan Management</div>
            <div class="card-body">
                <h5 class="fw-bold">{{ strtoupper($tenant->plan) }}</h5>
                <p class="text-muted small">Your current plan includes standard analysis and weekly highlights.</p>
                
                @if($tenant->plan === 'student')
                    @if(!$tenant->upgrade_requested_at)
                        <form action="{{ route('admin.upgrade.request') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary w-100">Request Pro Access</button>
                        </form>
                    @else
                        <button disabled class="btn btn-outline-secondary w-100 italic">Upgrade Pending Review</button>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
