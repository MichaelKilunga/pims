<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIMS Onboarding</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f8fafc;
            --accent-color: #3b82f6;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 600px;
            padding: 2rem;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 2.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        h1 { font-size: 1.5rem; margin-top: 0; color: var(--accent-color); }
        p { line-height: 1.6; color: var(--text-muted); }

        .domain-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }

        .domain-item {
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .domain-item:hover { border-color: var(--accent-color); background: #f0f7ff; }

        input[type="checkbox"] { display: none; }
        input[type="checkbox"]:checked + label .domain-item {
            border-color: var(--accent-color);
            background: #eff6ff;
            font-weight: 600;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        .limits-box {
            background: #f1f5f9;
            padding: 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Welcome to PIMS for Students</h1>
            <p>Your Personal Intelligence Monitoring System is ready. As a student, you'll receive a weekly high-level executive briefing tailored to your chosen areas of study.</p>
            
            <div class="limits-box">
                <strong>Student Plan Highlights:</strong>
                <ul style="margin: 0.5rem 0 0; padding-left: 1.25rem;">
                    <li>Up to 3 Intelligence Domains</li>
                    <li>Weekly AI-Synthesized Briefings</li>
                    <li>$2.00 Monthly AI Subsidy included</li>
                </ul>
            </div>

            <form action="{{ route('onboarding.student.store') }}" method="POST">
                @csrf
                <p><strong>Step 1: Select up to 3 Domains to Monitor</strong></p>
                
                <div class="domain-grid">
                    @foreach($domains as $domain)
                        <input type="checkbox" name="domains[]" value="{{ $domain->id }}" id="dom_{{ $domain->id }}">
                        <label for="dom_{{ $domain->id }}">
                            <div class="domain-item">
                                {{ $domain->name }}
                            </div>
                        </label>
                    @endforeach
                </div>

                <p style="font-size: 0.875rem; text-align: center; margin-bottom: 1.5rem;">
                    <em>Note: Your first briefing will be sent next Monday morning.</em>
                </p>

                <button type="submit" class="btn">Finish Onboarding</button>
            </form>
        </div>
    </div>

    <script>
        // Simple limit enforcer
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                const checked = document.querySelectorAll('input[type="checkbox"]:checked');
                if (checked.length > 3) {
                    cb.checked = false;
                    alert('Student plan is limited to 3 domains. Upgrade to Pro for unlimited monitoring.');
                }
            });
        });
    </script>
</body>
</html>
