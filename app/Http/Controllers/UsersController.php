<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;
use App\Models\Status;
class UsersController extends Controller
{
    //
    /**
     * 引入中间件
     * except 指定名称不过滤,优先使用(黑名单)
     * only指定名称过滤,不安全(白名单)
     */
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index','confirmEmail']
        ]);
    }


    /**
     * 激活邮箱
     *
     * @param [type] $token
     * @return void
     */
    public function confirmEmail($token){
        $user=User::where('activation_token',$token)->firstOrFail();
        /**
         * Eloquent就是User
         * where方法接受两个参数
         * 第一个参数为要查找的的字符名称,
         * 第二个参数为对应的值
         * 查询返回结果是数组,
         * firstOrFail方法来获取查询结果的第一个用户,如果查询不到,返回404页面
         * 
         */
        $user->activated=true;//修改用户激活状态为true
        $user->activation_token=null;//修改激活令牌为空
        $user->save();//保存修改

        Auth::login($user);//登陆激活用户
        session()->flash('success','恭喜你,激活成功');
        return redirect()->route('users.show',[$user]);//跳转
    }
    /**
     * 首页
     *
     * @return void
     */
    public function index()
    {
        //$users=User::all(); 全部
        $users = User::paginate(10);
        return view('users/index', compact('users'));
    }
    /**
     * 注册视图
     *
     * @return void
     */
    public function create()
    {
        return view('users.create');
    }
    public function show(User $user)
    {   
        $statuses=$user->statuses()
                    ->orderBy('created_at','desc') //查询 倒叙
                    ->paginate(30);//分页30
      
               return view('users.show', compact('user','statuses'));
    }
    /**
     *
     * POST接收注册数据验证
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
            'name' => 'required|min:3|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        // Auth::login($user);//注册后自动登陆
        $this->sendEmailConfirmationTo($user);
        //session()->flash('success', '欢迎,您将开启一段全新的旅程');
        session()->flash('success', '验证右键已发送到你的注册邮箱上,请注意查收');
        return redirect()->route('users.show', [$user]);
    }

    /**
     * 编辑用户信息/更新
     *
     * @param User $user
     * @return void
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }
    public function update(User $user, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50|min:3',
            'password' => 'nullable|confirmed|min:6',
        ]);
        $this->authorize('update', $user);
        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = $request->password;
        }
        $user->update($data);
        session()->flash('success', '个人资料更新成功');
        return redirect()->route('users.show', $user->id);
    }


    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户' . $user->name);
        return back();
    }
    /**
     * 发送邮件方法
     */
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'aufree@yousails.com';
        $name = 'Aufree';
        $to = $user->email;
        $subject = '感谢注册Sample应用!请确认您的邮箱';

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            //$message->from($from, $name)->to($to)->subject($subject);
            $message->to($to)->subject($subject);
        });
    }


     public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = '粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }
}
