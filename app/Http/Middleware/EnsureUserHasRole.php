<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        // The route supplies this value, for example role:ADMIN.
        $requiredRole = UserRole::tryFrom(strtoupper($role));

        if (! $requiredRole || $user->role !== $requiredRole) {
            return response()->json([
                'message' => 'You are not authorized to perform this action.',
            ], 403);
        }

        return $next($request);
    }
}
