<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f8fafc;
            --sidebar-color: #1e293b;
            --accent-color: #3b82f6;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #10b981;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        aside {
            width: 260px;
            background-color: var(--sidebar-color);
            color: white;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
        }

        aside h1 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 2rem;
            padding-left: 0.5rem;
            color: var(--accent-color);
        }

        aside nav a {
            color: #cbd5e1;
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            display: block;
            transition: all 0.2s;
        }

        aside nav a:hover, aside nav a.active {
            background-color: #334155;
            color: white;
        }

        /* Content */
        main {
            flex: 1;
            padding: 2rem 3rem;
            overflow-y: auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        header h2 {
            font-size: 1.5rem;
            margin: 0;
        }

        /* UI Components */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card h3 {
            font-size: 0.875rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0;
        }

        .stat-card p {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0 0;
        }

        .badge {
            padding: 0.25rem 0.625rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-act { background: #fee2e2; color: #991b1b; }
        .badge-watch { background: #ffedd5; color: #9a3412; }
        .badge-routine { background: #f1f5f9; color: #475569; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 1rem;
            border-bottom: 2px solid var(--border-color);
            color: var(--text-muted);
            font-weight: 600;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .btn-primary { background: var(--accent-color); color: white; }
    </style>
</head>
<body>
    <aside>
        <h1>PIMS ADMIN</h1>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.intelligence.index') }}" class="{{ request()->routeIs('admin.intelligence.*') ? 'active' : '' }}">Intelligence Feed</a>
            <a href="{{ route('admin.usage.index') }}" class="{{ request()->routeIs('admin.usage.*') ? 'active' : '' }}">AI Usage</a>
            <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">Settings</a>
        </nav>
    </aside>

    <main>
        <header>
            <h2>@yield('title', 'Dashboard')</h2>
            <div class="user-info">
                <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->tenant->name }})
            </div>
        </header>

        @if(session('success'))
            <div class="card" style="background-color: #dcfce7; color: #166534; margin-bottom: 2rem; border-color: #bbf7d0;">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
