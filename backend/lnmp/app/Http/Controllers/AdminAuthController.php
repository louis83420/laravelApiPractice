<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;


class AdminAuthController extends Controller
{
    // 管理員登入
    public function loginAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 查找管理員
        $admin = AdminUser::where('email', $request->email)
                      ->where('role', 'admin')
                      ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['error' => '管理員登入失敗，請檢查帳號或密碼'], 401);
        }

        // 刪除舊的 Token（避免多重登入）
        $admin->tokens()->delete();

        // 使用 Passport 建立 Token 並設定範圍
        $token = $admin->createToken('admin-token', ['admin'])->accessToken;

        return response()->json(['access_token' => $token], 200);
    }
}
