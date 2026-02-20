<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // belum login
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // pastikan user ada field "role" dalam database
        if (!isset($user->role)) {
            return response()->json(['message' => 'Role not set for this user.'], 403);
        }

        // check role
        if (!in_array($user->role, $roles)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $next($request);
    }
}
