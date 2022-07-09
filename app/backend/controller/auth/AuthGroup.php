<?php
namespace app\backend\controller\auth;
use app\backend\model\AuthGroup as AuthGroupModel ;
use app\backend\model\AuthRule;
use app\backend\service\AuthService;
use app\common\controller\Controller;
use fun\helper\TreeHelper;
use support\View;
use support\Request;
use app\common\annotation\ControllerAnnotation;
use app\common\annotation\NodeAnnotation;

/**
 * @ControllerAnnotation(title="会员组")
 * Class AuthGroup
 * @package app\backend\controller\auth
 */
class AuthGroup extends Controller
{
    public function beforeAction(Request $request)
    {
        parent::beforeAction($request);
        $this->modelClass = new AuthGroupModel();
    }

    /**
     * @NodeAnnotation(title="列表")
     * @return \support\Response
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
            list($this->page, $this->pageSize,$sort, $where) = $this->buildParames();
            $count = $this->modelClass
                ->where($where)
                ->count();
            $list = $this->modelClass
                ->where($where)
                ->order('id asc')
                ->page($this->page, $this->pageSize)
                ->select();
            $list = TreeHelper::cateTree($list,'title');
            $result = ['code' => 0, 'msg' => lang('operation success'), 'data' => $list, 'count' => $count];
            return json($result);
        }
        return fetch();
    }

    /**
     * @NodeAnnotation(title="添加")
     * @return \Response|\support\Response|void
     */
    public function add()
    {
        if (request()->isPost()) {
            $post = request()->post();
            $rule = [
                'title|用户组名' => [
                    'require' => 'require',
                    'max'     => '100',
                    'unique'  => 'auth_group',
                ]
            ];
            $this->validate($post, $rule);
            $result =  $this->modelClass->save($post);
            if ($result) {
                return $this->success(lang('operation success'));
            } else {
                return $this->error(lang('operation failed'));
            }

        } else {
            $authGroup = $this->modelClass->where('status',1)->select()->toArray();
            $authGroup = TreeHelper::cateTree($authGroup);
            $view = [
                'formData' => null,
                'authGroup' => $authGroup,
            ];
            View::assign($view);
            return fetch();
        }
    }

    /**
     * @NodeAnnotation(title="修改")
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $id = request()->get('id');
        $list = $this->modelClass->find($id);
        if (request()->isPost()) {
            $post = request()->post();
            if($id==1){
                return $this->error(lang('SupperAdmin cannot edit'));
            }
            $res = $list->save($post);
            if($res){
                return $this->success(lang('operation success'));
            }else{
                return $this->error(lang('operation failed'));
            }

        } else {
            $id = request()->input('id');
            $list = $this->modelClass->find(['id' => $id]);
            $authGroup = $this->modelClass->where('status',1)->select()->toArray();
            $authGroup = TreeHelper::cateTree($authGroup);
            $view = [
                'formData' => $list,
                'authGroup' => $authGroup,
            ];
            View::assign($view);
            return fetch('add');
        }
    }

    /**
     * @NodeAnnotation(title="修改")
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function modify()
    {
        if (request()->isPost()) {
            $id = request()->input('id');
            if($id==1){
                return $this->error(lang('SuperGroup Cannot Edit'));
            }
            $field = request()->input('field');
            $value = request()->input('value');
            if($id){
                $list = $this->modelClass->find($id);
                $list->$field = $value;
                $save = $list->save();
                if($save) return $this->success(lang('Modify Success')); return $this->error(lang("Modify Failed"));
            }else{
                return $this->error(lang('Invalid Data'));
            }

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
        if($ids==1 || is_array($ids) and in_array(1,$ids)){
            return $this->error(lang('SuperGroup Cannot Edit'));
        }else{
            $list = $this->modelClass->withTrashed()->where('id','in', $ids)->select();
            try {
                foreach ($list as $k=>$v){
                    $v->force()->delete();
                }
            } catch (\Exception $e) {
                return $this->error(lang("operation success"));
            }
            return $this->success(lang('operation success'));

        }
    }

    /**
     * @NodeAnnotation(title="显示权限")
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function access()
    {
        $AuthModel = new AuthRule();
        $group_id = request()->input('id');
        if(request()->isAjax()){
            if(request()->isGet()){
                $idList = Cache::get('authIdList'.session('admin.id'));
                if(!$idList){
                    $idList = $AuthModel->cache('authIdList'.session('admin.id'))
                        ->where('status',1)->column('id');
                    sort($idList);
                }
                $groupRule = $this->modelClass->where('id', $group_id)
//                    ->where('status',1)
                    ->field('id,rules,pid')
                    ->find();
                $rules = $groupRule && $groupRule['rules']?$groupRule['rules']:'';
                if($groupRule->pid > 0 && $groupRule->pid!=1){
                    $prules =  $this->modelClass->where('id', $groupRule->pid)
//                        ->where('status',1)
                        ->field('rules')
                        ->value('rules');
                    $admin_rule = $AuthModel->field('id, pid, title')
                        ->where('status',1)
                        ->where('id','in',trim($prules,','))
                        ->order('sort asc')->cache(true)
                        ->select()->toArray();
                }else{
                    $admin_rule = $AuthModel->field('id, pid, title')
                        ->where('status',1)
                        ->order('sort asc')->cache(true)
                        ->select()->toArray();
                }
                $request = request();
                $list = (new AuthService($request))->authChecked($admin_rule, $pid = 0, $rules,$group_id);
                $view = [
                    'code'=>1,
                    'msg'=>'ok',
                    'data'=>[
                        'list' => $list,
                        'idList' => $idList,
                        'group_id' => $group_id,
                    ]
                ];
                return json($view);
            }else{
                $rules = request()->post('rules');
                if (empty($rules)) {
                    return $this->error(lang('please choose rule'));
                }
                $rules = json_decode($rules,true);
                $request = request();
                $rules = (new AuthService($request))->authNormal($rules);
                $rules = array_column($rules, 'id');
                $rls = '';
                $childIndexId='';
                foreach ($rules as $k=>$v){
                    $child = AuthRule::where('pid',$v)
                        ->where('id','in',$rules)->find();
                    if($child){
                        $childIndex = AuthRule::where('pid','=',$v)
                            ->where('href', 'like', '%/index')
                            ->field('id')
                            ->find();
                        $childIndexId .= ($childIndex?$childIndex['id']:'').',';
                    }
                    $rls.= $v.',';
                }
                $rls = $childIndexId.$rls;
                $list = $this->modelClass->find($group_id);
                $list->rules = $rls;
                try {
                    $list->save();
                }catch(\Exception $e){
                    return $this->error(lang('rule assign fail'));
                }
                $admin = session('admin');
                $admin['rules'] = $rls;
                request()->session('admin', $admin);
                return $this->success(lang('rule assign success'),__u('sys.Auth/group'));
            }
        }
        return fetch('access');
    }


}