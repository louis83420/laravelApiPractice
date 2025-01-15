<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getAdminData()
    {
        return response()->json([
            'message' => '這是管理員專屬的資料。',
        ]);
    }
}
