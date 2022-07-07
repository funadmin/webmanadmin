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
 * Date: 2021/5/19
 * Time: 17:28
 */

namespace app\backend\controller\sys;

use app\common\controller\Controller;
use app\common\model\Languages as LanguagesModel;
use app\common\service\AuthCloudService;
use fun\helper\FileHelper;
use fun\helper\HttpHelper;
use fun\helper\ZipHelper;
use think\Exception;
use think\facade\Db;
use app\common\annotation\ControllerAnnotation;
use app\common\annotation\NodeAnnotation;
use support\Request;
use support\View;

/**
 * @ControllerAnnotation(title="系统更新")
 * Class Upgrade
 * @package app\backend\controller\sys
 */
class Upgrade extends Controller
{

    protected $backup_dir;
    protected $authCloudService;
    protected $lockFile;
    protected $now_version;

    public function beforeAction(Request $request)
    {
        parent::beforeAction($request);
        $this->backup_dir = '../../backup/';
        $this->now_version = config('funadmin.version');
        $this->lockFile = '../../backup/'.$this->now_version.'.lock';
        $this->authCloudService = new AuthCloudService();
    }

    /**
     * @NodeAnnotation('List')
     * @return \Response|\support\Response|void
     * @throws \think\db\exception\BindParamException
     */
    public function index()
    {
        if (request()->isPost()) {
            $data = request()->post();
            $this->authCloudService->setUserParams($data);
            $result = $this->authCloudService->setApiUrl('')->setMethod('post')
                ->setParams($this->authCloudService->getUserParams())
                ->run();
            if ($result['code'] == 200) {
                $this->authCloudService->setAuth($result['data']);
                return $this->success(lang('login successful'));
            } else {
                return $this->error(lang('Login failed:' . $result['msg']));
            }
        }
        $data['now_version'] = $this->now_version;
        $version = Db::query('SELECT VERSION() AS ver');
        $view = [
            'url' => request()->url(),
            'document_root' => public_path(),
            'document_protocol' => base_path(),
            'server_os' => PHP_OS,
            'server_port' =>request()->getLocalPort(),
            'server_ip' =>  request()->getLocalIp(),
            'server_soft' => $_SERVER['OS'],
            'server_file' => $_SERVER['SCRIPT_FILENAME'],
            'php_version' => PHP_VERSION,
            'mysql_version' => $version[0]['ver'],
            'auth' => $this->authCloudService->getAuth() ? 1 : 0,
        ];
        $view = array_merge($data, $view);
        return fetch('', $view);
    }

    /**
     * @NodeAnnotation('check')
     * 检测版本信息
     * @return mixed
     */
    public function check()
    {
        if (!$this->authCloudService->getAuth()) {
            return $this->error(lang('请先登录FunAdmin系统'));
        }
        $params = [
            "ip" => request()->getRealIp(),
            "domain" => request()->domain(),
            "version" => $this->now_version,
        ];
        $result = $this->authCloudService->setApiUrl('api/v1.version/getVersion')
            ->setParams($params)->setHeader()->run();
        if ($result['code'] == 200) {
            session('upgradeInfo', $result['data']);
            $result['data']['content'] =  $result['data']['content']? explode("\n", $result['data']['content']):'';
            return $this->success('发现新的更新包', '', $result);
        } else {
            return $this->error('您现在是最新的版本');
        }
    }

    /**
     * @NodeAnnotation('backup');
     */
    public function backup()
    {
        if (request()->isPost()) {
            if (!$this->authCloudService->getAuth()) {
                return $this->error(lang('请先登录FunAdmin系统'));
            }
            if (!is_dir($this->backup_dir)) {
                FileHelper::mkdirs($this->backup_dir);
            }
            $zipFile = '../../backup/' . date('YmdHis') . '_v' . $this->now_version . '.zip';
            if (!is_file($zipFile)) {
                FileHelper::createFile($zipFile, '');
            }
            FileHelper::createFile('../../backup/'.$this->now_version.'.lock',time());
            ZipHelper::zip($zipFile, '../');
            return $this->success(lang('backup success'));
        }
    }

    /**
     * @NodeAnnotation('install')
     * @throws \Exception
     */
    public function install()
    {
        if (request()->isPost()) {
            if (!$this->authCloudService->getAuth()) {
                return $this->error(lang('请先登录FunAdmin系统'));
            }
            if(!file_exists($this->lockFile)){
                return $this->error('请先备份');
            }
            $updateInfo = session('upgradeInfo');
            $content = file_get_contents($updateInfo['file_url']);
            $filename = $updateInfo['version'];
            $fileDir = '../runtime/upgrade/';
            if (!is_dir($fileDir)) {
                FileHelper::mkdirs($fileDir);
            }
            $fileName = $fileDir . $filename . '.zip';
            @touch($fileName);
            file_put_contents($fileName, $content);
            ZipHelper::unzip($fileName, $file = $fileDir . $filename . '/');
            $dir = scandir($fileDir . $filename . '/');
            try {
                foreach ($dir as $k => $v) {
                    if ($v == '.' || $v == '..') continue;
                    $file = $fileDir . $filename . '/' . $v;
                    if ($v == 'upgrade.sql') {
                        importSqlData($file);
                    } else if (is_file($file)){
                        @copy($file,'../'.$v);
                    }else{
                        FileHelper::copyDir($file, '../' . $v);
                    }
                }
            }catch (\Exception $e){
                return $this->error($e->getMessage());
            }
            @unlink($fileName);
            FileHelper::delDir($fileDir . $filename);
            @unlink($this->lockFile);
            $version = $updateInfo['version'];
            session('upgradeInfo','');
            setConfig('../config/app.php','version',$version);
            return $this->success('更新成功');
        }
    }

}