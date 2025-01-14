<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // 測試 API
    public function test()
    {
        return response()->json([
            'message' => 'API is working!',
            'status' => 200
        ]);
    }

    // 取得使用者資訊的 API
    public function getUser($id)
    {
        return response()->json([
            'id' => $id,
            'name' => 'John Doe',
            'email' => 'johndoe@example.com'
        ]);
    }
}
