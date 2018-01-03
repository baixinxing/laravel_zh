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
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
}
