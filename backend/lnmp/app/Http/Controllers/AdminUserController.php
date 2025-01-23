<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminUser;

class AdminUserController extends Controller
{
    // 查詢所有用戶
    public function index()
    {
        $users = AdminUser::all();
        return response()->json($users, 200);
    }

    // 顯示單一用戶
    public function show($id)
    {
        $user = AdminUser::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user, 200);
    }

    // 新增用戶
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = AdminUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    // 更新用戶
    public function update(Request $request, $id)
    {
        $user = AdminUser::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
            'role' => 'nullable|string|max:255',
        ]);

        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'role' => $request->role ?? $user->role,
        ]);

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }


    // 刪除用戶
    public function destroy($id)
    {
        $user = AdminUser::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
