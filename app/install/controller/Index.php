<?php

namespace app\install\controller;

use app\common\traits\Jump;
use app\common\controller\Controller;
use think\facade\Db;
use support\View;

class Index extends Controller
{
    use Jump;

    protected $config;
    //错误信息
    protected $msg = '';
    //安装文件
    protected $lockFile;
    //数据库
    protected $databaseConfigFile;
    //sql 文件
    protected $sqlFile = '';
    //mysql版本
    protected $mysqlVersion = '5.7';
    protected $phpVersion = '7.2';
    //database模板
    protected $databaseTpl = '';


    public function __construct()
    {
        parent::__construct(); // TODO: Change the autogenerated stub
        $this->databaseConfigFile = config_path() . "/thinkorm.php";
        $this->sqlFile = app_path() . "/install/funadmin.sql";
        $this->lockFile = public_path() . "/install.lock";
        $this->databaseTpl = app_path() . "/install/view/tpl/database.tpl";
        $this->config = [
            'siteName' => "funadmin-webmanadmin",
            'siteVersion' => config('funadmin.version'),
            'tablePrefix' => "fun_",
            'runtimePath' => runtime_path(),
            'lockFile' => $this->lockFile,
        ];

        if (request()->action != 'step4' && file_exists($this->lockFile)) {
            return $this->error('当前版本已经安装了，如果需要重新安装请先删除install.lock', '/');
        }
        View::assign('config', $this->config);
    }

    public function index()
    {
        return redirect(url('index/step1'));
    }

    public function step1()
    {
        return fetch('step1');

    }

    public function step2()
    {
        $data['php_version'] = PHP_VERSION;
        $data['pdo'] = extension_loaded("PDO");
        $data['mysqli'] = extension_loaded("mysqli");
        $data['open_basedir'] = ini_get('open_basedir');;
        $data['database'] = is_really_writable($this->databaseConfigFile);
        $data['gd_info'] = function_exists('gd_info') || class_exists('Imagick', false);
        return fetch('step2', ['data' => $data]);

    }

