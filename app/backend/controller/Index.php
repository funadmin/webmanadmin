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
use think\facade\Db;
use support\View;
use think\facade\Cache;

class Index extends Controller
{
    /**
     * @return string
     * @throws \Exception
     * 首页
     */
    public function index()
    {
        $menulist = Cache::get('adminmenushtml' . session('admin.id'));
        if (!$menulist) {
            $cate = AuthRule::where('menu_status', 1)
                ->where('type', 1)
                ->where('menu_status', 1)
                ->where('status', 1)
                ->order('sort asc')->select()->toArray();
            $menulist = (new AuthService())->menuhtml($cate, false);
            Cache::set('adminmenushtml' . session('admin.id'), $menulist, ['expire' => 3600]);
        }
        $languages = Db::name('languages')->cache(3600)->select();
        View::assign('menulist', $menulist);
        View::assign('languages', $languages);
        return fetch('');
    }

    /**
     * @return \support\View
     */
    public function console()
    {
        $version = Db::query('SELECT VERSION() AS ver');
        $main_config = Cache::get('main_config');
        if (!$main_config) {
            $config = [
//                'url' => $_SERVER['HTTP_HOST'],
//                'document_root' => $_SERVER['DOCUMENT_ROOT'],
//                'document_protocol' => $_SERVER['SERVER_PROTOCOL'],
//                'server_os' => PHP_OS,
//                'server_port' => $_SERVER['SERVER_PORT'],
//                'server_ip' => $_SERVER['REMOTE_ADDR'],
//                'server_soft' => $_SERVER['SERVER_SOFTWARE'],
//                'server_file' => $_SERVER['SCRIPT_FILENAME'],
//                'php_version' => PHP_VERSION,
//                'mysql_version' => $version[0]['ver'],
//                'max_upload_size' => ini_get('upload_max_filesize'),
            ];
            Cache::set('main_config', $config, 3600);
        }
        return fetch('', ['main_config' => $main_config]);
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        request()->session()->flush();
        Cache::clear();
        return $this->success(lang('Logout success'), __u('login/index'));
    }


}