<?php
/**
 * Created by PhpStorm.
 * User: yuming
 * Date: 16/11/8
 * Time: 下午9:57
 */

namespace App\Api\Controllers;

use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    private $user;

    public function __construct(User $user)
    {
        $this -> user = $user;
    }

    //登录
    public function login(Request $request)
    {
        $form_data = $request->only('email', 'password');

        //验证
        $rules = [
            'email' => 'required|email|max:30',
            'password' => 'required|max:20'
        ];
        $message = [
            'email.required' => '请填写邮箱!',
            'email.email' => '邮箱格式不正确!',
            'email.max' => '邮箱不正确!',
            'password.required' => '请填写密码!',
            'password.max' => '密码有误!',
        ];
        $validator = Validator::make($form_data, $rules, $message);
        //表单验证
        if($validator -> passes()){

            $user = $this -> user -> select(['id', 'name', 'email', 'password']) -> where('email', $form_data['email']) -> first();

            //验证用户
            if(empty($user)){
                return $this->setStatusCode(400)->responseError('没有该用户!');
            }
            //验证用户密码
            if(!Hash::check($form_data['password'], $user -> password)){
                return $this->setStatusCode(400)->responseError('密码错误!');
            }

            try {
                $token=JWTAuth::fromUser($user);
            } catch (JWTException $e) {
                return $this->setStatusCode(400)->responseError('could_not_create_token!');
            }
            unset($user->password);
            return $this->responseSuccess('登录成功!', [
                'token' => $token,
                'user' => $user,
            ]);
        }

        return $this->setStatusCode(400)->responseError($validator->errors()->first());
    }
    
    //注册
    public function register(Request $request)
    {
        //获取数据
        $form_data = $request->all();

        //验证
        $rules = [
            'name' => 'required|max:30',
            'email' => 'required|email|max:30|unique:users',
            'password' => 'required|max:20'
        ];
        $message = [
            'name.required' => '请填写昵称!',
            'name.max' => '昵称过长!',
            'email.required' => '请填写邮箱!',
            'email.email' => '邮箱格式不正确!',
            'email.max' => '邮箱过长!',
            'email.unique' => '邮箱已存在!',
            'password.required' => '请填写密码!',
            'password.max' => '密码过长!',
        ];

        $validator = Validator::make($form_data, $rules, $message);
        //验证表单
        if($validator -> passes()){
            $form_data['password'] = bcrypt($form_data['password']);
            $user = $this -> user -> create($form_data);
            if(!empty($user)) {
                return $this->responseSuccess('注册成功!');
            }
        }
        return $this->setStatusCode(400)->responseError($validator->errors()->first());
    }

    //获取用户信息
    public function info(Request $request)
    {
        return $this->responseSuccess('获取成功!', [
            'user' => $request->get('user'),
        ]);
    }

    //刷新token
    public function refreshToken(Request $request)
    {
        try {

            $old_token = JWTAuth::getToken();
            $token = JWTAuth::refresh($old_token);
            JWTAuth::invalidate($old_token);

        } catch (TokenExpiredException $e) {

            return $this->setStatusCode(401)->responseError('token失效!');

        } catch (JWTException $e) {

            return $this->setStatusCode(401)->responseError('token无效!');

        }

        return $this->responseSuccess('获取成功!', [
            'token' => $token,
        ]);
    }

}