<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class authJWT
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            // 如果用户登陆后的所有请求没有jwt的token抛出异常
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response() -> json([
                    'status' => 'failed',
                    'errors' => [
                        'status_code' => 401,
                        'message' => 'Token 无效',
                    ]
                ]);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response() -> json([
                    'status' => 'failed',
                    'errors' => [
                        'status_code' => 401,
                        'message' => 'Token 已过期',
                    ]
                ]);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
                return response() -> json([
                    'status' => 'failed',
                    'errors' => [
                        'status_code' => 401,
                        'message' => 'Token 错误',
                    ]
                ]);
            }else {
                return response() -> json([
                    'status' => 'failed',
                    'errors' => [
                        'status_code' => 401,
                        'message' => '该用户不存在',
                    ]
                ]);
            }
        }

        //传递参数
        $request->attributes->add(compact('user'));

        return $next($request);
    }
}
