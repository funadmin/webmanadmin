<?php

namespace app\controller;

use app\common\traits\Jump;
use app\common\controller\Controller;
use think\facade\Db;
use support\View;

class Index
{

    public function index(){

        return redirect('/index/index');
    }

}