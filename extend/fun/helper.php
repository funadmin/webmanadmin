<?php
/**
 * FunAdmin
 * ============================================================================
 * 版权所有 2017-2028 FunAdmin，并保留所有权利。
 * 网站地址: https://www.FunAdmin.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * Author: yuege
 * Date: 2019/10/3
 */
declare(strict_types=1);

use fun\addons\middleware\Addons;
use fun\addons\Service;
use fun\helper\FileHelper;
use think\Exception;
use think\facade\Db;
use think\facade\App;
use think\facade\Config;
use think\facade\Event;
use think\facade\Route;
use think\facade\Cache;
use think\helper\{
    Str, Arr
};

define('DS', DIRECTORY_SEPARATOR);


if (!function_exists('parse_name')) {
    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @param string $name    字符串
     * @param int    $type    转换类型
     * @param bool   $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    function parse_name(string $name, int $type = 0, bool $ucfirst = true): string
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);

            return $ucfirst ? ucfirst($name) : lcfirst($name);
        }

        return strtolower(trim(preg_replace('/[A-Z]/', '_\\0', $name), '_'));
    }
}
/**
 * 判断文件或目录是否有写的权限
 */
function is_really_writable($file)
{
    if (DIRECTORY_SEPARATOR == '/' && @ ini_get("safe_mode") == false) {
        return is_writable($file);
    }
    if (!is_file($file) || ($fp = @fopen($file, "r+")) === false) {
        return false;
    }
    fclose($fp);
    return true;
}

/**
 * 导入SQL
 *
 * @param string $name 插件名称
 * @return  boolean
 */
if (!function_exists('importsql')) {

    function importsql($name){
        $service = new Service(App::instance()); // 获取service 服务
        $addons_path = $service->getAddonsPath(); // 插件列表
        $sqlFile = $addons_path . $name . DS . 'install.sql';
        if (is_file($sqlFile)) {
            $gz = fopen($sqlFile, 'r');
            $sql = '';
            while(1) {
                $sql .= fgets($gz);
                if(preg_match('/.*;$/', trim($sql))) {
                    $sql = preg_replace('/(\/\*(\s|.)*?\*\/);/','',$sql);
                    $sql = str_replace('__PREFIX__', config('database.connections.mysql.prefix'),$sql);
                    if(strpos($sql,'CREATE TABLE')!==false || strpos($sql,'INSERT INTO')!==false || strpos($sql,'ALTER TABLE')!==false || strpos($sql,'DROP TABLE')!==false){
                        try {
                            Db::execute($sql);
                        } catch (\Exception $e) {
                            throw new Exception($e->getMessage());
                        }
                    }
                    $sql = '';
                }
                if(feof($gz)) break;
            }
        }
        return true;
    }
}


/**
 * 卸载SQL
 *
 * @param string $name 插件名称
 * @return  boolean
 */
if (!function_exists('uninstallsql')) {
     function uninstallsql($name)
    {
        $service = new Service(App::instance()); // 获取service 服务
        $addons_path = $service->getAddonsPath(); // 插件列表
        $sqlFile = $addons_path . $name . DS . 'uninstall.sql';
        if (is_file($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $sql = str_replace('__PREFIX__', config('database.connections.mysql.prefix'),$sql);
            $sql = explode("\r\n",$sql);
            foreach ($sql as $k=>$v){
                if(strpos(strtolower($v),'drop table')!==false){
                    try {
                        Db::execute($v);
                    } catch (\Exception $e) {
                        throw new Exception($e->getMessage());
                    }
                }
               
            }
        }
        return true;
    }
}

