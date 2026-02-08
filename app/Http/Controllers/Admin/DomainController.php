<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function index()
    {
        $domains = auth()->user()->tenant->domains()->withCount('signals')->get();
        return view('admin.domains.index', compact('domains'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Domain::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $request->name,
            'is_active' => true,
        ]);

        return redirect()->route('admin.domains.index')->with('success', 'Intelligence domain added successfully.');
    }

    public function toggle(Domain $domain)
    {
        $this->authorizeOwner($domain);

        $domain->update(['is_active' => !$domain->is_active]);

        return redirect()->back()->with('success', 'Domain status updated.');
    }

    public function destroy(Domain $domain)
    {
        $this->authorizeOwner($domain);
        
        $domain->delete();

        return redirect()->route('admin.domains.index')->with('success', 'Domain removed.');
    }

    protected function authorizeOwner(Domain $domain)
    {
        if ($domain->tenant_id !== auth()->user()->tenant_id || !auth()->user()->isOwner()) {
            abort(403);
        }
    }
}
