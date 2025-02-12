<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminUser;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Log;

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

    // 下載用戶excel
    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function import(Request $request)
    {
        // 驗證檔案
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        // 執行匯入
        try {
            Excel::import(new UsersImport, $request->file('file'));
            return response()->json(['message' => '匯入成功'], 200);
        } catch (\Exception $exception) { // 用更清楚的名稱
            Log::error('匯入失敗: ' . $exception->getMessage());

            return response()->json([
                'error' => '匯入失敗，請檢查 CSV 格式或聯繫管理員。',
                'details' => $exception->getMessage(),
            ], 500);
        }

    }

}
