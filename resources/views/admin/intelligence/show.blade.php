@extends('layouts.admin')

@section('title', 'Intelligence Analysis')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.intelligence.index') }}" class="text-decoration-none text-muted small fw-bold">
        &larr; BACK TO FEED
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h1 class="h3 fw-bold mb-0 text-dark">{{ $signal->title }}</h1>
                    @if($signal->action_required == 2)
                        <span class="badge badge-act fs-6">CRITICAL: ACT</span>
                    @elseif($signal->action_required == 1)
                        <span class="badge badge-watch fs-6">WATCH</span>
                    @else
                        <span class="badge badge-routine fs-6">ROUTINE</span>
                    @endif
                </div>
                
                <p class="text-muted small mb-4">
                    Source: <a href="{{ $signal->url }}" target="_blank" class="text-primary text-decoration-none fw-medium">{{ parse_url($signal->url, PHP_URL_HOST) }}</a> 
                    <span class="mx-2">&bull;</span> 
                    Published: {{ $signal->created_at->format('F j, Y H:i') }}
                </p>

                <div class="mb-4">
                    <h6 class="text-uppercase small fw-bold text-muted border-bottom pb-2 mb-3">AI Synthesis</h6>
                    <p class="lead" style="line-height: 1.7; color: #34495e;">{{ $signal->summary }}</p>
                </div>

                <div class="alert alert-danger border-start border-4 bg-light bg-opacity-10 p-4">
                    <h6 class="text-danger fw-bold text-uppercase small mb-2">Strategic Implications</h6>
                    <p class="mb-0 fst-italic text-dark">{{ $signal->implications }}</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Manual Intervention</div>
            <div class="card-body">
                <p class="text-muted small mb-3">If the AI miscategorized this signal, you can override the action priority below.</p>
                <form action="{{ route('admin.intelligence.override', $signal) }}" method="POST" class="row g-2">
                    @csrf
                    <div class="col-md-8">
                        <select name="action_required" class="form-select">
                            <option value="2" {{ $signal->action_required == 2 ? 'selected' : '' }}>Move to ACT</option>
                            <option value="1" {{ $signal->action_required == 1 ? 'selected' : '' }}>Move to WATCH</option>
                            <option value="0" {{ $signal->action_required == 0 ? 'selected' : '' }}>Ignore / Routine</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Update Priority</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header text-uppercase small fw-bold">Scoring Metrics</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Relevance Score</label>
                    <div class="h3 fw-bold mb-0 text-primary">{{ $signal->relevance_score }}/100</div>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $signal->relevance_score }}%"></div>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Source Trust</label>
                    <div class="h4 fw-bold mb-0 text-dark">{{ $signal->source->trust_weight }}%</div>
                </div>
                <hr>
                <div>
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Determination Status</label>
                    <div class="small fw-medium">{{ $signal->meta['user_override'] ? 'Manual Override' : 'AI Determined' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
