<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    //
    public function create(){
        return view('users.create');
    }
    public function show(User $user){
        return view('users.show',compact('user'));
    }
    /**
     * POST接受数据验证
     * required 存在性验证,也就是不能为空
     * min,max 长度验证,要在这个区间
     * email 格式验证,比附符合email格式
     * unique :users唯一性验证:表名
     * confirmed 重复输入匹配验证
     * @param Request $request
     * @param Request Illuminate\Http\Requesc实例参数
     * @return void
     */
    public function store(Request $request){
        $this->validate($request,[
            'name'=>'required|min:3|max:50',
            'email'=>'required|email|unique:users|max:255',
            'password'=>'required|confirmed|min:6',
        ]);
      $user=User::create([
          'name'=>$request->name,
          'email'=>$request->email,
          'password'=>bcrypt($request->password),
      ]);
      session()->flash('success','欢迎,您将开启一段全新的旅程');
      return redirect()->route('users.show',[$user]);
    }
}
