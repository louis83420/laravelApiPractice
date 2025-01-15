<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->tokenCan('admin')) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}