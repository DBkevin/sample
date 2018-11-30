<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    /**
     * 授权策略 更新用户的时候判断更新的是否是同一个
     *
     * @param User $currennUser 默认当前登陆用户
     * @param User $user  要进行授权的的用户示例
     * @return void
     */
    public function update(User $currennUser,User $user){
        return $currennUser->id===$user->id;
    }

    public function destroy(User $currennUser,User $user){
        return $currennUser->is_admin && $currennUser->id !== $user->id;
    }
}
