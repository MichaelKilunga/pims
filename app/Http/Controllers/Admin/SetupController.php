<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Http\Request;

class SetupController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        
        if ($tenant->setup_completed_at) {
            return redirect()->route('admin.dashboard');
        }

        // Available default domains to suggest
        $suggestedDomains = [
            'Global Security',
            'Market Disruption',
            'Policy & Regulation',
            'Deep Tech',
            'Economic Indicators'
        ];

        return view('admin.setup.index', compact('tenant', 'suggestedDomains'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'organization_name' => 'required|string|max:255',
            'domains' => 'required|array|min:1',
            'digest_frequency' => 'required|string|in:daily,weekly,both',
            'budget_ceiling' => 'required|numeric|min:2',
        ]);

        $user = auth()->user();
        $tenant = $user->tenant;

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $tenant) {
            // Update Tenant Identity & Policy
            $tenant->update([
                'name' => $request->organization_name,
                'setup_completed_at' => now(),
                'ai_monthly_budget_usd' => $request->budget_ceiling,
                'settings' => array_merge($tenant->settings ?? [], [
                    'digest_frequency' => $request->digest_frequency,
                    'delivery_enabled' => true,
                    'relevance_threshold' => 40,
                ])
            ]);

            // Create Initial Domains
            foreach ($request->domains as $domainName) {
                Domain::create([
                    'tenant_id' => $tenant->id,
                    'name' => $domainName,
                    'is_active' => true,
                ]);
            }
        });

        return redirect()->route('admin.dashboard')->with('success', 'PIMS activated successfully. Initial intelligence scanning is now in progress.');
    }
}
