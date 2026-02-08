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
        $tenant = auth()->user()->tenant;
        $domains = $tenant->domains;

        return view('admin.settings', compact('tenant', 'domains'));
    }

    public function update(Request $request, TenantSettingsService $settingsService)
    {
        if (!auth()->user()->isOwner()) {
            abort(403);
        }

        $tenant = auth()->user()->tenant;

        if ($request->has('digest_frequency')) {
            $settingsService->set($tenant, 'digest_frequency', $request->digest_frequency);
        }

        if ($request->has('delivery_enabled')) {
            $settingsService->set($tenant, 'delivery_enabled', $request->boolean('delivery_enabled'));
        }

        if ($request->has('relevance_threshold')) {
            $settingsService->set($tenant, 'relevance_threshold', (int) $request->relevance_threshold);
        }

        return back()->with('success', 'Intelligence policy updated successfully.');
    }

    public function requestUpgrade()
    {
        if (!auth()->user()->isOwner()) {
            abort(403);
        }

        $tenant = auth()->user()->tenant;

        if ($tenant->upgrade_requested_at) {
            return back()->with('info', 'Your upgrade request is already being processed.');
        }

        $tenant->update(['upgrade_requested_at' => now()]);

        return back()->with('success', 'Your request for Pro Access has been submitted.');
    }
}
