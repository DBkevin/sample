<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
}
