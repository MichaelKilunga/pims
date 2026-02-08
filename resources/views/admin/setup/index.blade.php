<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Your Intelligence Platform | PIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .wizard-card { border: none; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .step-indicator { width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 0.5rem; transition: all 0.3s; }
        .step-indicator.active { background: #3b82f6; color: white; }
        .step-indicator.done { background: #10b981; color: white; }
        .setup-step { display: none; }
        .setup-step.active { display: block; }
        .domain-badge { cursor: pointer; transition: all 0.2s; }
        .domain-badge:hover { transform: translateY(-2px); }
        .domain-badge.selected { background-color: #3b82f6 !important; color: white !important; border-color: #3b82f6 !important; }
    </style>
</head>
<body class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h1 class="fw-bold text-primary">PIMS</h1>
                    <p class="text-muted">Personal Intelligence Management System</p>
                </div>

                <div class="card wizard-card">
                    <div class="card-body p-5">
                        <div class="d-flex justify-content-between mb-5 px-4 text-center">
                            <div>
                                <div class="step-indicator active mx-auto" id="node-1">1</div>
                                <span class="small fw-bold">Identity</span>
                            </div>
                            <div class="flex-grow-1 border-top mt-3 mx-2"></div>
                            <div>
                                <div class="step-indicator mx-auto" id="node-2">2</div>
                                <span class="small fw-bold">Domains</span>
                            </div>
                            <div class="flex-grow-1 border-top mt-3 mx-2"></div>
                            <div>
                                <div class="step-indicator mx-auto" id="node-3">3</div>
                                <span class="small fw-bold">Strategy</span>
                            </div>
                        </div>

                        <form action="{{ route('setup.store') }}" method="POST" id="setupForm">
                            @csrf
                            
                            <!-- Step 1: Identity -->
                            <div class="setup-step active" id="step-1">
                                <h3 class="fw-bold mb-3">Define Your Organization</h3>
                                <p class="text-muted mb-4">Welcome to PIMS. To begin, please give your intelligence environment a professional name.</p>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Organization Name</label>
                                    <input type="text" name="organization_name" value="{{ $tenant->name }}" class="form-control form-control-lg border-2" placeholder="e.g. Acme Strategy Group">
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary px-5 py-2 fw-bold" onclick="nextStep(2)">Continue &rarr;</button>
                                </div>
                            </div>

                            <!-- Step 2: Intelligence Domains -->
                            <div class="setup-step" id="step-2">
                                <h3 class="fw-bold mb-3">Intelligence Subscriptions</h3>
                                <p class="text-muted mb-4">Select at least one domain to monitor. These drive the automated data discovery and indexing engine.</p>
                                
                                <div class="row g-3 mb-4">
                                    @foreach($suggestedDomains as $domain)
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded domain-badge h-100" onclick="toggleDomain(this, '{{ $domain }}')">
                                                <div class="form-check pointer-none">
                                                    <input class="form-check-input visually-hidden" type="checkbox" name="domains[]" value="{{ $domain }}">
                                                    <label class="form-check-label fw-medium">{{ $domain }}</label>
                                                </div>
                                                <small class="text-muted d-block mt-1">Automated tracking for {{ strtolower($domain) }} signals.</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-link text-muted fw-bold" onclick="nextStep(1)">&larr; Back</button>
                                    <button type="button" class="btn btn-primary px-5 py-2 fw-bold" onclick="nextStep(3)">Set Strategy &rarr;</button>
                                </div>
                            </div>

                            <!-- Step 3: Strategy & Launch -->
                            <div class="setup-step" id="step-3">
                                <h3 class="fw-bold mb-3">Strategic Guardrails</h3>
                                <p class="text-muted mb-4">Configure your delivery cadence and AI budget safety cap.</p>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Monthly AI Budget Ceiling ($)</label>
                                    <select name="budget_ceiling" class="form-select form-select-lg border-2">
                                        <option value="5">$5 (Experimental)</option>
                                        <option value="10" selected>$10 (Standard Personal)</option>
                                        <option value="25">$25 (Deep Strategy)</option>
                                        <option value="50">$50 (Enterprise-Lite)</option>
                                    </select>
                                    <div class="form-text mt-2">PIMS will automatically pause high-cost analysis if this cap is reached.</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Intelligence Delivery</label>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <input type="radio" class="btn-check" name="digest_frequency" id="freq_weekly" value="weekly" checked>
                                            <label class="btn btn-outline-primary w-100 p-3" for="freq_weekly">
                                                <div class="fw-bold">Weekly Digest</div>
                                                <small>Low noise, high value.</small>
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="radio" class="btn-check" name="digest_frequency" id="freq_daily" value="both">
                                            <label class="btn btn-outline-primary w-100 p-3" for="freq_daily">
                                                <div class="fw-bold">Daily Briefing</div>
                                                <small>Real-time situational awareness.</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-link text-muted fw-bold" onclick="nextStep(2)">&larr; Back</button>
                                    <button type="submit" class="btn btn-success px-5 py-2 fw-bold">Activate PIMS &rarr;</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        function nextStep(step) {
            $('.setup-step').removeClass('active');
            $(`#step-${step}`).addClass('active');
            
            $('.step-indicator').removeClass('active done');
            for(let i=1; i<step; i++) {
                $(`#node-${i}`).addClass('done');
            }
            $(`#node-${step}`).addClass('active');
        }

        function toggleDomain(el, name) {
            const checkbox = $(el).find('input');
            checkbox.prop('checked', !checkbox.prop('checked'));
            $(el).toggleClass('selected');
        }
    </script>
</body>
</html>
