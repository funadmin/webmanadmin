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
use support\Request;
class Index extends Base{


    public function beforeAction(Request $request)
    {
        parent::beforeAction($request);

    }
    public function index(){

        return fetch('index/index');
    }

}