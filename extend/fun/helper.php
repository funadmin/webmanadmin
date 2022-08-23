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








// Form别名
if (!class_exists('Form')) {
    class_alias('fun\\Form', 'Form');
}

use fun\helper\FormHelper;

if (!function_exists('form_token')) {
    /**
     * @param string $name
     * @param string $type
     * @param array $options
     * @param '' $value
     * @return string
     */
    function form_token($name = '__token__', $type = 'md5')
    {
        return (new FormHelper())->token($name , $type);
    }
}

if (!function_exists('form_input')) {
    /**
     * @param string $name
     * @param string $type
     * @param array $options
     * @param '' $value
     * @return string
     */
    function form_input($name = '', $type = 'text', $options = [], $value = '')
    {
        return (new FormHelper())->input($name, $type, $options, $value);
    }
}

if (!function_exists('form_text')) {
    /**
     * @param $name
     * @param $options
     * @param $value
     * @return string
     */
    function form_text($name = '', $options = [], $value = '')
    {
        return (new FormHelper())->text($name,$options, $value);
    }
}
if (!function_exists('form_password')) {
    /**
     * @param $name
     * @param $options
     * @param $value
     * @return string
     */
    function form_password($name = '', $options = [], $value = '')
    {
        return (new FormHelper())->password($name,$options, $value);
    }
}
if (!function_exists('form_hidden')) {
    /**
     * @param $name
     * @param $options
     * @param $value
     * @return string
     */
    function form_hidden($name = '', $options = [], $value = '')
    {
        return (new FormHelper())->hidden($name,$options, $value);
    }
}
if (!function_exists('form_number')) {
    /**
     * @param $name
     * @param $options
     * @param $value
     * @return string
     */
    function form_number($name = '', $options = [], $value = '')
    {
        return (new FormHelper())->number($name,$options, $value);
    }
}
if (!function_exists('form_range')) {
    /**
     * @param $name
     * @param $options
     * @param $value
     * @return string
     */
    function form_range($name = '', $options = [], $value = '')
    {
        return (new FormHelper())->range($name,$options, $value);
    }
}
if (!function_exists('form_url')) {
    /**
     * @param $name
     * @param $options
     * @param $value
     * @return string
     */
    function form_url($name = '', $options = [], $value = '')
    {
        return (new FormHelper())->url($name,$options, $value);
    }
}
if (!function_exists('form_tel')) {
    /**
     * @param $name
     * @param $options
     * @param $value
     * @return string
     */
    function form_tel($name = '', $options = [], $value = '')
    {
        return (new FormHelper())->tel($name,$options, $value);
    }
}


if (!function_exists('form_email')) {
    /**
     * @param $name
     * @param $options
     * @param $value
     * @return string
     */
    function form_email($name = '', $options = [], $value = '')
    {
        return (new FormHelper())->email($name,$options, $value);
    }
}
if (!function_exists('form_rate')) {
    /**
     * 评分
     * @param string $name
     * @param array $options
     * @param '' $value
     * @return string
     */
    function form_rate($name = '', $options = [], $value = '')
    {
        return (new FormHelper())->rate($name, $options, $value);
    }
}

if (!function_exists('form_slider')) {
    /**
     * 滑块
     * @param string $name
     * @param array $options
     * @param '' $value
     * @return string
     */
    function form_slider($name = '', $options = [], $value = '')
    {
        return (new FormHelper())->slider($name, $options, $value);
    }
}
if (!function_exists('form_radio')) {
    /**
     * @param '' $name
     * @param '' $radiolist
     * @param array $options
     * @param string $value
     * @return string
     */
    function form_radio($name = '', $radiolist = '', $options = [], $value = '')
    {
        return (new FormHelper())->radio($name, $radiolist, $options, $value);
    }
}
if (!function_exists('form_switchs')) {
    /**
     * @param $name
     * @param $switch
     * @param $option
     * @param $value
     * @return string
     */
    function form_switchs($name='', $switch = [], $option = [], $value = '')
    {
        return (new FormHelper())->switchs($name, $switch, $option, $value);
    }
}
if (!function_exists('form_switch')) {
    /**
     * @param $name
     * @param $switch
     * @param $option
     * @param $value
     * @return string
     */
    function form_switch($name='', $switch = [], $option = [], $value = '')
    {
        return (new FormHelper())->switchs($name, $switch, $option, $value);
    }
}
if (!function_exists('form_checkbox')) {
    /**
     * @param $name
     * @return string
     */
    function form_checkbox($name ='', $list = [], $option = [], $value = '')
    {
        return (new FormHelper())->checkbox($name, $list, $option, $value);
    }
}

if (!function_exists('form_arrays')) {
    /**
     * @param $name
     * @return string
     */
    function form_arrays($name='', $list = [], $option = [])
    {
        return (new FormHelper())->arrays($name, $list, $option);
    }
}


