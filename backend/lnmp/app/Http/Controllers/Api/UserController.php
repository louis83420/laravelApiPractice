<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // 1. 取得所有使用者 (GET /api/users) - 僅限管理員 (admin scope)
    public function index()
    {
        // 確認 token 是否包含 'admin' scope
        if (!Auth::user()->tokenCan('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $users = User::all();
        return response()->json($users);
    }

    // 2. 建立新使用者 (POST /api/users) - 僅限管理員 (admin scope)
    public function store(Request $request)
    {
        // 確認 token 是否包含 'admin' scope
        if (!Auth::user()->tokenCan('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return response()->json($user, 201);
    }

    // 3. 取得單一使用者 (GET /api/users/{id})
    public function show(User $user)
    {
        $currentUser = Auth::user();

        // 如果 token 有 'admin' scope，允許查看任何用戶
        if ($currentUser->tokenCan('admin')) {
            return response()->json($user);
        }

        // 如果沒有 'admin' scope，僅允許查看自己的資料
        if ($currentUser->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($user);
    }

    // 4. 更新使用者 (PUT/PATCH /api/users/{id})
    public function update(Request $request, User $user)
    {
        $currentUser = Auth::user();

        // 管理員可以更新任何用戶，普通用戶只能更新自己的資料
        if (!$currentUser->tokenCan('admin') && $currentUser->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    // 5. 刪除使用者 (DELETE /api/users/{id}) - 僅限管理員 (admin scope)
    public function destroy(User $user)
    {
        // 確認 token 是否包含 'admin' scope
        if (!Auth::user()->tokenCan('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user->delete();
        return response()->json(null, 204);
    }
}
