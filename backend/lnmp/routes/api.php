<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ProductController;



// 簡單的測試 API
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'status' => 200
    ]);
});

// 建立一個取得使用者資訊的 API
Route::get('/user/{id}', function ($id) {
    return response()->json([
        'id' => $id,
        'name' => 'John Doe',
        'email' => 'johndoe@example.com'
    ]);
});

//測試
Route::get('/test', [UserController::class, 'test']);
Route::get('/user/{id}', [UserController::class, 'getUser']);


//商品
Route::apiResource('products', ProductController::class);

//登入
Route::post('/login', [AuthController::class, 'login']);

