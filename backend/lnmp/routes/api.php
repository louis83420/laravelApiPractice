<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ApiFieldController;
use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;


//測試
Route::get('/test', [ApiTestController::class, 'apitest']);

//商品
Route::apiResource('products', ProductController::class);

//測試用小工具拿資料表
Route::get('/get-fields', [ApiFieldController::class, 'getFields']);

//用戶
Route::apiResource('users', UserController::class);

// 消費者登入
Route::post('/login', [AuthController::class, 'login']);

// 管理員登入
Route::post('/admin/login', [AuthController::class, 'loginAdmin']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('users', UserController::class);
});


Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/data', [AdminController::class, 'getAdminData']);
});
