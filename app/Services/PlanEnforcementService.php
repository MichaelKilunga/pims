<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Run;
use Illuminate\Support\Facades\Log;

class PlanEnforcementService
{
    /**
     * Check if a tenant can perform a specific action based on their plan limits.
     */
    public function can(Tenant $tenant, string $action, array $params = []): bool
    {
        $plan = $tenant->plan ?? 'free';
        $limits = config("plans.{$plan}");

        if (!$limits) {
            Log::warning("Enforcement: No limits defined for plan '{$plan}'. Defaulting to blocked.");
            return false;
        }

        return match ($action) {
            'add_domain' => $tenant->domains()->count() < $limits['max_domains'],
            'add_source' => $this->canAddSource($tenant, $params['domain_id'] ?? null),
            'fetch_content' => true, // Fetching is generally allowed but filtered by source count
            'analyze_ai' => $this->canAnalyze($tenant),
            'send_daily_digest' => (bool) ($limits['features']['daily_digest'] ?? false),
            'send_weekly_summary' => true, // All plans allow weekly summaries
            'custom_keywords' => (bool) ($limits['features']['custom_keywords'] ?? false),
            'manual_priority_override' => (bool) ($limits['features']['manual_priority_override'] ?? false),
            default => true,
        };
    }

    /**
     * Check if tenant can add more sources to a domain.
     */
    protected function canAddSource(Tenant $tenant, ?int $domainId): bool
    {
        if (!$domainId) return false;
        
        $plan = $tenant->plan ?? 'free';
        $limit = config("plans.{$plan}.max_sources_per_domain", 0);
        
        $count = $tenant->sources()->where('domain_id', $domainId)->count();
        
        return $count < $limit;
    }

    /**
     * Check if tenant is within their plan's AI budget.
     */
    protected function canAnalyze(Tenant $tenant): bool
    {
        $plan = $tenant->plan ?? 'free';
        $limit = config("plans.{$plan}.ai_monthly_budget", 0);
        
        // Use the common budget check logic but override with plan-specific limit
        $monthlyCost = Run::where('tenant_id', $tenant->id)
            ->where('type', 'analysis')
            ->where('started_at', '>=', now()->startOfMonth())
            ->get()
            ->sum(fn($run) => (float) ($run->meta['stats']['total_cost'] ?? 0));

        if ($monthlyCost >= (float) $limit) {
            Log::info("Plan Enforcement: AI Budget reached for Tenant {$tenant->id} ({$tenant->plan}).");
            return false;
        }

        return true;
    }

    /**
     * Get the AI depth allowed for the plan.
     */
    public function getAiDepth(Tenant $tenant): string
    {
        $plan = $tenant->plan ?? 'free';
        return config("plans.{$plan}.ai_depth", 'basic');
    }
}
