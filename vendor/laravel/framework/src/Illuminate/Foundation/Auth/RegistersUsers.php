<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

trait RegistersUsers
{
    use RedirectsUsers;

    /**
     * 显示应用注册表单
     * 
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * 处理应用程序的注册请求。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {   //验证
        $this->validator($request->all())->validate();
        //事件
        event(new Registered($user = $this->create($request->all())));
        //
        $this->guard()->login($user);
        //跳转  已注册业务逻辑  之后 跳转
        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }

    /**
     * 让警卫在注册时使用。
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * 用户已注册。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        //
    }
}
