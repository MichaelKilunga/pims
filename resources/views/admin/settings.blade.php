@extends('layouts.admin')

@section('title', 'Tenant Settings')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    <div class="card" style="margin-bottom: 2rem;">
        <h3>Digest & Delivery</h3>
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Delivery Cadence</label>
            <select name="digest_frequency" style="width: 100%; max-width: 400px; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                <option value="daily" {{ data_get($tenant->settings, 'digest_frequency') == 'daily' ? 'selected' : '' }}>Daily Briefing Only</option>
                <option value="weekly" {{ data_get($tenant->settings, 'digest_frequency') == 'weekly' ? 'selected' : '' }}>Weekly Summary Only</option>
                <option value="both" {{ data_get($tenant->settings, 'digest_frequency') == 'both' || !data_get($tenant->settings, 'digest_frequency') ? 'selected' : '' }}>Both Daily & Weekly</option>
                <option value="none" {{ data_get($tenant->settings, 'digest_frequency') == 'none' ? 'selected' : '' }}>Paused</option>
            </select>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Relevance Threshold</label>
            <input type="number" name="relevance_threshold" value="{{ data_get($tenant->settings, 'relevance_threshold', 40) }}" style="width: 100%; max-width: 100px; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
            <p style="color: var(--text-muted); font-size: 0.875rem;">Only signals scoring above this value will be sent for AI analysis.</p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <input type="checkbox" name="delivery_enabled" id="delivery_enabled" {{ data_get($tenant->settings, 'delivery_enabled', true) ? 'checked' : '' }} style="width: 1.25rem; height: 1.25rem;">
            <label for="delivery_enabled" style="font-weight: 600;">Enable Email Intelligence Reports</label>
        </div>
    </div>

    <div class="card" style="margin-bottom: 2rem;">
        <h3>Intelligence Domain Subscriptions</h3>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Toggle which data domains your system should monitor.</p>
        <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
            @foreach($domains as $domain)
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="domain_subscriptions[{{ $domain->id }}]" id="dom_{{ $domain->id }}" 
                        {{ data_get($tenant->settings, "domain_subscriptions.{$domain->name}", true) ? 'checked' : '' }} 
                        style="width: 1rem; height: 1rem;">
                    <label for="dom_{{ $domain->id }}">{{ $domain->name }}</label>
                </div>
            @endforeach
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="padding: 1rem 2rem;">Save Settings</button>
</form>
@endsection
