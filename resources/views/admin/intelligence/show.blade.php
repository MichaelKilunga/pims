@extends('layouts.admin')

@section('title', 'Intelligence Analysis')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('admin.intelligence.index') }}" style="color: var(--text-muted); text-decoration: none;">&larr; Back to Feed</a>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.75rem; margin-top: 0;">{{ $signal->title }}</h1>
            <p style="color: var(--text-muted);">
                Source: <a href="{{ $signal->url }}" target="_blank" style="color: var(--accent-color);">{{ parse_url($signal->url, PHP_URL_HOST) }}</a> | 
                Published: {{ $signal->created_at->format('F j, Y H:i') }}
            </p>
        </div>
        @if($signal->action_required == 2)
            <span class="badge badge-act" style="font-size: 1rem;">CRITICAL: ACT</span>
        @elseif($signal->action_required == 1)
            <span class="badge badge-watch" style="font-size: 1rem;">WATCH</span>
        @else
            <span class="badge badge-routine" style="font-size: 1rem;">ROUTINE</span>
        @endif
    </div>

    <div style="margin-bottom: 2rem;">
        <h3 style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; color: var(--text-muted); text-transform: uppercase; font-size: 0.875rem;">AI Synthesis</h3>
        <p style="font-size: 1.125rem; line-height: 1.6;">{{ $signal->summary }}</p>
    </div>

    <div style="margin-bottom: 2rem; background: #fdf2f2; border-left: 4px solid var(--danger); padding: 1.5rem; border-radius: 0.25rem;">
        <h3 style="margin-top: 0; color: #991b1b;">Strategic Implications</h3>
        <p style="margin-bottom: 0; font-style: italic;">{{ $signal->implications }}</p>
    </div>

    <div class="grid" style="grid-template-columns: repeat(3, 1fr);">
        <div class="card">
            <h4>Relevance Score</h4>
            <p style="font-size: 1.5rem; font-weight: 700;">{{ $signal->relevance_score }}/100</p>
        </div>
        <div class="card">
            <h4>Source Trust</h4>
            <p style="font-size: 1.5rem; font-weight: 700;">{{ $signal->source->trust_weight }}%</p>
        </div>
        <div class="card">
            <h4>Status</h4>
            <p style="font-size: 1rem;">{{ $signal->meta['user_override'] ? 'Manual Override' : 'AI Determined' }}</p>
        </div>
    </div>
</div>

<div class="card">
    <h3>Manual Intervention</h3>
    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">If the AI miscategorized this signal, you can override the action priority below.</p>
    <form action="{{ route('admin.intelligence.override', $signal) }}" method="POST" style="display: flex; gap: 1rem;">
        @csrf
        <select name="action_required" style="padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.375rem;">
            <option value="2" {{ $signal->action_required == 2 ? 'selected' : '' }}>Move to ACT</option>
            <option value="1" {{ $signal->action_required == 1 ? 'selected' : '' }}>Move to WATCH</option>
            <option value="0" {{ $signal->action_required == 0 ? 'selected' : '' }}>Ignore / Routine</option>
        </select>
        <button type="submit" class="btn btn-primary">Update Priority</button>
    </form>
</div>
@endsection
