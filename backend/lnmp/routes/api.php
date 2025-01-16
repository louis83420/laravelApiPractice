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
// Route::apiResource('products', ProductController::class);

//測試用小工具拿資料表
Route::get('/get-fields', [ApiFieldController::class, 'getFields']);

//用戶
// Route::apiResource('users', UserController::class);

// 消費者登入
Route::post('/login', [AuthController::class, 'login']);

// 管理員登入
Route::post('/admin/login', [AuthController::class, 'loginAdmin']);

// 一般用戶，使用 Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'show']); // 取得自己的資料
});

// 管理員，使用 Passport
Route::middleware(['auth:api', 'scope:admin'])->group(function () {
    Route::apiResource('users', UserController::class); // 管理用戶資料
    Route::apiResource('products', ProductController::class); // 管理商品資料
});
