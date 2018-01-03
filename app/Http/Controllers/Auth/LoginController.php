<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 登陆控制器
    |--------------------------------------------------------------------------
    |
    | 这个控制器处理用户认证申请
    | 重定向到您的主屏幕,控制器使用一个trait
    | 方便地提供它的功能到你的应用程序。
    |
    */

    use AuthenticatesUsers;

    /**
     * 登陆后跳转地址
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * 创建一个控制器实例
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
