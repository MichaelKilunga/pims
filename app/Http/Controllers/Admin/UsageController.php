<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Run;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsageController extends Controller
{
    public function index()
    {
        $tenantId = config('app.tenant_id');
        $tenant = Tenant::findOrFail($tenantId);

        // 1. Monthly Totals
        $runs = Run::where('type', 'analysis')
            ->where('started_at', '>=', now()->startOfMonth())
            ->get();

        $stats = [
            'total_cost' => $runs->sum(fn($r) => $r->meta['stats']['total_cost'] ?? 0),
            'total_tokens' => $runs->sum(fn($r) => $r->meta['stats']['total_tokens'] ?? 0),
            'blocked_attempts' => Run::where('type', 'analysis')
                ->where('started_at', '>=', now()->startOfMonth())
                ->where('meta->status', 'blocked_budget')
                ->count(),
        ];

        // 2. Daily Trend (Last 30 days)
        $dailyTrend = Run::where('type', 'analysis')
            ->where('started_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(started_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.usage', compact('tenant', 'stats', 'dailyTrend'));
    }
}
