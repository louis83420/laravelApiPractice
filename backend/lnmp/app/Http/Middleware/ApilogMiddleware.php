<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

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

        // 記錄 API 請求
        Log::channel('daily')->info("API Request", [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'params' => $request->all(),
            'ip' => $request->header('X-Forwarded-For') ?? $request->ip(),
            'time' => now()->toDateTimeString(),
        ]);

        // 執行請求並取得回應
        $response = $next($request);

        // 記錄 API 回應
        $executeTime = microtime(true) - $startTime;
        Log::channel('daily')->info('API Response', [
            'status' => $response->getStatusCode(),
            'excuteTime' => number_format($executeTime, 3) . ' second',
        ]);

        return $response; // 只返回一次 $response
    }
}