if (!function_exists('form_textarea')) {
    /**
     * @param $name
     * @return string
     */
    function form_textarea($name = '', $option = [], $value = '')
    {
        return (new FormHelper())->textarea($name, $option, $value);
    }
}
if (!function_exists('form_select')) {
    /**
     * @param '' $name
     * @param array $options
     * @return string
     */
    function form_select($name = '', $select = [], $options = [], $attr = '', $value = '')
    {
        if (!empty($attr) and !is_array($attr)) $attr = explode(',', $attr);
        if (!empty($value) and !is_array($value)) $value = explode(',', $value);
        return (new FormHelper())->multiselect($name, $select, $options, $attr, $value);
    }
}
if (!function_exists('form_multiselect')) {
    /**
     * @param $name
     * @param $select
     * @param $options
     * @param $attr
     * @param $value
     * @return string
     */
    function form_multiselect($name = '', $select = [], $options = [], $attr = '', $value = '')
    {
        if (!empty($attr) and !is_array($attr)) $attr = explode(',', $attr);
        return (new FormHelper())->multiselect($name, $select, $options, $attr, $value);
    }
}
if (!function_exists('form_selectplus')) {
    /**
     * @param $name
     * @param $select
     * @param $options
     * @param $attr
     * @param $value
     * @return string
     */
    function form_selectplus($name = '', $select = [], $options = [], $attr = '', $value = '')
    {
        if (!empty($attr) and !is_array($attr)) $attr = explode(',', $attr);
        return (new FormHelper())->selectplus($name, $select, $options, $attr, $value);
    }
}
if (!function_exists('form_selectn')) {
    /**
     * @param $name
     * @param $select
     * @param $options
     * @param $attr
     * @param $value
     * @return string
     */
    function form_selectn($name = '', $select = [], $options = [], $attr = '', $value = '')
    {
        if (!empty($attr) and !is_array($attr)) $attr = explode(',', $attr);
        return (new FormHelper())->selectn($name, $select, $options, $attr, $value);
    }
}
if (!function_exists('form_xmselect')) {
    /**
     * @param '' $name
     * @param array $options
     * @return string
     */
    function form_xmselect($name = '', $select = [], $options = [], $attr = '', $value = '')
    {
        if (!empty($attr) and is_array($attr)) $attr = implode(',', $attr);
        return (new FormHelper())->xmselect($name, $select, $options, $attr, $value);
    }
}
if (!function_exists('form_icon')) {
    /**
     * @param array $options
     * @return string
     */

    function form_icon($name = '', $options = [], $value = '')
    {
        return (new FormHelper())->icon($name, $options, $value);
    }
}

if (!function_exists('form_date')) {
    /**
     * @param array $options
     * @return string
     */

    function form_date($name = '', $options = [], $value = '')
    {
        return (new FormHelper())->date($name, $options, $value);
    }
}

if (!function_exists('form_city')) {
    /**
     * @param array $options
     * @return string
     */

    function form_city($name = 'cityPicker', $options = [])
    {
        return (new FormHelper())->city($name, $options);
    }
}
if (!function_exists('form_region')) {
    /**
     * @param array $options
     * @return string
     */

    function form_region($name = 'regionCheck', $options = [])
    {
        return (new FormHelper())->region($name, $options);
    }
}
if (!function_exists('form_tags')) {
    /**
     * @param array $options
     * @return string
     */

    function form_tags($name = '', $options = [], $value = '')
    {
        $value = is_array($value) ? implode(',', $value) : $value;
        return (new FormHelper())->tags($name, $options, $value);
    }
}
if (!function_exists('form_color')) {
    /**
     * @param array $options
     * @return string
     */

    function form_color($name = '', $options = [], $value = '')
    {
        return (new FormHelper())->color($name, $options, $value);
    }
}

if (!function_exists('form_label')) {
    /**
     * @param bool $reset
     * @param array $options
     * @return string
     */
    function form_label($label = '', $options = [])
    {
        return (new FormHelper())->label($label, $options);
    }
}
if (!function_exists('form_submitbtn')) {
    /**
     * @param bool $reset
     * @param array $options
     * @return string
     */
    function form_submitbtn($reset = true, $options = [])
    {
        return (new FormHelper())->submitbtn($reset, $options);
    }
}
if (!function_exists('form_closebtn')) {
    /**
     * @param bool $reset
     * @param array $options
     * @return string
     */
    function form_closebtn($reset = true, $options = [])
    {
        return (new FormHelper())->closebtn($reset, $options);
    }
}
if (!function_exists('form_upload')) {
    /**
     * @param $name
     * @param '' $formdata
     * @return string
     */
    function form_upload($name = '', $formdata = [], $options = [], $value = '')
    {
        return (new FormHelper())->upload($name, $formdata, $options, $value);
    }
}
if (!function_exists('form_editor')) {
    /**
     * @param $name
     * @return string
     */
    function form_editor($name = 'content', $type = 1, $options = [], $value = '')
    {
        return (new FormHelper())->editor($name, $type, $options, $value);
    }
}
if (!function_exists('form_selectpage')) {
    /**
     * @param $name
     * @return string
     */
    function form_selectpage($name = 'selectpage', $list = [], $options = [], $value=null)
    {
        return (new FormHelper())->selectpage($name, $list, $options, $value);
    }
}
