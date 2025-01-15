<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // 1. 取得所有使用者 (GET /api/users)
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    // 2. 建立新使用者 (POST /api/users)
    public function store(Request $request)
    {
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
        return response()->json($user);
    }

    // 4. 更新使用者 (PUT/PATCH /api/users/{id})
    public function update(Request $request, User $user)
    {
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

    // 5. 刪除使用者 (DELETE /api/users/{id})
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}
