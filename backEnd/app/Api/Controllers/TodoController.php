<?php

namespace App\Api\Controllers;

use App\User;
use App\Todo;
use Illuminate\Http\Request;

class TodoController extends BaseController
{
    private $user;
    private $todo;

    public function __construct(User $user, Todo $todo)
    {
        $this -> user = $user;
        $this -> todo = $todo;
    }

    //获取列表
    public function getList(Request $request)
    {
        $todos = $request->get('user')->getTodoList()->get();

        return $this->responseSuccess('获取成功!', [
            'todos' => $todos,
        ]);
    }

    //获取详情
    public function getTodo(Request $request, $id)
    {
        $user = $request->get('user');
        $todo = $this->todo->find($id);

        if(empty($todo) && $user->id != $todo->user_id){
            return $this->setStatusCode(400)->responseError('数据不存在!');
        }

        return $this->responseSuccess('获取成功!', [
            'todo' => $todo,
        ]);
    }

    //创建
    public function create(Request $request)
    {
        $user = $request->get('user');
        $data = ['user_id' => $user->id, 'title' => $request->get('title'), 'completed' => 0];
        $todo = $this->todo->create($data);

        if(!empty($todo)){
            return $this->responseSuccess('添加成功!', [
                'todo' => $todo,
            ]);
        }
        return $this->setStatusCode(400)->responseError('添加失败!');
    }

    //标记
    public function completed(Request $request, $id)
    {
        $user = $request->get('user');
        $todo = $this->todo->find($id);

        if(empty($todo) && $user->id != $todo->user_id){
            return $this->setStatusCode(400)->responseError('数据不存在!');
        }

        $todo->completed = ! $todo->completed;
        $res = $todo->save();

        if(!$res){
            return $this->setStatusCode(400)->responseError('标记失败!');
        }
        return $this->responseSuccess('标记成功!', [
            'todo' => $todo,
        ]);
    }

    //删除
    public function delete(Request $request, $id)
    {
        $user = $request->get('user');
        $todo = $this->todo->find($id);

        if(empty($todo) && $user->id != $todo->user_id){
            return $this->setStatusCode(400)->responseError('数据不存在!');
        }

        $res = $todo->delete();

        if(!$res){
            return $this->setStatusCode(400)->responseError('删除失败!');
        }
        return $this->responseSuccess('删除成功!', [
            'todo' => $todo,
        ]);
    }

}