    public function step3()
    {
        // 检测环境页面
        if (request()->action === 'step3' && request()->isGet()) {
            return fetch('step3');
        }
        if (request()->action === 'step3' && request()->isPost()) {
            //执行安装
            $db['host'] = request()->post('hostname') ? request()->post('hostname') : '127.0.0.1';
            $db['port'] = request()->post('port') ?: '3306';
            //判断是否在主机头后面加上了端口号
            $hostData = explode(":", $db['host']);
            if (isset($hostData) && $hostData && is_array($hostData) && count($hostData) > 1) {
                $db['host'] = $hostData[0];
                $db['port'] = $hostData[1];
            }
            //mysql的账户相关
            $db['username'] = request()->post('username') ?: 'root';
            $db['password'] = request()->post('password') ?: 'root';
            $db['database'] = request()->post('database') ?: 'funadmin';
            $db['prefix'] = request()->post('prefix') ?: $this->config['tablePrefix'];
            $db['prefix'] = rtrim($db['prefix'], "_") . "_";
            $admin['username'] = request()->post('adminUserName') ?: 'admin';
            $admin['password'] = request()->post('adminPassword') ?: '123456';
            $admin['repassword'] = request()->post('rePassword') ?: '123456';
            $admin['email'] = request()->post('email') ?: 'admin@admin.com';
            if (file_exists($this->lockFile)) {
                return $this->error('当前版本已经安装了，如果需要重新安装请先删除install.lock');
            }
            //php 版本
            if (version_compare(PHP_VERSION, $this->phpVersion, '<')) {
                return $this->error('当前版本(" . PHP_VERSION . ")过低，请使用PHP{$this->phpVersion}以上版本');
            }
            if (!extension_loaded("PDO")) {
                return $this->error('当前未开启PDO，无法进行安装');
            }
            //判断两次输入是否一致
            if ($admin['password'] != $admin['repassword']) {
                return $this->error('两次输入密码不一致！');

            }
            if (!preg_match('/^[0-9a-z_$]{6,16}$/i', $admin['password'])) {
                return $this->error('密码必须6-16位,且必须包含字母和数字,不能有中文和空格');

            }
            if (!preg_match("/^\w+$/", $admin['username'])) {
                return $this->error('用户名只能输入字母、数字、下划线！');
            }
            if (strlen($admin['username']) < 3 || strlen($admin['username']) > 12) {
                return $this->error('用户名请输入3~12位字符！');
            }
            if (strlen($admin['password']) < 5 || strlen($admin['password']) > 16) {
                return $this->error('密码请输入5~16位字符！');
            }
            //检测能否读取安装文件
            $sql = @file_get_contents($this->sqlFile);
            if (!$sql) {
                return $this->error("无法读取{$this->sqlFile}文件，请检查是否有读权限");
            }
            // 连接数据库
            $link = @new \mysqli("{$db['host']}:{$db['port']}", $db['username'], $db['password']);
            if (mysqli_connect_errno()) {
                return $this->error(mysqli_connect_error());
            }
            $link->query("SET NAMES 'utf8mb4'");
            $link->query('set global wait_timeout=2147480');
            $link->query("set global interactive_timeout=2147480");
            $link->query("set global max_allowed_packet=104857600");
            //版本
            if (version_compare($link->server_info, $this->mysqlVersion, '<')) {
                return $this->error("MySQL数据库版本不能低于{$this->mysqlVersion},请将您的MySQL升级到{$this->mysqlVersion}及以上");
            }
            // 创建数据库并选中
            if (!$link->select_db($db['database'])) {
                $create_sql = 'CREATE DATABASE IF NOT EXISTS ' . $db['database'] . ' DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;';
                if (!$link->query($create_sql)) {
                    return $this->error('创建数据库失败');
                }
            }
            $link->select_db($db['database']);
            // 写入数据库
            $sql = file_get_contents($this->sqlFile);
            $sql = str_replace(["`fun_",'CREATE TABLE'], ["`{$db['prefix']}",'CREATE TABLE IF NOT EXISTS'], $sql);
            $config = config('database');
            $config['connections']['mysql'] = [
                // 数据库类型
                'type' => 'mysql',
                // 服务器地址
                'hostname' => $db['host'],
                // 数据库名
                'database' => $db['database'],
                // 数据库用户名
                'username' => $db['username'],
                // 数据库密码
                'password' => $db['password'],
                // 数据库连接端口
                'hostport' => $db['port'],
                // 数据库连接参数
                'params' => [],
                // 数据库编码默认采用utf8
                'charset' => 'utf8mb4',
                // 数据库表前缀
                'prefix' =>  getenv('DB_PREFIX')?:'fun_',
                // 断线重连
                'break_reconnect' => true,
                // 关闭SQL监听日志
                'trigger_sql' => false,
            ];
            try {
                Db::setConfig($config);
                $instance = Db::connect('mysql');
                $instance->execute("SELECT 1");     //如果是【数据】增删改查直接运行
                $instance->getPdo()->exec($sql);
                sleep(2);
                $password = password_hash($admin['password'], PASSWORD_BCRYPT);
                $instance->execute("UPDATE {$db['prefix']}admin SET `email`='{$admin['email']}',`username` = '{$admin['username']}',`password` = '{$password}' WHERE `username` = 'admin'");
                $instance->execute("UPDATE {$db['prefix']}member SET `email`='{$admin['email']}',`username` = '{$admin['username']}',`password` = '{$password}' WHERE `username` = 'admin'");
            } catch (\PDOException $e) {
                return $this->error($e->getMessage());
            }catch(\Exception $e){
                return $this->error($e->getMessage());
            }
            //替换数据库相关配置
            $putDatabase = str_replace(
                ['{{hostname}}', '{{database}}', '{{username}}', '{{password}}', '{{port}}', '{{prefix}}'],
                [$db['host'],$db['database'], $db['username'], $db['password'], $db['port'], $db['prefix']],
                file_get_contents($this->databaseTpl));
            $putConfig = @file_put_contents($this->databaseConfigFile, $putDatabase);
            if (!$putConfig) {
                return $this->error('安装失败、请确定database.php是否有写入权限');
            }
            $result = @touch($this->lockFile);
            if (!$result) {
                return $this->error("安装失败、请确定install.lock是否有写入权限");
            }
            $adminUser['username'] = $admin['username'];
            $adminUser['password'] = $admin['password'];
            request()->session()->set('admin_install', $adminUser);
            return $this->success('安装成功,安装后请重新启动程序');
        }
    }

    public function step4()
    {
        //完成安装
        if (request()->isPost()) {
            request()->session()->set('admin_install', '');
            return $this->success('OK');
        }
        return fetch('step4');
    }


}