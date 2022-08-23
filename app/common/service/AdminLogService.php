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
namespace app\common\service;

use app\backend\model\AdminLog;
use app\backend\model\AuthRule;
use support\Request;

class AdminLogService extends AbstractService
{
    /**
     * @param $config
     */
    public function __construct($config=[])
    {
        $this->config = is_array($config)?array_merge($config,$this->config):$config;
        $this->initialize();
    }

    /**
     * 初始化服务
     * @return $this
     */
    protected function initialize()
    {
        return $this;
    }


    /**
     * @return
     */
    public static function instance($config = [])
    {

        if (!self::$_instance instanceof self) {

            self::$_instance = new self($config);

        }

        return self::$_instance;

    }

    /**
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function save()
    {
        list($app,$controller,$action,$route,$url) = getNodeInfo();
        $post_data = json_encode(request()->post(),JSON_UNESCAPED_UNICODE);
        $get_data = json_encode(request()->get(),JSON_UNESCAPED_UNICODE);
        $header_data = json_encode( request()->header(),JSON_UNESCAPED_UNICODE);
        $method = request()->method();
        $ip = request()->getRealIp();
        $agent =request()->header('"user-agent');
        $module =  $app;
        $admin_id   = session('admin.id',0);
        $username   = session('admin.username','Unknown');
        $content    = request()->all();
        $title = '';
        if(array_key_exists('callback',$content) && $content['callback']=='define'){
            $title = '';
        }
        if (strpos($url, 'enlang') !== false &&  request()->isAjax()) {
            $title = '[切换语言]';
        }elseif (strpos($url, 'ajax/clearData') !== false &&  request()->isAjax()) {
            $title = '[清除缓存]';
        }elseif (strpos($url, 'login/index') !== false &&  request()->isAjax()) {
            $title = '[登录成功]';
            $username = json_decode($post_data,true)['username'];
        }else{
            //权限
            $auth = AuthRule::column('href','id');
            $auth = array_map('strtolower',$auth);
            $key = array_search($url,$auth);
            if($key>=0){
                $auth = AuthRule::where('id',$key)->find();
                if($auth) $title=$auth->title;
            }
        }
        //插入数据
        if (!empty($title) && $content) {
            AdminLog::create([
                'title'       => $title ? $title : '',
                'admin_id'    => $admin_id,
                'username'    => $username,
                'url'         => $url,
                'addons'      => 'app',
                'module'      => $module,
                'controller'      => $controller,
                'action'      => $action,
                'get_data'     => $get_data,
                'post_data'     => $post_data,
                'header_data'     => $header_data,
                'agent'       => $agent,
                'ip'          => $ip,
                'method'      => $method,
            ]);
        }
    }

}