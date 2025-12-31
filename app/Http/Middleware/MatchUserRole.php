<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MatchUserRole
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:
     *   ->middleware('matchRole:sahitya,super_admin')
     *
     * Accepts either a single comma-separated string or multiple params.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Prefer the authenticated user (sanctum/session). Fallback to legacy session('user').
        $user = $request->user() ?? auth()->user() ?? session('user');

        // No user found -> redirect to login (or return JSON for API calls)
        if (! $user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Please login first.'], 401);
            }
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        // Normalize roles parameter: handle "a,b" single string or multiple params
        if (count($roles) === 1 && is_string($roles[0]) && strpos($roles[0], ',') !== false) {
            $roles = array_map('trim', explode(',', $roles[0]));
        } else {
            $roles = array_map(function ($r) {
                return is_string($r) ? trim($r) : $r;
            }, $roles);
        }

        if (empty($roles)) {
            // No roles provided -> deny access
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Access denied'], 403);
            }
            return redirect()->route('login')->with('error', 'Access denied');
        }

        // If $user is an array (rare), try to access role key; else assume object with role prop
        $userRole = is_array($user) ? ($user['role'] ?? null) : ($user->role ?? null);

        // super_admin को हमेशा access दो (bypass role check)
        if ($userRole === 'super_admin') {
            return $next($request);
        }

        // Final check for other roles
        if (! in_array($userRole, $roles, true)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Access denied'], 403);
            }
            return redirect()->route('dashboard.super_admin')->with('error', 'आपको इस page का access नहीं है।');
        }

        return $next($request);
    }
}
