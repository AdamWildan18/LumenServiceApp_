<?php

namespace App\Http\Middleware;

use Closure;

class ValidateInputMiddleware
{
    public function handle($request, Closure $next)
    {
        $credentials = $request->json()->all();

        if (!isset($credentials['username']) || !isset($credentials['password']) ||
            $credentials['username'] !== 'admin' || $credentials['password'] !== 'password') {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}

