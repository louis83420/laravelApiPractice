<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ApiFieldController;
use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\OAuthClientController;
use App\Http\Controllers\CustomAuthorizationController;
use Laravel\Passport\Http\Controllers\{
    AuthorizationController,
    ApproveAuthorizationController,
    DenyAuthorizationController

};



//測試
Route::get('/test', [ApiTestController::class, 'apitest']);

//商品
// Route::apiResource('products', ProductController::class);

//測試用小工具拿資料表
Route::get('/get-fields', [ApiFieldController::class, 'getFields']);

//用戶
// Route::apiResource('users', UserController::class);

// 消費者登入
Route::post('/login', [AuthController::class, 'login']);

// 管理員登入
Route::post('/admin/login', [AdminAuthController::class, 'loginAdmin']);

// 一般用戶路由（使用 Sanctum）
Route::middleware('auth:users')->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'update']);
});

// 管理員路由（使用 Passport）
Route::middleware(['auth:admin', 'scope:admin'])->group(function () {
    Route::apiResource('users', AdminUserController::class);
    Route::apiResource('products', ProductController::class);
    // Route::get('api_users', [AdminUserController::class, 'index']);
});


Route::post('/oauth/token', [\Laravel\Passport\Http\Controllers\AccessTokenController::class, 'issueToken']);

//API Account 專用路由，只能使用 index 和 store 方法

Route::middleware(['auth.admin'])->group(function () {
    // 針對有 'read' scope 的操作
    Route::middleware('client_scope:read')->group(function () {
        Route::get('/apiuser', [AdminUserController::class, 'index']);
        Route::get('/apiuser/{id}', [AdminUserController::class, 'show']);

        Route::get('/productsout', [ProductController::class, 'index']);
        Route::get('/productsout/{id}', [ProductController::class, 'show']);
    });

    // 針對有 'write' scope 的操作(Client Credentials Grant)
    Route::middleware('client_scope:write')->group(function () {
        Route::post('/apiuser', [AdminUserController::class, 'store']);
        Route::post('/productsout', [ProductController::class, 'store']);
    });
});

Route::get('/export-users', [AdminUserController::class, 'export']);
Route::post('/import-users', [AdminUserController::class, 'import']);



Route::middleware(['auth.admin'])->group(function () {
    Route::get('/useronly', [UserController::class, 'show']);
    Route::put('/useronly', [UserController::class, 'update']);

    Route::get('/productsouts', [ProductController::class, 'index']);
    Route::get('/productsouts/{id}', [ProductController::class, 'show']);
    });



Route::middleware('auth:admin')->group(function () {
    Route::post('/oauth/clients', [OAuthClientController::class, 'store']);
});


Route::post('/auth/callback', [AuthController::class, 'handleProviderCallback']);


// OAuth 核心路由
Route::group(['prefix' => 'oauth'], function () {
    

    Route::post('/authorize', [ApproveAuthorizationController::class, 'approve'])
          ->middleware('auth:custom_guard');

    Route::delete('/authorize', [DenyAuthorizationController::class, 'deny'])
          ->middleware('auth:custom_guard');

    Route::get('/authorize', [CustomAuthorizationController::class, 'authorize'])
            ->middleware(['api', 'passport:custom_guard']);

    Route::post('/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
});

// 第三方客戶端註冊 API
Route::post('/clients', [OAuthClientController::class, 'store'])
     ->middleware('auth:custom_guard');
