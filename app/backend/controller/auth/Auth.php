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

namespace app\backend\controller\auth;

use app\backend\service\AuthService;
use app\common\controller\Controller;
use app\backend\model\AuthRule;
use fun\helper\TreeHelper;
use support\Request;
use think\facade\Cache;
use support\View;
use app\common\annotation\ControllerAnnotation;
use app\common\annotation\NodeAnnotation;

/**
 * @ControllerAnnotation(title="权限")
 * Class Auth
 * @package app\backend\controller\auth
 */
class Auth extends Controller
{

    public $uid;
    public function __construct()
    {
        parent::__construct();
        $this->modelClass = new AuthRule();
        $this->uid  =session('admin.id');
    }


    /**
     * @NodeAnnotation(title="权限列表")
     * @return \Response|\support\Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        if (request()->isAjax()) {
            if (request()->input('selectfields')) {
                return $this->selectList();
            }
            $uid = $this->uid;
            $list = Cache::get('ruleList_' . $uid);
            if (!$list) {
                $list = $this->modelClass
                    ->order('pid asc,sort asc')
                    ->select()->toArray();
                foreach ($list as $k => &$v) {
//                    $v['lay_is_open'] = true;
                    $v['title'] = lang($v['title']);
                }
                Cache::set('ruleList_' . $uid, $list, 3600);
            }
            $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list, 'count' => count($list), 'is' => true, 'tip' => '操作成功'];
            return json($result);
        }
        return fetch();
    }
    /**
     * @NodeAnnotation(title="权限增加")
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        if (request()->isAjax()) {
            $post = request()->post();
            if (empty($post['title'])) {
                return $this->error(lang('rule name cannot null'));
            }
            if (empty($post['sort'])) {
                return $this->error(lang('sort') . lang(' cannot null'));
            }
            $post['icon'] = $post['icon'] ? 'layui-icon '.$post['icon'] : 'layui-icon layui-icon-diamond';
            $post['href'] = '/'.trim($post['href'], '/');
            $rule = [
                'href'=>'require|unique:auth_rule',
                'title'=>'require'
            ];
            $this->validate($post, $rule);
            if ($this->modelClass->save($post)) {
                Cache::clear();
                return $this->success(lang('operation success'));
            } else {
                return $this->error(lang('operation failed'));
            }
        } else {
            $list = $this->modelClass
                ->order('sort ASC')
                ->field('id,title,pid')
                ->select()->toArray();
            $list = TreeHelper::getTree($list);
            $view = [
                'formData' => null,
                'ruleList' => $list,
            ];
            View::assign($view);
            return fetch();
        }
    }

    /**
     * @NodeAnnotation(title="修改")
     * @return \Response|\support\Response|void
     *
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $post = request()->all();
            $post['icon'] = $post['icon'] ? 'layui-icon '.$post['icon'] : 'layui-icon layui-icon-diamond';
            $id = $this->request->param('id');
            $model = $this->findModel($id);
            if($post['pid'] && $post['pid'] == $id)  $this->error(lang('The superior cannot be set as himself'));
            $childIds = array_filter(explode(',',(new AuthService())->getAllIdsBypid($id)));
            if($childIds && in_array($post['pid'],$childIds)) $this->error(lang('Parent menu cannot be modified to submenu'));
            if ($model->save($post)) {
                Cache::clear();
                return $this->success(lang('operation success'));
            } else {
                return $this->error(lang('operation failed'));
            }
        } else {
            $list = $this->modelClass
                ->order('sort ASC')
                ->field('id,title,pid')
                ->select()->toArray();
            $list = TreeHelper::getTree($list);
            $id = request()->input('id');
            $one = $this->modelClass->find($id)->toArray();
            $one['icon'] = $one['icon'] ? trim(substr($one['icon'],10),' ') : 'layui-icon layui-icon-diamond';
            $view = [
                'formData' => $one,
                'ruleList' => $list,
            ];
            View::assign($view);
            return fetch('add');
        }
    }

    /**
     * @NodeAnnotation(title="子权限添加")
     * @return \Response|\support\Response|void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function child()
    {
        if (request()->isAjax()) {
            $post = request()->post();
            $post['icon'] = $post['icon'] ? 'layui-icon '.$post['icon'] : 'layui-icon layui-icon-diamond';
            $rule = [
                'href'=>'require|unique:auth_rule',
                'title'=>'require'
            ];
            $this->validate($post, $rule);
            $save = $this->modelClass->save($post);
            Cache::delete('ruleList_' . $this->uid);
            if($save) return $this->success(lang('operation success')) ;
            return $this->error(lang('operation failed'));
        } else {
            $ruleList =$this->modelClass
                ->order('sort asc')
                ->select();
            $ruleList = $this->modelClass->cateTree($ruleList);
            $parent = $this->modelClass->find(request()->input('id'));
            $view = [
                'formData' => '',
                'ruleList' => $ruleList,
                'parent' => $parent,
            ];
            View::assign($view);
            return fetch('child');
        }
    }

    /**
     * @NodeAnnotation(title="权限删除")
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete()
    {
        $ids = request()->input('ids')?request()->input('ids'):request()->input('id');
        $list = $this->modelClass->find($ids);
        $child = $this->modelClass->where('pid', 'in', $ids)->select();
        if (!empty($child->toArray())) {
            return $this->error(lang('delete child first'));
        } elseif (empty($child->toArray())) {
            $list->force(true)->delete();
            return $this->success(lang('operation success'));
        } else {
            return $this->error('id' . lang('not exist'));
        }
    }

    /**
     * @NodeAnnotation(title="修改")
     */
    public function modify()
    {
        $uid = session('admin.id');
        $id = request()->input('id');
        $field = request()->input('field');
        $value = request()->input('value');
        if($id){
            if(!$this->allowModifyFileds = ['*'] and !in_array($field, $this->allowModifyFileds)){
                return $this->error(lang('Field Is Not Allow Modify：' . $field));
            }
            $model = $this->findModel($id);
            if (!$model) {
                return $this->error(lang('Data Is Not 存在'));
            }
            $model->$field = $value;
            $save = $model->save();
            Cache::delete('ruleList_' . $uid);
            if($save) return $this->success(lang('Modify success'));
            return  $this->error(lang("Modify Failed"));
        }else{
            return $this->error(lang('Invalid data'));
        }
    }
}