<?php

namespace App\Http\Middleware;

use Closure;
use Laravel\Passport\TokenRepository;

class CheckClientScope
{
    public function handle($request, Closure $next, ...$scopes)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // 提取 Payload 並解碼
        $segments = explode('.', $token);
        if (count($segments) !== 3) {
            return response()->json(['message' => 'Invalid Token.'], 401);
        }

        $payload = json_decode(base64_decode(strtr($segments[1], '-_', '+/')), true);
        $jti = $payload['jti'] ?? null;

        if (!$jti) {
            return response()->json(['message' => 'Missing jti in token'], 401);
        }

        // 查詢資料表
        $tokenRepository = app(TokenRepository::class);
        $accessToken = $tokenRepository->find($jti);

        if (!$accessToken || $accessToken->revoked) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // 驗證 Scope
        foreach ($scopes as $scope) {
            if (!in_array($scope, $accessToken->scopes)) {
                return response()->json(['message' => "Missing required scope: {$scope}"], 403);
            }
        }

        return $next($request);
    }
}




