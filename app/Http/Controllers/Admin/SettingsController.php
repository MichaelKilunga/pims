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
        // ... (existing update logic)
    }

    public function requestUpgrade()
    {
        $tenantId = config('app.tenant_id');
        $tenant = Tenant::findOrFail($tenantId);

        if ($tenant->upgrade_requested_at) {
            return back()->with('info', 'Your upgrade request is already being processed.');
        }

        $tenant->update(['upgrade_requested_at' => now()]);

        return back()->with('success', 'Your request for Pro Access has been submitted. Our team will review your account.');
    }
}
