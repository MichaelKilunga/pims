<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfSetupIncomplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && 
            auth()->user()->hasVerifiedEmail() &&
            !auth()->user()->tenant->setup_completed_at && 
            !$request->is('setup*', 'logout')) {
            return redirect()->route('setup.index');
        }

        return $next($request);
    }
}
