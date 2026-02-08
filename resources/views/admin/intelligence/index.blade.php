@extends('layouts.admin')

@section('title', 'Intelligence Feed')

@section('content')
<div class="card" style="margin-bottom: 2rem;">
    <form action="{{ route('admin.intelligence.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end;">
        <div>
            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.5rem;">Domain</label>
            <select name="domain" style="padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.375rem;">
                <option value="">All Domains</option>
                @foreach($domains as $domain)
                    <option value="{{ $domain->name }}" {{ request('domain') == $domain->name ? 'selected' : '' }}>{{ $domain->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.5rem;">Priority</label>
            <select name="priority" style="padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.375rem;">
                <option value="">All Priorities</option>
                <option value="2" {{ request('priority') == '2' ? 'selected' : '' }}>ACT</option>
                <option value="1" {{ request('priority') == '1' ? 'selected' : '' }}>WATCH</option>
                <option value="0" {{ request('priority') == '0' ? 'selected' : '' }}>Routine</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="padding-bottom: 0.65rem;">Filter</button>
    </form>
</div>

<div class="card">
    <table style="width: 100%;">
        <thead>
            <tr>
                <th>Published</th>
                <th>Domain</th>
                <th>Title</th>
                <th>Priority</th>
                <th>Score</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($signals as $signal)
            <tr>
                <td style="white-space: nowrap; color: var(--text-muted);">{{ $signal->created_at->format('M d, H:i') }}</td>
                <td>{{ $signal->domain->name }}</td>
                <td style="font-weight: 500;">{{ Str::limit($signal->title, 60) }}</td>
                <td>
                    @if($signal->action_required == 2)
                        <span class="badge badge-act">ACT</span>
                    @elseif($signal->action_required == 1)
                        <span class="badge badge-watch">WATCH</span>
                    @else
                        <span class="badge badge-routine">Routine</span>
                    @endif
                </td>
                <td><strong>{{ $signal->relevance_score }}</strong></td>
                <td style="text-align: right;">
                    <a href="{{ route('admin.intelligence.show', $signal) }}" class="btn" style="border-color: var(--border-color); font-size: 0.875rem;">View Analysis</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top: 1.5rem;">
        {{ $signals->links() }}
    </div>
</div>
@endsection
