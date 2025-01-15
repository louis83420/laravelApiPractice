<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * 全域中介層陣列，適用於每個請求。
     *
     * @var array
     */
    protected $middleware = [
        // 添加 CORS 的處理
        \Fruitcake\Cors\HandleCors::class,

        // 其他預設的中介層
        \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];
    protected $routeMiddleware = [
        // 其他 Middleware
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'auth:api' => \Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
    ];

}
