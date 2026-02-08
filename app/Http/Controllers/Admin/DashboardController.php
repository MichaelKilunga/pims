<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Signal;
use App\Models\Run;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $tenantId = config('app.tenant_id');
        $tenant = Tenant::findOrFail($tenantId);
        $plan = $tenant->plan ?? 'free';
        $limits = config("plans.{$plan}");

        // 1. Weekly Stats
        $stats = [
            'total' => Signal::where('created_at', '>=', now()->startOfWeek())->count(),
            'act' => Signal::where('created_at', '>=', now()->startOfWeek())->where('action_required', 2)->count(),
            'watch' => Signal::where('created_at', '>=', now()->startOfWeek())->where('action_required', 1)->count(),
        ];

        // 2. Budget Utilization
        $monthlyCost = Run::where('type', 'analysis')
            ->where('started_at', '>=', now()->startOfMonth())
            ->get()
            ->sum(fn($run) => (float) ($run->meta['stats']['total_cost'] ?? 0));

        $budget = [
            'used' => $monthlyCost,
            'limit' => (float) $limits['ai_monthly_budget'],
            'percent' => $limits['ai_monthly_budget'] > 0 
                ? ($monthlyCost / $limits['ai_monthly_budget']) * 100 
                : 0,
        ];

        // 3. Domain Usage
        $domainUsage = [
            'used' => $tenant->domains()->count(),
            'limit' => $limits['max_domains'],
        ];

        // 4. Last Run Status
        $lastRuns = [
            'fetch' => Run::where('type', 'fetch')->latest()->first(),
            'scoring' => Run::where('type', 'analysis')->whereNotNull('completed_at')->latest()->first(),
            'delivery' => Run::where('type', 'delivery')->latest()->first(),
        ];

        return view('admin.dashboard', compact('tenant', 'stats', 'budget', 'domainUsage', 'limits', 'plan'));
    }
}
