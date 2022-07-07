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

class Ajax extends Base
{

    public function beforeAction(Request $request)
    {
        parent::beforeAction($request);
        $this->modelClass = new Attach();

    }

    /**
     * @return \support\Response|void
     * 文件上传总入口 集成qiniu ali tenxunoss
     */
    public function uploads()
    {
        try {
            $upload = new UploadService();
            $result = $upload->uploads(session('member.id'),0);
            return json($result);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @return \support\Response|void
     * 获取附件列表
     */
    public function getAttach()
    {
        if ( request()->isAjax()) {
            list($this->page, $this->pageSize, $sort, $where) = $this->buildParames();
            $where = [];
            if( request()->input('original_name')){
                $where[] =['original_name|id','like','%'. request()->input('original_name').'%'];
            }
            $count = $this->modelClass
                ->where($where)
                ->order($sort)
                ->count();
            $list = $this->modelClass
                ->where($where)
                ->order($sort)
                ->page($this->page, $this->pageSize)
                ->select();
            $result = ['code' => 0, 'msg' => lang('operation success'), 'data' => $list, 'count' => $count];
            return json($result);
        }
    }
    /**
     * 自动加载语言函数
     * @return void
     */
    public function getLang()
    {
        $name = request()->get("controllername");
        $name = strtolower(parse_name($name, 1));
        $addon = request()->get("addons");
        //默认只加载了控制器对应的语言名，你还根据控制器名来加载额外的语言包
        return jsonp($this->loadlang($name, $addon),'define');
    }
}