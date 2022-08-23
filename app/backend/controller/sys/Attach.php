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
use app\common\model\Attach as AttachModel;
use app\backend\model\AttachGroup ;
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
class Attach extends Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->modelClass = new AttachModel();
    }

    /**
     * @NodeAnnotation(title="列表")
     * @return mixed|\think\response\Json|\think\response\View
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
            list($this->page, $this->pageSize, $sort, $where) = $this->buildParames();
            $count = $this->modelClass->where($where)
                ->count();
            $list = $this->modelClass
                ->where($where)
                ->order($sort)
                ->page($this->page, $this->pageSize)
                ->select();
            $result = ['code' => 0, 'msg' => lang('operation success'), 'data' => $list, 'count' => $count];
            return json($result);
        }
        return fetch();
    }
    /**
     * @NodeAnnotation('选择文件')
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function selectfiles()
    {
        $param = request()->all();
        $group = AttachGroup::order('sort asc')->select()->toArray();
        $allGroup = [['pid'=>0,'id'=>0,'title'=>'全部','sort'=>1, 'disabled'=> true,'radioDisabled'=>true ,  "children" => []]];
        $groupList = array_merge($allGroup,TreeHelper::getTree($group));
        list($this->page, $this->pageSize, $sort, $where) = $this->buildParames();
        if(request()->input('group_id')){
            $where[] = ['group_id','=',request()->input('group_id')];
        }
        if(request()->input('original_name')){
            $where[] = ['original_name','like','%'.request()->input('original_name').'%'];
        }
        $this->pageSize = $param['limit']??12;
        $list = $this->modelClass
            ->where($where)
            ->order($sort)
            ->paginate([
                'list_rows'=> $this->pageSize,
                'page' => $this->page,
            ]);
        return fetch('selectfiles',['param'=>$param,'groupList'=>$groupList, 'data' => $list->items(), 'count' =>$list->total()]);
    }
    /**
     * @NodeAnnotation('移动文件')
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function move()
    {
        $ids = request()->input('ids') ? request()->input('ids') : request()->input('id');
        $list = $this->modelClass->where('id','in', $ids)->select();
        if (empty($list)) return $this->error('Data is not exist');
        try {
            foreach ($list as $v) {
                $v->group_id= input('group_id');
                $save = $v->save();
            }
        } catch (\Exception $e) {
            return $this->error(lang("operation success"));
        }
        if($save) return  $this->success(lang('move success')) ;
        return $this->error(lang("move fail"));
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
        $list = $this->modelClass->where('id','in', $ids)->select();
        if (empty($list)) return $this->error('Data is not exist');
        try {
            foreach ($list as $v) {
                $path = str_replace('\\',DIRECTORY_SEPARATOR,$v->path);
                $path = str_replace('/',DIRECTORY_SEPARATOR,$v->path);
                if(file_exists(public_path() . $path)) {
                    @unlink(public_path() . 'public' .$path);
                }
                $save = $v->force(true)->delete();
            }
        } catch (\Exception $e) {
            return $this->error(lang("operation success"));
        }
        if($save) return $this->success(lang('operation success')) ;

        return $this->error(lang("Delete fail"));
    }

}

