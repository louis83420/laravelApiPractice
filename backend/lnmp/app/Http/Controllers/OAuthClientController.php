<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class OAuthClientController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'redirect_uri' => 'required|url',
        ]);

        // 建立 OAuth Client
        $client = Client::create([
            'user_id' => auth()->id(),  // 綁定開發者帳戶
            'name' => $request->name,
            'secret' => Str::random(40),
            'redirect' => $request->redirect_uri,
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
        ]);

        return response()->json([
            'client_id' => $client->id,
            'client_secret' => $client->secret,
        ], 201);
    }
}
