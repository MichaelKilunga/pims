@extends('layouts.admin')

@section('title', 'Intelligence Domains')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold">Active Subscriptions</span>
                <span class="badge bg-primary">{{ $domains->count() }} Domains Tracked</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-uppercase small fw-bold">Domain Name</th>
                            <th class="text-uppercase small fw-bold text-center">Signals Captured</th>
                            <th class="text-uppercase small fw-bold text-center">Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($domains as $domain)
                        <tr>
                            <td class="ps-4 fw-medium text-dark">{{ $domain->name }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ number_format($domain->signals_count) }}</span>
                            </td>
                            <td class="text-center">
                                @if($domain->is_active)
                                    <span class="badge bg-success">MONITORING</span>
                                @else
                                    <span class="badge bg-outline-secondary text-muted">PAUSED</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <form action="{{ route('admin.domains.toggle', $domain) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $domain->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                            {{ $domain->is_active ? 'Pause' : 'Resume' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.domains.destroy', $domain) }}" method="POST" onsubmit="return confirm('Danger: Removing a domain will stop all future signal discovery for this area. Proceed?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($domains->isEmpty())
                    <div class="text-center py-5">
                        <p class="text-muted">No intelligence domains configured. Discovery engine is idle.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-primary">
            <div class="card-header bg-primary text-white fw-bold">Add New Domain</div>
            <div class="card-body">
                <p class="text-muted small">Target a new strategic area for PIMS to monitor across the indexing pipeline.</p>
                <form action="{{ route('admin.domains.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Strategic Area Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Semiconductor Policy" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Activate Tracker</button>
                </form>
            </div>
        </div>

        <div class="card mt-4 border-0 bg-light">
            <div class="card-body">
                <h6 class="fw-bold text-uppercase small text-muted">How it works</h6>
                <p class="small mb-0">Adding a domain triggers the <strong>Discovery Engine</strong> to find relevant RSS feeds, news sources, and technical documents matching the keywords associated with this area.</p>
            </div>
        </div>
    </div>
</div>
@endsection
