<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AuthV2Controller extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 使用 Guzzle 向 Passport 發送請求
        $http = new Client();

        try {
            $response = $http->post(config('app.url') . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
                    'client_secret' => env('PASSPORT_PASSWORD_CLIENT_SECRET'),
                    'username' => $request->email,
                    'password' => $request->password,
                    'scope' => '',
                ],
            ]);

            return response()->json(json_decode((string)$response->getBody(), true), 200);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            // 打印完整的錯誤信息
            return response()->json([
                'error' => $e->getMessage(),
                'response' => json_decode((string) $e->getResponse()->getBody(), true),
            ], $e->getCode());
        }

    }
}
