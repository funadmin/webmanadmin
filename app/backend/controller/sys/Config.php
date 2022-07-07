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
use app\common\model\FieldType;
use app\common\model\FieldVerify;
use think\facade\Cache;
use think\facade\Db;
use support\View;
use app\common\annotation\ControllerAnnotation;
use app\common\annotation\NodeAnnotation;
use support\Request;

/**
 * @ControllerAnnotation('配置')
 * Class Config
 * @package app\backend\controller\sys
 */
class Config extends Controller {


    public function beforeAction(Request $request)
    {
        parent::beforeAction($request);
        $this->modelClass = new ConfigModel();
    }

    /**
     * @NodeAnnotation(title="设置")
     * @return \support\View
     *
     */
    public function set(){
        if (request()->isPost()) {
            $post = request()->all();
            foreach ($post as $k=>$v){
                $res = $this->modelClass->where('code',$k)->update(['value'=>$v]);
            }
            Cache::clear();
            return $this->success(lang('Save Success'));
        }

        $group =  ['site','email','upload','sms'];
        $list = Db::name('config')
            ->where('group','in',$group)
            ->field('code,value')
            ->column('value','code');
        View::assign('formData',$list);
        return fetch();

    }

    /**
     * @NodeAnnotation(title="添加")
     * @return \Response|\support\Response|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add(){
        if(request()->isPost()){
            $post = request()->all();
            $rule = ['code|编码'=>"require|unique:config"];
            $this->validate($post, $rule);
            if($this->modelClass->save($post)){
                return $this->success(lang('operation success'));
            }else{
                return $this->error(lang('edit fail'));
            }

        }
        $list = '';
        $configGroup = Db::name('config_group')->select();
        $fieldType = FieldType::select()->toArray();
        $fieldVerify = FieldVerify::select()->toArray();
        $view = ['title'=>lang('Edit'),'fieldVerify'=>$fieldVerify,'formData'=>$list,'configGroup'=>$configGroup,'fieldType'=>$fieldType,];
        View::assign($view);
        return fetch();
    }

    /**
     * @NodeAnnotation(title="编辑配置")
     * @return \Response|\support\Response|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit(){
        $id  = request()->get('id');
        if(request()->isPost()){
            $list = $this->modelClass->find($id);
            if(empty($list)) return $this->error(lang('Data is not exist'));
            if (request()->isPost()) {
                $post = request()->post();
                $rule = [];
                $this->validate($post, $rule);
                try {
                    $save = $list->save($post);
                } catch (\Exception $e) {
                    return $this->error(lang('Save Failed'));
                }
                return $this->success(lang('Save Success')) ;
            }
        }
        $list = $this->modelClass->find(request()->input('id'));
        $configGroup = ConfigGroupModel::select();
        $fieldType = FieldType::select()->toArray();
        $fieldVerify = FieldVerify::select()->toArray();
        $view = ['title'=>lang('Add'),'fieldVerify'=>$fieldVerify,'formData'=>$list,'configGroup'=>$configGroup,'fieldType'=>$fieldType,];
        View::assign($view);
        return fetch('edit');
    }
    /**
     * @NodeAnnotation(title="设置值")
     * @param $id
     * @return \Response|\support\Response|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setValue($id){

        if(request()->isPost()){
            $list = $this->modelClass->find($id);
            if(empty($list)) return $this->error(lang('Data is not exist'));
            if (request()->isPost()) {
                $post = request()->post();
                $rule = [];
                $this->validate($post, $rule);
                $post['value'] = $this->buildValue($list,$post);
                try {
                    $save = $list->save($post);
                } catch (\Exception $e) {
                    return $this->error(lang('Save Failed'));
                }
                Cache::clear();
                return $this->success(lang('Save Success'));
            }
        }
        $list = $this->modelClass->find(request()->input('id'));
        $configGroup = ConfigGroupModel::select();
        $fieldType = FieldType::select()->toArray();
        $fieldVerify = FieldVerify::select()->toArray();
        $view = ['title'=>lang('Add'),'fieldVerify'=>$fieldVerify,'formData'=>$list,'configGroup'=>$configGroup,'fieldType'=>$fieldType,];
        View::assign($view);
        return fetch();
        
    }

    protected function buildValue($list,$post){
        switch ($list->type){
            case 'checkbox':
                $value = [];
                if(isset($post['value'])){
                    foreach ($post['value'] as $k => $v) {
                        $value[] = $k;
                    }
                    $value = implode("\n", $value);

                }
                break;
            case 'switch':
                if(isset($post['value']) && $post['value']== 'on') $value = 1;
                if(!isset($post['value'])) $value = 0;
                break;
            case 'array':
                $value = $post['value'];
                break;
            case 'datetime':
                $value = $post['value'];
                break;
            case 'range':
                $value = $post['value'];
                break;
            default:
                $value = $post['value'];
                break;
        }
        return $value;

    }

}