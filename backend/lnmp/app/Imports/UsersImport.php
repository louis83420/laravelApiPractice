<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class UsersImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // 檢查 email 是否已存在
        if (User::where('email', $row['email'])->exists()) {
            Log::warning("Duplicate email found: " . $row['email']); // 記錄重複 email
            return null; // 跳過這筆資料
        }

        return new User([
            'name'              => $row['name'],                // 使用者名稱
            'email'             => $row['email'],               // Email
            'email_verified_at' => $row['email_verified_at'] ?? null, // 可選欄位
            'password'          => bcrypt($row['password']),    // 加密密碼
            'remember_token'    => $row['remember_token'] ?? null, // 可選欄位
            'role'              => $row['role'] ?? 'user',      // 預設角色為 "user"
        ]);

    }
}
