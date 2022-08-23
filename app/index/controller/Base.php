<?php
/**
 * FunAdmin
 * ============================================================================
 * 版权所有 2017-2028 FunAdmin，并保留所有权利。
 * 网站地址: http://www.FunAdmin.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * Author: yuege
 * Date: 2017/8/2
 */
namespace app\index\controller;

use app\common\controller\Controller;
use app\common\model\Attach;
use app\common\service\UploadService;
use think\Exception;
use support\Request;

class Base extends Controller
{
    public function __construct()
    {
        parent::__construct();

    }
    //是否登录
    protected function isLogin()
    {
        if (session('member.id')) {
            return session('member');
        } else {
            return false;
        }
    }
}