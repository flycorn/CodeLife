<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    $api->group(['namespace' => 'App\Api\Controllers', 'middleware' => ['api','cors']], function ($api) {

        //注册
        $api->post('user/register', 'AuthController@register');
        //登录
        $api->post('user/login', 'AuthController@login');

        //需授权
        $api->group(['middleware' => 'jwt.api'], function($api){

            //获取用户信息
            $api->post('user/info', 'AuthController@info');

            //刷新token
            $api->post('user/refreshToken', 'AuthController@refreshToken');

            //列表
            $api->post('/todo', 'TodoController@getList');

            //创建
            $api->post('/todo/create', 'TodoController@create');

            //详情
            $api->post('/todo/{id}', 'TodoController@getTodo');

            //更改状态
            $api->patch('/todo/{id}/completed', 'TodoController@completed');

            //删除
            $api->post('/todo/{id}/delete', 'TodoController@delete');

        });

    });

});
