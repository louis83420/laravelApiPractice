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
        if (!$request->user()->tokenCan('admin') && !$request->user()->tokenCan('read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $users = AdminUser::all();
        return response()->json($users);
    }

    // 2. 建立新使用者 (僅限管理員)
    public function store(Request $request)
    {
        if (!$request->user()->tokenCan('admin') && !$request->user()->tokenCan('write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admin_users',
            'password' => 'required|string|min:6',
        ]);

        $user = AdminUser::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return response()->json($user, 201);
    }

    // 3. 取得單一使用者資料 (僅限管理員)
    public function show(Request $request, AdminUser $adminUser)
    {
        if (!$request->user()->tokenCan('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($adminUser);
    }

    // 4. 更新使用者資料 (僅限管理員)
    public function update(Request $request, AdminUser $adminUser)
    {
        if (!$request->user()->tokenCan('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:admin_users,email,' . $adminUser->id,
            'password' => 'sometimes|string|min:6',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $adminUser->update($validated);

        return response()->json($adminUser);
    }

    // 5. 刪除使用者 (僅限管理員)
    public function destroy(Request $request, AdminUser $adminUser)
    {
        if (!$request->user()->tokenCan('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $adminUser->delete();
        return response()->json(null, 204);
    }
}
