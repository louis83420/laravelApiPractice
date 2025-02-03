<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\User;

class UsersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::select('id', 'name', 'email', 'email_verified_at', 'password', 'remember_token', 'created_at', 'updated_at', 'role')->get();
    }

    public function headings(): array
    {
        return [
            "id",
            "name",
            "email",
            "email_verified_at",  // ✅ 加入 email_verified_at
            "password",           // ✅ 加入 password
            "remember_token",     // ✅ 加入 remember_token
            "created_at",
            "updated_at",
            "role"
        ];
    }
}

