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
use app\backend\model\AuthGroup as AuthGroupModel;
use app\common\controller\Controller;
use fun\helper\SignHelper;
use fun\helper\StringHelper;
use support\Request;
use support\View;
use app\backend\model\Admin as AdminModel;
use app\common\annotation\ControllerAnnotation;
use app\common\annotation\NodeAnnotation;

/**
 * @ControllerAnnotation (title="管理员")
 * Class Admin
 * @package app\backend\controller\auth
 */
class Admin extends Controller
{

    public function beforeAction(Request $request)
    {
        parent::beforeAction($request);
        $this->modelClass = new AdminModel();
    }

    /**
     * @NodeAnnotation (title="List")
     * @return \Response|\support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        if(request()->isAjax()){
            if (request()->input('selectfields')) {
                return $this->selectList();
            }
            list($this->page, $this->pageSize,$sort,$where) = $this->buildParames();
            $count = $this->modelClass
                ->where($where)
                ->order($sort)
                ->count();
            $list =$this->modelClass->where($where)
                ->order($sort)
                ->page($this->page  ,$this->pageSize)
                ->select()->toArray();
            foreach ($list as &$v){
                $title = AuthGroupModel::where('id','in',$v['group_id'])->column('title');
                $v['authGroup']['title'] = join(',',$title);
            }
            unset($v);
            $result = ['code'=>0,'msg'=>lang('For password retrieval, please fill in carefully.'),'data'=>$list,'count'=>$count];
            return json($result);
        }
        return fetch();
    }

    /**
     * @NodeAnnotation (title="添加")
     * @return \support\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        if (request()->isPost()) {
            $post = request()->post();
            $rule = [
                'username|用户名' => [
                    'require' => 'require',
                    'max'     => '100',
                    'unique'  => 'admin',
                ],
                'password|密码' =>[
                    'require' => 'require',
                ],
                'group_id|用户组'=>[
                    'require' => 'require',
                ],
            ];
            $this->validate($post, $rule);
            $post['password'] = StringHelper::filterWords($post['password']);
            if(!$post['password']){
                $post['password']='123456';
            }
            $post['password'] = SignHelper::password($post['password']);
            //添加

            $result = $this->modelClass->save($post);
            if ($result) {
                return $this->success(lang('operation success'));
            } else {
                return $this->error(lang('operation failed'));
            }
        }
        $list = '';
        $auth_group = AuthGroupModel::where('status', 1)->select();
        $view = [
            'formData'  =>$list,
            'authGroup' => $auth_group,
            'title' => lang('Add'),
        ];
        View::assign($view);
        return fetch('add');

    }

    /**
     * @NodeAnnotation (title="更新信息")
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function upme()
    {
        $id = request()->input('id');
        if (request()->isPost()) {
            $post = request()->post();
            $rule = ['group_id'=>'require'];
            $this->validate($post, $rule);
            if(session('admin.id'))
                if($post['password']){
                    $post['password'] = password_hash($post['password'],PASSWORD_BCRYPT);
                }else{
                    unset($post['password']);
                }
            $list =  $this->modelClass->find($id);
            $result = $list->save($post);
            if ($result) {
                return $this->success(lang('operation success'));
            } else {
                return $this->error(lang('operation failed'));
            }
        }
        $list =  $this->modelClass->find($id);
        $list->password = '';
        $auth_group = AuthGroupModel::where('status', 1)->select();
        if($list['group_id']) $list['group_id'] = explode(',',$list['group_id']);
        $view = [
            'formData'  =>$list,
            'authGroup' => $auth_group,
            'title' => lang('Add'),
            'type' => request()->get('type'),
        ];
        View::assign($view);
        return fetch('add');

    }
    /**
     * @NodeAnnotation (title="编辑")
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $id = request()->input('id');
        if (request()->isPost()) {
            $post = request()->post();
            $rule = ['group_id'=>'require'];
            $this->validate($post, $rule);
            if(session('admin.id'))
            if($post['password']){
                $post['password'] = password_hash($post['password'],PASSWORD_BCRYPT);
            }else{
                unset($post['password']);
            }
            $list =  $this->modelClass->find($id);
            $result = $list->save($post);
            if ($result) {
                return $this->success(lang('operation success'));
            } else {
                return $this->error(lang('operation failed'));
            }
        }
        $list =  $this->modelClass->find($id);
        $list->password = '';
        $auth_group = AuthGroupModel::where('status', 1)->select();
        if($list['group_id']) $list['group_id'] = explode(',',$list['group_id']);
        $view = [
            'formData'  =>$list,
            'authGroup' => $auth_group,
            'title' => lang('Add'),
            'type' => request()->get('type'),
        ];
        View::assign($view);
        return fetch('add');

    }

    /**
     * @NodeAnnotation (title="修改")
     */
    public function modify()
    {
        $id = request()->input('id');
        $field = request()->input('field');
        $value = request()->input('value');
        if($id){
            if($id==1){
                return $this->error(lang('SupperAdmin can not modify'));
            }
            $model = $this->findModel($id);
            $model->$field = $value;
            $save = $model->save();
            if($save) return $this->success(lang('Modify success')) ;
            return  $this->error(lang("Modify Failed"));
        }else{
            return $this->error(lang('Invalid data'));
        }

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
        $ids = request()->input('ids')?request()->input('ids'):request()->input('id');
        if (!empty($ids)) {
            if($ids==1){
                return $this->error(lang('SupperAdmin can not delete'));
            }
            if(is_array($ids) && in_array(1,$ids)){
                return $this->error(lang('SupperAdmin can not delete'));
            }
            $list = $this->modelClass->where('id','in', $ids)->select();
            try {
                foreach ($list as $k=>$v){
                    $v->force()->delete();
                }
            } catch (\Exception $e) {
                return $this->error(lang($e->getMessage()));
            }
            return $this->success(lang('operation success'));
        } else {
            return $this->error(lang('Ids can not empty'));
        }
    }

    /**
     * @NodeAnnotation(title="修改密码")
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function password()
    {
        $id = request()->input('id');
        if (request()->isAjax()) {
            $oldpassword = request()->post('oldpassword');
            $password = request()->post('password', '',['strip_tags','trim','htmlspecialchars']);
            $one = $this->modelClass->find($id?:session('admin.id'));
            if (!$id && !password_verify($oldpassword, $one['password'])) {
                return $this->error(lang('Old Password Error'));
            }else if($oldpassword == $password){
                return $this->error(lang('Password Cannot the Same'));
            }
            try {
                $post['password'] = SignHelper::password($password);
                $one->save($post);
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
            return $this->success(lang('operation success'));
        }
        $view = ['id'=>$id];
        return fetch('password',$view);
    }

    /**
     * @NodeAnnotation(title="基本信息")
     * @return string
     */
    public function base()
    {
        if (!request()->isAjax()) {
            return View::fetch('index/password');
        } else {
            $post = request()->post();
            $admin = Admin::find($post['id']);
            $oldpassword = request()->post('oldpassword', '123456', 'fun\helper\StringHelper::filterWords');
            if (!password_verify($oldpassword, $admin['password'])) {
                return $this->error(lang('Origin password error'));
            }
            $password = request()->post('password', '123456', 'fun\helper\StringHelper::filterWords');
            try {
                $post['password'] = SignHelper::password($password);
                if (Session::get('admin.id') == 1) {
                    Admin::update($post);
                } elseif (Session::get('admin.id') == $post['id']) {
                    Admin::update($post);
                } else {
                    return $this->error(lang('Permission denied'));
                }

            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
            return $this->success(lang('operation success'));

        }
    }
}
