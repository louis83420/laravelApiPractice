<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use Laravel\Sanctum\HasApiTokens;

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
    public function authenticateToken()
    {
        \Log::channel('passport')->info('Token Debug', [
            'token' => request()->bearerToken(),
            'guard' => 'api_account'
        ]);
    }

    public function handleProviderCallback(Request $request)
    {
        // 確保有收到 `code`
        if (!$request->has('code')) {
            return response()->json(['error' => 'Authorization code is missing'], 400);
        }

        // 交換 Access Token
        $response = Http::asForm()->post('http://localhost/oauth/token', [
            'grant_type'    => 'authorization_code',
            'client_id'     => env('OAUTH_CLIENT_ID'),
            'client_secret' => env('OAUTH_CLIENT_SECRET'),
            'redirect_uri'  => 'http://localhost/auth/callback',
            'code'          => $request->code,
        ]);

        return response()->json($response->json());
    }

}
