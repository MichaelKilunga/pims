<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Domain;
use App\Intelligence\TenantSettingsService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $tenantId = config('app.tenant_id');
        $tenant = Tenant::findOrFail($tenantId);
        $domains = Domain::all();

        return view('admin.settings', compact('tenant', 'domains'));
    }

    public function update(Request $request, TenantSettingsService $settingsService)
    {
        $tenantId = config('app.tenant_id');
        $tenant = Tenant::findOrFail($tenantId);

        $validated = $request->validate([
            'digest_frequency' => 'required|in:daily,weekly,both,none',
            'relevance_threshold' => 'required|numeric|min:0|max:100',
            'domain_subscriptions' => 'array',
            'delivery_enabled' => 'boolean',
        ]);

        $settings = $tenant->settings ?? [];
        $settings['digest_frequency'] = $validated['digest_frequency'];
        $settings['relevance_threshold'] = $validated['relevance_threshold'];
        $settings['delivery_enabled'] = $request->has('delivery_enabled');
        
        // Handle domain subscriptions (checkboxes)
        $domainSubs = [];
        $allDomains = Domain::all();
        foreach ($allDomains as $domain) {
            $domainSubs[$domain->name] = isset($validated['domain_subscriptions'][$domain->id]);
        }
        $settings['domain_subscriptions'] = $domainSubs;

        $tenant->update(['settings' => $settings]);

        return back()->with('success', 'Settings updated successfully.');
    }
}
