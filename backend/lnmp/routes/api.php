<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ApiFieldController;
use App\Http\Controllers\ApiTestController;



//測試
Route::get('/test', [ApiTestController::class, 'apitest']);
Route::get('/user/{id}', [UserController::class, 'getUser']);


//商品
Route::apiResource('products', ProductController::class);




Route::get('/get-fields', [ApiFieldController::class, 'getFields']);


