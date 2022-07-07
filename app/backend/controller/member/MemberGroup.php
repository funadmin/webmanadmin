<?php
namespace app\backend\controller\member;

use app\backend\model\MemberGroup as MemberGroupModel;
use app\common\controller\Controller;
use support\Request;
use think\App;
use think\Exception;
use app\common\annotation\NodeAnnotation;
use app\common\annotation\ControllerAnnotation;

/**
 * @ControllerAnnotation (title="会员组")
 * Class MemberGroup
 * @package app\backend\controller\member
 */
class MemberGroup extends Controller{

    public function beforeAction(Request $request)
    {
        parent::beforeAction($request);
        $this->modelClass = new MemberGroupModel();
    }
    /**
     * @NodeAnnotation (title="添加")
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add(){
        if (request()->isAjax()) {
            $post = request()->post();
            $rule = ['name|组名'=>'require|unique:member_group'];
            $this->validate($post, $rule);
            try {
                $save = $this->modelClass->save($post);
            } catch (Exception $e) {
                return $this->error(lang('Save Failed'));
            }
            return $this->success(lang('Save Success')) ;
        }
        $view = [
            'formData' => '',
            'title' => lang('Add'),
        ];
        return fetch('add',$view);
    }

    /**
     * @NodeAnnotation (title="删除")
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete()
    {
        $ids =  request()->input('ids')?request()->input('ids'):request()->input('id');
        $list = $this->modelClass->withTrashed()->where('id','in', $ids)->select();
        if($ids ==1 || is_array($ids) and in_array(1,$ids)){
            return $this->error(lang("Default Group Cannot Delete"));
        }
        if(empty($list)) return $this->error('Data is not exist');
        try{
            foreach ($list as $k=>$v){
                $save = $v->force()->delete();
            }
        } catch (\Exception $e) {
            return $this->error(lang("operation failed"));
        }

        return $this->success(lang('operation success')) ;
    }


}