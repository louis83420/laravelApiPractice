<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Laravel\Passport\Http\Controllers\AuthorizationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


// routes/web.php
Route::group(['middleware' => ['web']], function () {
    Route::get('/oauth/authorize', [AuthorizationController::class, 'authorize']);
    Route::post('/oauth/authorize', [AuthorizationController::class, 'authorize']);
});

