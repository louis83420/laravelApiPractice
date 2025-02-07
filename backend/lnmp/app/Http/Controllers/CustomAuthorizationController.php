<?php

namespace App\Http\Controllers\OAuth;

use Laravel\Passport\Http\Controllers\AuthorizationController as BaseController;
use Laravel\Passport\Bridge\User;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomAuthorizationController extends BaseController
{
    /**
     * 自定义授权验证流程
     */
    public function authorize(ServerRequestInterface $psrRequest, Request $request)
    {
        try {
            // 验证基础参数
            $validator = Validator::make($request->all(), [
                'client_id' => 'required|uuid',
                'redirect_uri' => 'required|url',
                'response_type' => 'required|in:code',
                'scope' => 'sometimes|string',
                'state' => 'required|string|min:10',
                'code_challenge' => 'required|string|min:43',
                'code_challenge_method' => 'required|in:S256'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($request, 'invalid_request', $validator->errors()->first());
            }

            // 获取并验证客户端
            $client = app(ClientRepository::class)->findActive($request->client_id);

            if (!$client || !$this->validateRedirectUri($client, $request->redirect_uri)) {
                return $this->errorResponse($request, 'invalid_client', '客户端验证失败');
            }

            // 验证 PKCE 参数
            if (empty($request->code_challenge) || !in_array($request->code_challenge_method, ['S256'])) {
                return $this->errorResponse($request, 'invalid_request', '缺少 PKCE 参数');
            }

            // 检查用户认证状态
            if (!auth('api')->check()) {
                return response()->json([
                    'error' => 'unauthenticated',
                    'login_url' => config('app.frontend_url').'/login'
                ], 401);
            }

            // 执行标准授权流程
            $authRequest = $this->withErrorHandling(function () use ($psrRequest) {
                return $this->server->validateAuthorizationRequest($psrRequest);
            });

            // 自动批准授权请求
            return $this->approveRequest($authRequest, auth('api')->user());

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * 验证重定向URI
     */
    private function validateRedirectUri($client, $redirectUri): bool
    {
        $allowedUris = is_array($client->redirect)
                      ? $client->redirect
                      : explode(',', $client->redirect);

        return in_array($redirectUri, $allowedUris);
    }

    /**
     * 统一错误响应
     */
    private function errorResponse(Request $request, string $errorType, string $description)
    {
        $redirect = $request->redirect_uri.'?'.http_build_query([
            'error' => $errorType,
            'error_description' => $description,
            'state' => $request->state
        ]);

        return redirect()->away($redirect);
    }

    /**
     * 覆盖授权批准逻辑
     */
    protected function approveRequest($authRequest, $user)
    {
        $authRequest->setUser(new User($user->getAuthIdentifier()));
        $authRequest->setAuthorizationApproved(true);

        return $this->withErrorHandling(function () use ($authRequest) {
            return $this->convertResponse(
                $this->server->completeAuthorizationRequest($authRequest, new \Nyholm\Psr7\Response)
            );
        });
    }
}
