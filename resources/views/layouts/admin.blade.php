<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} Admin</title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/admin.css'])
    
    @yield('styles')
</head>
<body>
    <div class="sidebar d-flex flex-column">
        <div class="p-4">
            <h4 class="text-white fw-bold mb-0">PIMS <span class="text-primary">ADMIN</span></h4>
        </div>
        
        <nav class="nav flex-column mt-2">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('admin.intelligence.index') }}" class="nav-link {{ request()->routeIs('admin.intelligence.*') ? 'active' : '' }}">
                Intelligence Feed
            </a>
            <a href="{{ route('admin.usage.index') }}" class="nav-link {{ request()->routeIs('admin.usage.*') ? 'active' : '' }}">
                AI Usage
            </a>
            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                Settings
            </a>
        </nav>
        
        <div class="mt-auto p-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm w-100">Logout</button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <header class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <h2 class="h4 mb-0 fw-bold">@yield('title', 'Dashboard')</h2>
                
                <div class="d-flex align-items-center">
                    <div class="text-end me-3">
                        <div class="fw-bold small">{{ auth()->user()->name }}</div>
                        <div class="text-muted smaller" style="font-size: 0.75rem;">{{ auth()->user()->tenant->name }}</div>
                    </div>
                    <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle" style="width: 40px; height: 40px;">
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- JS Dependencies -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @vite(['resources/js/admin/admin.js'])
    
    @yield('scripts')
</body>
</html>
