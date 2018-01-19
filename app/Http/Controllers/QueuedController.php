<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Jobs\QueuedTest;

class QueuedController extends Controller
{

    public function Test()
    {
        $arr = array('time' => time());
        $this->dispatch(new QueuedTest($arr));
    }

}
