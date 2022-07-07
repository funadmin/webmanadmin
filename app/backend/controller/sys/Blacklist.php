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
namespace app\backend\controller\sys;
use app\common\controller\Controller;
use app\common\traits\Curd;
use think\App;
use think\facade\Db;
use support\View;
use app\common\annotation\ControllerAnnotation;
use app\common\annotation\NodeAnnotation;
use support\Request;

class Blacklist extends Controller {


    public function beforeAction(Request $request)
    {
        parent::beforeAction($request);
        $this->modelClass = new \app\common\model\Blacklist();
    }

}