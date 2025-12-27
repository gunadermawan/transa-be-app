<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Allowed role IDs (e.g., '1', '2', '3')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (! $request->user()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Check if user's role is in allowed roles
        if (! in_array((string) $request->user()->role_id, $roles)) {
            return response()->json([
                'message' => 'Unauthorized - Insufficient permissions',
            ], 403);
        }

        return $next($request);
    }
}
