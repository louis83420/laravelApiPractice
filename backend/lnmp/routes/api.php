<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ApiFieldController;
use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\api\AdminUserController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\TestAuthController;

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
Route::middleware('auth:sanctum')->group(function () {
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

// API Account 專用路由，只能使用 index 和 store 方法
Route::middleware(['auth:api_account', 'scopes:read,write'])->group(function () {
    Route::apiResource('users/out', AdminUserController::class);
    Route::apiResource('products', ProductController::class);
    // Route::get('api_users', [AdminUserController::class, 'index']);
});

