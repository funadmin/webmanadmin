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

namespace app\backend\controller;

use app\backend\model\AuthRule;
use app\backend\service\AuthService;
use app\common\controller\Controller;
use think\facade\Db;
use support\View;
use think\facade\Cache;

class Test extends Controller
{
    /**
     * @return string
     * @throws \Exception
     * 首页
     */
    public function index()
    {
        if (request()->isAjax()) {
            if (request()->input('selectfields')) {
                return $this->selectList();
            }
            list($this->page, $this->pageSize, $sort, $where) = $this->buildParames();
            $list = $this->modelClass
                ->withJoin(['memberGroup','memberLevel'])
                ->withCache(10)
                ->where($where)
                ->order($sort)
                ->paginate([
                    'list_rows'=> $this->pageSize,
                    'page' => $this->page,
                ]);
            $result = ['code' => 0, 'msg' => lang('Get Data Success'), 'data' => $list->items(), 'count' =>$list->total()];
            return json($result);
        }
        return fetch('member/member/index');
    }



}