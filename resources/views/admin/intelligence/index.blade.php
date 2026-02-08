@extends('layouts.admin')

@section('title', 'Intelligence Feed')

@section('content')
<div class="card card-body mb-4">
    <form action="{{ route('admin.intelligence.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small text-muted text-uppercase fw-bold">Domain</label>
            <select name="domain" class="form-select select2">
                <option value="">All Domains</option>
                @foreach($domains as $domain)
                    <option value="{{ $domain->name }}" {{ request('domain') == $domain->name ? 'selected' : '' }}>{{ $domain->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted text-uppercase fw-bold">Priority</label>
            <select name="priority" class="form-select">
                <option value="">All Priorities</option>
                <option value="2" {{ request('priority') == '2' ? 'selected' : '' }}>ACT</option>
                <option value="1" {{ request('priority') == '1' ? 'selected' : '' }}>WATCH</option>
                <option value="0" {{ request('priority') == '0' ? 'selected' : '' }}>Routine</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter Results</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover datatable align-middle">
            <thead>
                <tr>
                    <th class="text-uppercase small fw-bold">Published</th>
                    <th class="text-uppercase small fw-bold">Domain</th>
                    <th class="text-uppercase small fw-bold">Title</th>
                    <th class="text-uppercase small fw-bold text-center">Priority</th>
                    <th class="text-uppercase small fw-bold text-center">Score</th>
                    <th class="text-end"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($signals as $signal)
                <tr>
                    <td class="text-muted small" style="white-space: nowrap;">
                        {{ $signal->created_at->format('M d, H:i') }}
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $signal->domain->name }}</span>
                    </td>
                    <td class="fw-medium">
                        @if(($signal->meta['status'] ?? '') === 'analysis_skipped_plan_limit')
                            <span class="text-muted text-decoration-line-through">{{ Str::limit($signal->title, 50) }}</span>
                        @else
                            {{ Str::limit($signal->title, 60) }}
                        @endif
                    </td>
                    <td class="text-center">
                        @if(($signal->meta['status'] ?? '') === 'analysis_skipped_plan_limit')
                            <span class="badge badge-routine">SKIPPED</span>
                        @elseif($signal->action_required == 2)
                            <span class="badge badge-act">ACT</span>
                        @elseif($signal->action_required == 1)
                            <span class="badge badge-watch">WATCH</span>
                        @else
                            <span class="badge badge-routine">Routine</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="fw-bold">{{ $signal->relevance_score ?: '-' }}</span>
                    </td>
                    <td class="text-end">
                        @if(($signal->meta['status'] ?? '') === 'analysis_skipped_plan_limit')
                            <button disabled class="btn btn-sm btn-outline-secondary">Analysis Paused</button>
                        @else
                            <a href="{{ route('admin.intelligence.show', $signal) }}" class="btn btn-sm btn-outline-primary">View Analysis</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="mt-4">
            {{ $signals->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
