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
use app\common\model\Config as ConfigModel;
use app\common\model\ConfigGroup as ConfigGroupModel;
use support\View;
use support\Request;
use app\common\annotation\ControllerAnnotation;
use app\common\annotation\NodeAnnotation;

/**
 * @ControllerAnnotation(title="配置组")
 * Class ConfigGroup
 * @package app\backend\controller\sys
 */
class ConfigGroup extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->modelClass = new ConfigGroupModel();

    }

    /**
     * @NodeAnnotation(title="添加")
     * @return \Response|\support\Response|void
     */
    public function add(){
        if(request()->isPost()){
            $post = request()->all();
            $rule = ['name|组名'=>'unique:config_group'];
            $this->validate($post, $rule);
            try {
                $save = $this->modelClass->save($post);
            } catch (\Exception $e) {
                return $this->error(lang('Save failed'));
            }
            return $this->success(lang('Save success'));
        }
        $view = ['title'=>lang('Config Group'),'formData'=>''];
        View::assign($view);
        return fetch('add');

    }

    /**
     * @NodeAnnotation(title="删除")
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete(){
        $id = request()->input('id')?request()->input('id'):request()->input('ids');
        $lists = $this->modelClass->where('id','in',$id)->select();
        if(!$lists){
            return $this->error(lang('Data is not exist'));
        }
        foreach ($lists as $k=>$list){
            $config = ConfigModel::where('type',$list->name)->find();
            if($config){
                return $this->error(lang('Group has config'));
            }else{
                try {
                    $list->force()->delete();
                } catch (\Exception $e) {
                    return $this->error(lang("operation failed"));
                }
            }
        }
        return $this->success(lang('operation success'));
    }

}