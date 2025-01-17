<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    // 消費者登入
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 查找用戶
        $user = User::where('email', $request->email)->first();

        // if (!$user || !Hash::check($request->password, $user->password)) {
        //     return response()->json(['error' => '登入失敗，請檢查帳號或密碼'], 401);
        // }
        if (!$user) {
            Log::error('User not found', ['email' => $request->email]);
            return response()->json(['error' => '帳號不存在'], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            Log::error('Password mismatch', ['email' => $request->email]);
            return response()->json(['error' => '密碼錯誤'], 401);
        }


        // 建立 Token
        $token = $user->createToken('consumer-token')->plainTextToken;

        return response()->json(['access_token' => $token], 200);
    }


}
