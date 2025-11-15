<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSession
{
   public function handle(Request $request, Closure $next)
{
    if (! auth()->check()) {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Please login first.'], 401);
        }
        return redirect()->route('login')->with('error', 'Please login first.');
    }

    // Optional: keep legacy session('user') in sync
    // session(['user' => auth()->user()]);

    return $next($request);
}

}
