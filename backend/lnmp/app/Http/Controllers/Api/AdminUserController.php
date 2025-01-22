<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminUser;



class AdminUserController extends Controller
{
    // 1. 取得所有使用者資料 (僅限管理員)
    public function index(Request $request)
    {


        $users = AdminUser::all();
        return response()->json($users);
    }

    // 2. 建立新使用者 (僅限管理員)
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);


        $user = AdminUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json($user, 201);
    }

    // 3. 取得單一使用者資料 (僅限管理員)
    public function show(Request $request, AdminUser $user)
    {
        return response()->json($user);
    }

    // 4. 更新使用者資料 (僅限管理員)
    public function update(Request $request, AdminUser $user)
    {


        // 驗證輸入
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
            'email_verified_at' => 'nullable|date',
            'remember_token' => 'nullable|string|max:100',
            'role' => 'nullable|string|max:255',
        ]);

        // 如果有提供 password，進行加密處理
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        // 更新用戶資料
        $user->update($validated);

        return response()->json($user);
    }

    // 5. 刪除使用者 (僅限管理員)
    public function destroy(Request $request, AdminUser $user)
    {


        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
