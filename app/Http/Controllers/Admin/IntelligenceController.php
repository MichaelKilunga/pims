<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Signal;
use App\Models\Domain;
use Illuminate\Http\Request;

class IntelligenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Signal::with('domain')
            ->orderBy('created_at', 'desc');

        if ($request->has('domain')) {
            $query->whereHas('domain', function($q) use ($request) {
                $q->where('name', $request->domain);
            });
        }

        if ($request->has('priority')) {
            $query->where('action_required', $request->priority);
        }

        $signals = $query->paginate(20);
        $domains = Domain::all();

        return view('admin.intelligence.index', compact('signals', 'domains'));
    }

    public function show(Signal $signal)
    {
        return view('admin.intelligence.show', compact('signal'));
    }

    public function override(Request $request, Signal $signal)
    {
        $request->validate([
            'action_required' => 'required|integer|in:0,1,2',
        ]);

        $signal->update([
            'action_required' => $request->action_required,
            'meta' => array_merge($signal->meta ?? [], [
                'user_override' => [
                    'by' => auth()->id(),
                    'at' => now()->toIso8601String(),
                ]
            ])
        ]);

        return back()->with('success', 'Signal priority updated manually.');
    }
}
