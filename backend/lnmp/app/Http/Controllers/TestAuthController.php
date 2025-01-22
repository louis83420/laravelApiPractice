<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\ClientRepository;
use Lcobucci\JWT\Parser as JwtParser;

class TestAuthController extends Controller
{
    protected $tokenRepository;
    protected $clientRepository;

    public function __construct(TokenRepository $tokenRepository, ClientRepository $clientRepository)
    {
        $this->tokenRepository = $tokenRepository;
        $this->clientRepository = $clientRepository;
    }

    public function test(Request $request)
    {
        $token = $request->bearerToken();

        // 解析 token
        try {
            $tokenId = (new \Lcobucci\JWT\Parser())->parse($token)->claims()->get('jti');
            $accessToken = $this->tokenRepository->find($tokenId);

            $client = null;
            if ($accessToken) {
                $client = $this->clientRepository->find($accessToken->client_id);
            }

            return response()->json([
                'is_authenticated' => !empty($accessToken),
                'token_exists' => !empty($token),
                'token' => $token,
                'client' => $client,
                'token_info' => $accessToken ? [
                    'client_id' => $accessToken->client_id,
                    'user_id' => $accessToken->user_id,
                    'scopes' => $accessToken->scopes,
                    'expires_at' => $accessToken->expires_at,
                ] : null
            ]);

        } catch (\Exception $e) {
            \Log::error('Token validation error: ' . $e->getMessage());

            return response()->json([
                'is_authenticated' => false,
                'token_exists' => !empty($token),
                'token' => $token,
                'error' => $e->getMessage()
            ]);
        }
    }
}
