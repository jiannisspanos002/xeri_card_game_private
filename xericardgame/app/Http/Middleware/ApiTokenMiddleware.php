<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class ApiTokenMiddleware
{
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');

        if (!$token || !str_starts_with($token, 'Bearer ')) {
            return response()->json(['error' => 'Missing token'], 401);
        }

        $token = substr($token, 7);

        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $request->merge(['auth_user' => $user]);

        return $next($request);
    }
}
