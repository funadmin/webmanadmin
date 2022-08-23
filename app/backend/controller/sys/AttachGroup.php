<?php
/**
 * ============================================================================
 * Created by FunAdmin.
 * 版权所有 2017-2028 FunAdmin，并保留所有权利。
 * 网站地址: http://www.FunAdmin.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * User: yuege
 * Date: 2020/2/10
 * Time: 18:51
 */

namespace app\backend\controller\sys;

use app\common\controller\Controller;
use app\common\traits\Curd;
use app\backend\model\AttachGroup as AttachGroupModel;
use fun\helper\TreeHelper;
use think\App;
use app\common\annotation\ControllerAnnotation;
use app\common\annotation\NodeAnnotation;
use support\Request;

/**
 * @ControllerAnnotation(title="文件")
 * Class Attach
 * @package app\backend\controller\sys
 */
class AttachGroup extends Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->modelClass = new AttachGroupModel();

    }

    /**
     * @NodeAnnotation(title="删除")
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete()
    {
        $ids = request()->input('ids') ? request()->input('ids') : request()->input('id');
        $child= $this->getallIdsBypid($ids);
        if($child){
            return $this->error(lang('please delete child node first'));
        }
        if($ids==1){
            return $this->error(lang('default group cannot delete'));
        }
        $list = $this->modelClass->where('id','in', $ids)->select();
        if (empty($list)) return $this->error('Data is not exist');
        try {
            foreach ($list as $k=>$v){
                $res = \app\common\model\Attach::where('group_id',$v->id)->update(['group_id'=>0]);
                $save = $v->force()->delete();
            }
        } catch (\Exception $e) {
            return $this->error(lang("operation success"));
        }
        if($save) return $this->success(lang('operation success')) ;
        return $this->error(lang("Delete fail"));
    }
    protected function getallIdsBypid($pid)
    {
        $res = $this->modelClass->where('pid', $pid)->select();
        $str = '';
        if (!empty($res)) {
            foreach ($res as $k => $v) {
                $str .= "," . $v['id'];
                $str .= $this->getallIdsBypid($v['id']);
            }
        }
        return $str;
    }


}

