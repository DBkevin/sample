<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword;

use App\Models\Status;

class User extends Authenticatable
{
    use Notifiable;
    /**
     * 链接的数据表名
     */
    protected $table='users';

    /**
     * The attributes that are mass assignable.
     *  只有包含在这个数组里面的字段才能被更新
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * 在这个字段里面属性会在实例化和json的时候隐藏
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * 头像获取方法
     *通过 $this->attributes['email'] 获取到用户的邮箱；
     *使用 trim 方法剔除邮箱的前后空白内容；
     *用 strtolower 方法将邮箱转换为小写；
     *将小写的邮箱使用 md5 方法进行转码；
     * @param string $size
     * @return void
     */
    public function gravatar($size='100')
    {
        $hash=md5(strtolower(trim($this->attributes['email'])));
        // $hash=md5(strtolower(trim('416606903@qq.com')));
        return "https://s.gravatar.com/avatar/$hash?s=$size";
    }


    public static function boot()
    {
        parent::boot();
        static::creating(function($user){
            $user->activation_token=str_random(30);
        });
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
    //指明一个用户可以有多条微博
    public function statuses(){
        return $this->hasMany(Status::class);
    }


    public function feed(){
        return $this->statuses()
                    ->orderBy('created_at', 'desc');
    }
}
