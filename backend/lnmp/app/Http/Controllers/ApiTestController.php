<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiTestController extends Controller
{
    public function apitest(Request $request)
    {
        return response()->json([
            'message' => 'API is working!',
            'status' => 200
        ]);
    }
}
