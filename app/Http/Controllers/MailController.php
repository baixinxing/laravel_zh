<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Jobs\SendReminderEmail;
use App\Http\Controllers\Controller;

class MailController extends Controller
{
    //其他方法

    //发送提醒邮件
    //Queue 2 控制器
    public function sendReminderEmail(Request $request,$id){
        $user = User::findOrFail($id);
        
        //Queue 3 控制器
        $this->dispatch(new SendReminderEmail($user));
    }
}