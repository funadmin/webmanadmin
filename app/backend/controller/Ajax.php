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

namespace app\backend\controller;

use app\backend\model\AuthRule;
use app\backend\service\AuthService;
use app\common\controller\Controller;
use app\common\model\Attach as AttachModel;
use app\common\model\Config;
use app\common\service\UploadService;
use fun\helper\FileHelper;
use support\Request;
use think\Exception;
use think\facade\Cache;

class Ajax extends Controller
{
    public function beforeAction(Request $request)
    {
        parent::beforeAction($request);
        $this->modelClass = new AttachModel();
    }

    /**
     * @return \support\Response|void
     * 文件上传总入口 集成qiniu ali tenxunoss
     */
    public function uploads()
    {
        try {
            $upload = new UploadService();
            $result = $upload->uploads(0,session('admin.id'));
            return json($result);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * 刷新菜单
     */
    public function refreshmenu()
    {
        $request = request();
        $cate = AuthRule::where('menu_status', 1)->order('sort asc')->select()->toArray();
        $menulsit = (new AuthService($request))->menuhtml($cate);
        return $this->success('ok','',$menulsit);
    }

    /**
     * 获取图片列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */

    public function getList()
    {
        $path = request()->input('path', 'uploads');
        $paths = public_path().'/storage/' . $path;
        $type = request()->input('type', 'image');
        $list = FileHelper::getFileList($paths, $type);
        $post = ['state' => 'SUCCESS', 'start' => 0, 'total' => count($list), 'list' => []];
        $attach = AttachModel::where('mime', 'like', '%' . 'image' . '%')->select()->toArray();
        if ($list) {
            foreach ($list[0] as $k => $v) {
                $post['list'][$k]['url'] = str_replace(app()->getRootPath() . 'public', '', $v);
                $post['list'][$k]['mtime'] = mime_content_type($v);
            }
        }
        $post['list'] = array_merge($post['list'], $attach);
        return json($post);
    }
    /**
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * 获取附件列表
     */
    public function getAttach()
    {
        if (request()->isAjax()) {
            list($this->page, $this->pageSize, $sort, $where) = $this->buildParames();
            $where = [];
            if(request()->input('original_name')){
                $where[] =['original_name|id','like','%'.request()->input('original_name').'%'];
            }
            $count = $this->modelClass
                ->where($where)
                ->order($sort)
                ->count();
            $list = $this->modelClass->where($where)
                ->where($where)
                ->order($sort)
                ->page($this->page, $this->pageSize)
                ->select();
            $result = ['code' => 0, 'msg' => lang('operation success'), 'data' => $list, 'count' => $count];
            return ($result);
        }
    }
    /*
     * 清除缓存
    */
    public function clearcache()
    {
        $type = request()->input('type');
//        try {
//            switch ($type) {
//                case 'all':
//                    FileHelper::delDir(runtime_path().'/'.'logs');
//                    FileHelper::delDir(runtime_path().'/'.'logs');
//                    FileHelper::delDir($frontpath);
//                    break;
//                case 'backend':
//                    FileHelper::delDir(runtime_path());
//                    break;
//                case 'index':
//                    FileHelper::delDir($frontpath);
//                    break;
//            }
//        }catch(Exception $e){
//            return $this->error($e->getMessage());
//        }
//        FileHelper::delDir(base_path() . 'runtime' . DIRECTORY_SEPARATOR . 'temp');
//        FileHelper::delDir(base_path() . 'runtime' . DIRECTORY_SEPARATOR . 'views');
        if(Cache::clear()) return  $this->success('清除成功');
        return $this->error('清除失败');
    }

    /**
     * 自动加载语言函数
     * @return void
     */
    public function getLang()
    {
//        header('Content-Type: application/javascript');
        $name = request()->get("controllername");
        $name = strtolower(parse_name($name, 1));
        $addon = request()->get("addons");
        //默认只加载了控制器对应的语言名，你还根据控制器名来加载额外的语言包
        return jsonp($this->loadlang($name, $addon),'define');
    }
    public function setConfig()
    {
        $config = Config::where('code',request()->input('code'))->find();
        $result = $config?$config->save(['value'=>request()->input('value')]):'';
        Cache::clear();
        if($result) return $this->success(lang('operation success'));
        return $this->error(lang('operation failed'));
    }

}