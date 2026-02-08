<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIMS | Personal Intelligence Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --pims-blue: #3b82f6; --pims-dark: #0f172a; }
        body { font-family: 'Inter', sans-serif; background-color: #ffffff; color: #334155; }
        .navbar { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
        .hero-section { background: radial-gradient(circle at top right, #f8fafc 0%, #ffffff 100%); padding: 120px 0; border-bottom: 1px solid #f1f5f9; }
        .feature-card { border: none; border-radius: 1.5rem; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background: #f8fafc; }
        .feature-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.05); background: #ffffff; border: 1px solid #e2e8f0; }
        .btn-primary { background-color: var(--pims-blue); border: none; padding: 12px 32px; border-radius: 0.75rem; font-weight: 600; }
        .badge-new { background-color: #ecf2ff; color: var(--pims-blue); padding: 6px 16px; border-radius: 2rem; font-size: 0.875rem; font-weight: 600; }
        .footer { background-color: var(--pims-dark); color: #94a3b8; padding: 80px 0; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary fs-4" href="/">PIMS</a>
            <div class="d-flex align-items-center">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary me-2">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-decoration-none text-muted fw-bold me-4">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Start Fresh</a>
                @endauth
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container text-center">
            <span class="badge-new mb-4 d-inline-block">AI-Driven Strategic Awareness</span>
            <h1 class="display-3 fw-bold text-dark mb-4 px-lg-5">Intelligence at the speed of decision-making.</h1>
            <p class="lead text-muted mb-5 px-lg-5 mx-lg-5">PIMS automates your signal discovery, synthesizes strategic implications, and delivers executive-grade briefings directly to your inbox. Designed for founders and executives who need to stay ahead without the noise.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5">Join the Foundation</a>
                <a href="#features" class="btn btn-outline-secondary btn-lg px-5">Explore Capabilities</a>
            </div>
        </div>
    </header>

    <section id="features" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold h1 mb-3">Engineered for Clarity</h2>
                <p class="text-muted">High-precision tools for modern strategists.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card p-5 h-100">
                        <div class="h3 fw-bold text-primary mb-3">01</div>
                        <h4 class="fw-bold mb-3">Autonomous Discovery</h4>
                        <p class="text-muted mb-0">Our pipeline continuously scans technical documents, policy shifts, and market disruptions based on your specific strategic domains.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-5 h-100 border border-primary border-opacity-25 shadow-lg" style="background: white;">
                        <div class="h3 fw-bold text-primary mb-3">02</div>
                        <h4 class="fw-bold mb-3">Deep AI Synthesis</h4>
                        <p class="text-muted mb-0">Beyond mere summaries. PIMS extracts "Strategic Implications" for every signal, telling you exactly why a piece of data matters to you.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-5 h-100">
                        <div class="h3 fw-bold text-primary mb-3">03</div>
                        <h4 class="fw-bold mb-3">Executive Briefings</h4>
                        <p class="text-muted mb-0">Scheduled delivery of "ACT" and "WATCH" signals. Beautiful, high-density reports that respect your time and attention.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="display-5 fw-bold mb-4">You own the setup. We own the processing.</h2>
                    <p class="lead text-muted mb-4">With our self-service onboarding, you define your strategic boundaries in minutes. PIMS takes over the relentless scanning and analysis, ensuring you never miss a critical market shift.</p>
                    <ul class="list-unstyled mb-5">
                        <li class="mb-3 d-flex align-items-center"><span class="text-primary me-2 fst-normal">✓</span> No complex enterprise integration needed.</li>
                        <li class="mb-3 d-flex align-items-center"><span class="text-primary me-2">✓</span> Hard cost-capping with transparent AI budgeting.</li>
                        <li class="mb-3 d-flex align-items-center"><span class="text-primary me-2">✓</span> Zero data leakage between organizations.</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Activate Your Platform</a>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <div class="p-4 bg-white shadow-lg rounded-4 overflow-hidden border">
                        <div class="d-flex gap-2 mb-3">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background: #ff5f56;"></div>
                            <div style="width: 12px; height: 12px; border-radius: 50%; background: #ffbd2e;"></div>
                            <div style="width: 12px; height: 12px; border-radius: 50%; background: #27c93f;"></div>
                        </div>
                        <div class="p-3 bg-light rounded small font-monospace text-muted">
                            [pims-engine] Discovered 14 new signals for "Deep Tech"<br>
                            [pims-ai] Analyzing 3 critical policy shifts...<br>
                            [pims-ai] Extraction: "US Chip Export Rules (Strategic ACT)"<br>
                            [pims-delivery] Briefing successfully queued for Monday 07:00 AM.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer text-center text-lg-start">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h2 class="text-white fw-bold mb-4">PIMS</h2>
                    <p>Building the foundation for autonomous strategic awareness.</p>
                </div>
                <div class="col-lg-8">
                    <div class="text-lg-end mt-4">
                        <p class="mb-0">© 2026 PIMS Foundry. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
