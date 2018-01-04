<?php

namespace Illuminate\Foundation\Auth;

trait RedirectsUsers
{
    /**
     * 获取注册后/登录重定向路径
     *
     * @return string
     */
    public function redirectPath()
    {   //如果存在跳转方法直接跳转
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }
        //如果存在跳转属性返回跳转属性否则默认返回/home
        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
}
