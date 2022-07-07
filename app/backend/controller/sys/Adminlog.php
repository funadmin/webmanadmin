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
use app\backend\model\AdminLog as LogModel;
use support\Request;
use app\common\annotation\ControllerAnnotation;
use app\common\annotation\NodeAnnotation;
use think\facade\Db;

/**
 * @ControllerAnnotation(title="日志")
 * Class Adminlog
 * @package app\backend\controller\sys
 */
class Adminlog extends Controller {
    public function beforeAction(Request $request)
    {
        parent::beforeAction($request);
        $this->modelClass = new LogModel();

    }

    /**
     * @NodeAnnotation(title="列表")
     * @return array|string
     * @throws \think\db\exception\DbException
     */
    public function index(){
        if(request()->isAjax()){
            if (request()->input('selectfields')) {
                return $this->selectList();
            }
            list($this->page, $this->pageSize,$sort,$where) = $this->buildParames();
            if(session('admin.group_id') != 1){
                $where[] = ['admin_id','=',session('admin.id')];
            }
            $list = $this->modelClass
                ->where($where)
                ->order($sort)
                ->paginate([
                    'list_rows'=> $this->pageSize,
                    'page' => $this->page,
                ]);
            $result = ['code' => 0, 'msg' => lang('operation Success'), 'data' =>$list->items(),  'count' =>$list->total()];
            return json($result);
        }
        return fetch();
    }




}
