<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
    //
    /**
     * 引入中间件
     * except 指定名称不过滤,优先使用
     * only指定名称过滤,不安全
     */
    public function __construct()
    {
        $this->middleware('auth', [
            'except'=>['show','create','store','index']
        ]);
    }

    public function index()
    {
        //$users=User::all(); 全部
        $users=User::paginate(10);
        return view('users/index', compact('users'));
    }
    public function create()
    {
        return view('users.create');
    }
    public function show(User $user)
    {
        return view('users.show', compact('user'));
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
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'=>'required|min:3|max:50',
            'email'=>'required|email|unique:users|max:255',
            'password'=>'required|confirmed|min:6',
        ]);
        $user=User::create([
          'name'=>$request->name,
          'email'=>$request->email,
          'password'=>bcrypt($request->password),
      ]);
        Auth::login($user);//注册后自动登陆
        session()->flash('success', '欢迎,您将开启一段全新的旅程');
        return redirect()->route('users.show', [$user]);
    }


    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }
    public function update(User $user, Request $request)
    {
        $this->validate($request, [
                'name'=>'required|max:50|min:3',
                'password'=>'nullable|confirmed|min:6',
            ]);
        $this->authorize('update', $user);
        $data=[];
        $data['name']=$request->name;
        if ($request->password) {
            $data['password']=$request->password;
        }
        $user->update($data);
        session()->flash('success', '个人资料更新成功');
        return redirect()->route('users.show', $user->id);
    }


    public function destroy(User $user){
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','成功删除用户'.$user->name);
        return back();
    }
}
