<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use illuminate\Support\Facades\Log;

class ApilogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        Log::channel('daily')->info("API Request",[
            'url' => $request ->fullUrl(),
            'method' => $request ->method(),
            'params' => $request ->all(),
            'ip' => $request->header('X-Forwarded-For') ?? $request->ip(),
            'time' => now()->toDateTimeString(),
        ]);
        $response = $next($request);

        $executeTime = microtime(true) - $startTime;
        Log::channel('daily')->info('API Response',[
            'status' => $response->getStatusCode(),
            'excuteTime' => number_format($executeTime, 3).' second',
        ]);
        return $next($request);
    }
}
