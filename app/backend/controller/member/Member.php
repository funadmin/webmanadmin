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

namespace app\backend\controller\member;

use app\common\controller\Controller;
use app\common\model\Provinces;
use app\common\traits\Curd;
use support\View;
use app\backend\model\MemberLevel;
use app\backend\model\MemberGroup;
use app\backend\model\Member as MemberModel;
use support\Request;
class Member extends Controller
{
    protected $allowModifyFields = ['*'];
    protected $relationSearch = true;

    public function beforeAction(Request $request)
    {
        parent::beforeAction($request);
        $this->modelClass = new MemberModel();
    }

    public function getcitys(){
        if(request()->isAjax()) {}{
            $pid = request()->input('pid',0);
            $citys = Provinces::where('pid',$pid)->order('id desc')->field('id,name,pid')
                ->cache('provinces.'.$pid,3600*24)->select()->toArray();
            return $this->success('','',$citys);
        }
    }
    public function getgroup(){
        if(request()->isAjax()) {}{
            $memberGroup = MemberGroup::where('status', 1)->select()->toArray();

            return $this->success('','',$memberGroup);
        }
    }
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
        return fetch('');
    }

    public function add()
    {
        if (request()->isPost()) {
            $post = request()->post();
            $rule = [
                'username|用户名' => 'require|unique:member',
                'mobile|手机号' => 'require|unique:member',
            ];
            $this->validate($post, $rule);
            $save = $this->modelClass->save($post);
            if ($save) {
                return $this->success(lang('operation success'));
            } else {
                return $this->error(lang('add fail'));
            }
        }
        $memberLevel = MemberLevel::where('status', 1)->select();
        $memberGroup = MemberGroup::where('status', 1)->select();

        $view = [
            'formData' => '',
            'title' => lang('Add'),
            'memberLevel' => $memberLevel,
            'memberGroup' => $memberGroup,
        ];
        View::assign($view);
        return fetch('');
    }

    public function edit()
    {
        $id = request()->get('id');
        if (request()->isPost()) {
            $list = $this->modelClass->find($id);
            if(empty($list)) return $this->error(lang('Data is not exist'));
            $post = request()->post();
            $rule = [
                'username|用户名' => 'require',
                'group_id|用户组别' => 'require',
                'level_id|用户级别' => 'require',
            ];
            $this->validate($post, $rule);
            $res = $list->save($post);
            if ($res) {
                return $this->success(lang('operation success'), __u('index'));
            } else {
                return $this->error(lang('Edit fail'));
            }
        }
        $list = MemberModel::find(request()->get('id'));
        $memberLevel = MemberLevel::where('status', 1)->select();
        $memberGroup = MemberGroup::where('status', 1)->select();
        $view = [
            'formData' => $list,
            'title' => lang('Edit'),
            'memberLevel' => $memberLevel,
            'memberGroup' => $memberGroup,
        ];
        View::assign($view);
        return fetch('add');
    }
    public function recycle()
    {
        if (request()->isAjax()) {
            list($this->page, $this->pageSize, $sort, $where) = $this->buildParames();
            $count = $this->modelClass->onlyTrashed()
                ->withJoin(['memberGroup','memberLevel'])
                ->where($where)
                ->count();
            $list = $this->modelClass->onlyTrashed()
                ->withJoin(['memberGroup','memberLevel'])
                ->where($where)
                ->order($sort)
                ->page($this->page, $this->pageSize)
                ->select();
            $result = ['code' => 0, 'msg' => lang('Get Data Success'), 'data' => $list, 'count' => $count];
            return json($result);
        }
        return fetch('index');
    }


}