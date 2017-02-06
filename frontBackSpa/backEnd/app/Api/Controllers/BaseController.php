<?php
/**
 * Created by PhpStorm.
 * User: yuming
 * Date: 16/11/8
 * Time: 下午9:22
 */

namespace App\Api\Controllers;


use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Response;

class BaseController extends Controller
{
    use Helpers;

    protected $statusCode = '200'; //请求状态

    //获取状态
    protected function getStatusCode()
    {
        return $this -> statusCode;
    }
    //设置状态
    protected function setStatusCode($statusCode)
    {
        $this -> statusCode = $statusCode;
        return $this;
    }
    //输出失败状态
    protected function responseError($message)
    {
        return $this -> response([
            'status' => 'failed',
            'errors' => [
                'status_code' => $this -> getStatusCode(),
                'message' => $message,
            ]
        ]);
    }
    //输出成功状态
    protected function responseSuccess($message, $data = [])
    {
        return $this -> response([
            'status' => 'successful',
            'correct' => [
                'status_code' => $this -> getStatusCode(),
                'message' => $message,
                'data' => $data,
            ]
        ]);
    }
    //返回请求结果
    protected function response($data)
    {
        return Response::json($data);
    }

}