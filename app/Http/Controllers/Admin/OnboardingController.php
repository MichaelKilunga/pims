<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Domain;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function student()
    {
        $tenantId = config('app.tenant_id');
        $tenant = Tenant::findOrFail($tenantId);

        // If already has domains, maybe they shouldn't be here, but let's allow it for now
        $domains = Domain::all();

        return view('admin.onboarding.student', compact('tenant', 'domains'));
    }

    public function storeStudent(Request $request)
    {
        $tenantId = config('app.tenant_id');
        $tenant = Tenant::findOrFail($tenantId);

        $validated = $request->validate([
            'domains' => 'required|array|min:1|max:3',
            'domains.*' => 'exists:domains,id',
        ]);

        // Clear existing subscriptions if any and set new ones
        $settings = $tenant->settings ?? [];
        $domainSubs = [];
        $allDomains = Domain::all();
        
        foreach ($allDomains as $domain) {
            $domainSubs[$domain->name] = in_array($domain->id, $validated['domains']);
        }

        $settings['domain_subscriptions'] = $domainSubs;
        $settings['digest_frequency'] = 'weekly'; // Forced for students
        $settings['relevance_threshold'] = 50; // Sensible default
        $settings['delivery_enabled'] = true;

        $tenant->update([
            'settings' => $settings,
            'plan' => 'student', // Ensure plan is set
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Onboarding complete! Your intelligence feed is now being prepared.');
    }
}
