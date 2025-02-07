<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ResponseFactory
{
    /**
     * 回應成功
     */
    public function success($data = null, $msg = 'success', $code = 200): JsonResponse
    {
        return $this->response($data, $msg, $code);
    }

    /**
     * 回應失敗
     */
    public function fail($data = null, $msg = 'fail', $code = 400): JsonResponse
    {
        return $this->response($data, $msg, $code);
    }

    /**
     * 驗證錯誤回應
     */
    public function validator($errors, $code = 422): JsonResponse
    {
        return $this->response($errors, '驗證失敗', $code);
    }

    /**
     * 系統錯誤回應
     */
    public function error($msg = 'Server Error', $code = 500): JsonResponse
    {
        return $this->response(null, $msg, $code);
    }

    /**
     * 通用回應格式
     */
    private function response($data, $msg, $code): JsonResponse
    {
        return response()->json([
            'status' => $code === 200,
            'code' => $code,
            'message' => $msg,
            'data' => $data,
            'timestamp' => now()->timestamp
        ], $code);
    }
}
