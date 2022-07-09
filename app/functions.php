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
 * Date: 2021/8/2
 */

use think\facade\Cache;
use think\facade\Db;
use think\Template;

if (!function_exists('hook')) {

    /**
     * @param $event
     * @param $params
     * @return mixed
     */
    function hook($event, $params = null)
    {
        return  \Webman\Event\Event::emit($event, $params);
    }
}
if (!function_exists('captcha_check')) {
    function captcha_check($captcha)
    {
        // 对比session中的captcha值
        if (strtolower($captcha) !== request()->session()->get('captcha')) {
            return false;
        }
        return true;
    }
}

if (!function_exists('auth')) {
    function auth($url)
    {
        $auth = new \app\backend\service\AuthService();
        return $auth->authNode($url);
    }
}
if (!function_exists('fetch')) {
    /**
     * @param $template
     * @param array $vars
     * @param null $app
     * @return Response
     */
    function fetch($template = '', $vars = [], $app = null)
    {
        list($app, $controller, $action, $route, $url) = getNodeInfo();
        if (!$template) {
            $template = $controller . '/' . $action;
        } elseif (strpos(trim($template, '/'), '/') === false) {
            $template = $controller . '/' . trim($template, '/');
        }
        return view($template, $vars, $app);
    }
}

if (!function_exists('__')) {
    /**
     * 多语言函数
     * @param $str
     * @param $vars
     * @return array|float|int|mixed|string|string[]
     */
    function __($str, $vars = [])
    {
        if (is_numeric($str) || empty($str)) {
            return $str;
        }
        if (!is_array($vars)) {
            $vars = func_get_args();
            array_shift($vars);
        }
        return lang($str, $vars);
    }
}
if (!function_exists('lang')) {
    /**
     * 语言
     * @param $id
     * @param $parameters
     * @param $domain
     * @param $locale
     * @return array|mixed|string|string[]
     */
    function lang($id, $parameters = [], $domain = null, $locale = null)
    {
        list($app, $controller, $action, $route, $url) = getNodeInfo();
        if ($domain == null) $domain = $app;
        if ($locale == null) $locale = locale();
        $name = $controller;
        $lang = loadLang($name, $domain, $locale);
        $id = strtolower($id);
        $value = array_key_exists($id, $lang) ? $lang[$id] : $id;
        // 变量解析
        if (!empty($parameters) && is_array($parameters)) {
            /**
             * Notes:
             * 为了检测的方便，数字索引的判断仅仅是参数数组的第一个元素的key为数字0
             * 数字索引采用的是系统的 sprintf 函数替换，用法请参考 sprintf 函数
             */
            if (key($parameters) === 0) {
                // 数字索引解析
                array_unshift($parameters, $value);
                $value = call_user_func_array('sprintf', $parameters);
            } else {
                // 关联索引解析
                $replace = array_keys($parameters);
                foreach ($replace as &$v) {
                    $v = "{:{$v}}";
                }
                $value = str_replace($replace, $parameters, $value);
            }
        }
        return $value;
    }
}
if (!function_exists('loadLang')) {
    /**
     *      * 语言

     * @param $name
     * @param $domain
     * @param $locale
     * @return array|mixed
     */
    function loadLang($name= null, $domain = null, $locale = null)
    {
        $domain = $domain?:$domain = explode('\\', request()->controller)[1];
        $locale = $locale ?: locale();
        $config = config('translation');
        $file = $config['path'] . '/' . locale() . '/' . $domain . '/' . locale() . '.php';
        $lang = [];
        if (file_exists($file)) {
            $file = include $file;
            $lang = $file + $lang;
        }
        $file = $config['path'] . '/' . locale() . '/' . $domain . '/' . $name . '.php';
        if (file_exists($file)) {
            $file = include $file;
            $lang = $file + $lang;
        }

        return array_change_key_case($lang);
    }
}
if (!function_exists('getNodeInfo')) {
    /**
     * 获取访问的节点数据
     * @return array
     */
    function getNodeInfo(): array
    {
        $app = request()->app;
        $controller = request()->controller;
        $action = request()->action;
        $route = request()->route;
        $controller = str_replace("app\\$app\\controller\\", '', $controller);
        $controller = str_replace("\\", '/', $controller);
        $controller = strtolower($controller);
        $url = '/' . $app . '/' . $controller . '/' . $action;
        return [$app, $controller, $action, $route, $url];
    }
}
if (!function_exists('parse_name')) {
    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @param string $name 字符串
     * @param int $type 转换类型
     * @param bool $ucfirst 首字母是否大写（驼峰规则）
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

if (!function_exists('token')) {
    /**
     * 获取Token令牌
     * @param string $name 令牌名称
     * @param mixed $type 令牌生成方法
     * @return string
     */
    function token(string $name = '__token__', string $type = 'md5'): string
    {
        return buildToken($name, $type);
    }
}

if (!function_exists('token_field')) {
    /**
     * 生成令牌隐藏表单
     * @param string $name 令牌名称
     * @param mixed $type 令牌生成方法
     * @return string
     */
    function token_field(string $name = '__token__', string $type = 'md5'): string
    {
        $token = buildToken($name, $type);

        return '<input type="hidden" name="' . $name . '" value="' . $token . '" />';
    }
}

if (!function_exists('token_meta')) {
    /**
     * 生成令牌meta
     * @param string $name 令牌名称
     * @param mixed $type 令牌生成方法
     * @return string
     */
    function token_meta(string $name = '__token__', string $type = 'md5'): string
    {
        $token = buildToken($name, $type);

        return '<meta name="csrf-token" content="' . $token . '">';
    }
}

if (!function_exists('buildToken')) {

    function buildToken(string $name = '__token__', $type = 'md5')
    {
        $type = is_callable($type) ? $type : 'md5';
        $token = call_user_func($type, $_SERVER['REQUEST_TIME_FLOAT']);
        request()->session()->set($name, $token);
        return $token;
    }
}

if (!function_exists('syscfg')) {
    /**
     * @param $group
     * @param null $code
     */
    function syscfg($group, $code = null)
    {
        $where = ['group' => $group];
        $value = empty($code) ? Cache::get("syscfg_{$group}") : Cache::get("syscfg_{$group}_{$code}");
        if (!empty($value)) {
            return $value;
        }
        if (!empty($code)) {
            $where['code'] = $code;
            $value = \app\common\model\Config::where($where)->value('value');
            Cache::set("syscfg_{$group}_{$code}", $value, 3600);
        } else {
            $value = \app\common\model\Config::where($where)->column('value', 'code');
            Cache::set("syscfg_{$group}", $value, 3600);
        }
        return $value;

    }
}

//重写url 助手函数
if (!function_exists('__u')) {

    function __u($url = '', array $vars = [])
    {
        $app =  request()->app;
        $vars = !empty($vars) ? '?' . http_build_query($vars) : '';
        if(\think\helper\Str::startsWith($url,'/')){
            return $url.$vars;
        }
        return '/' . $app . '/' . trim(str_replace('/'.$app .'/' , '', $url)) . $vars;
    }
}

//重写url 助手函数
if (!function_exists('url')) {

    function url($url = '', array $vars = [])
    {
        $app =  request()->app;
        $vars = !empty($vars) ? '?' . http_build_query($vars) : '';
        if(\think\helper\Str::startsWith($url,'/')){
            return $url.$vars;
        }
        return '/' . $app . '/' . trim(str_replace('/'.$app .'/' , '', $url)) . $vars;
    }
}


if (!function_exists("_getProvicesByPid")) {
    function _getProvicesByPid($pid = 0)
    {
        return \think\facade\Db::name('provinces')->cache(true)->find($pid);
    }
}

if (!function_exists("_getMember")) {
    function _getMember($id)
    {
        $member = \think\facade\Db::name('member')->cache(true)->find($id);
        if ($member) {
            return $member;
        }
        return [];
    }
}
/**
 * 打印
 */
if (!function_exists('p')) {
    function p($var, $die = 0)
    {
        print_r($var);
        $die && die();
    }
}
/**
 * 手机
 */
if (!function_exists('isMobile')) {

    function isMobile()
    {
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        if (isset ($_SERVER['HTTP_VIA'])) {
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia',
                'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel',
                'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce',
                'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile'
            );
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
}

//是否https;

if (!function_exists('isHttps')) {
    function isHttps()
    {
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }
}

/**
 * 获取http类型
 */
if (!function_exists('httpType')) {
    /**
     * http 类型
     * @return string
     */
    function httpType()
    {
        return $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

    }
}

if (!function_exists('timeAgo')) {
    /**
     * 从前
     * @param $posttime
     * @return string
     */
    function timeAgo($posttime)
    {
        //当前时间的时间戳
        $nowtimes = strtotime(date('Y-m-d H:i:s'), time());
        //之前时间参数的时间戳
        $posttimes = strtotime($posttime);
        //相差时间戳
        $counttime = $nowtimes - $posttimes;
        //进行时间转换
        if ($counttime <= 10) {
            return '刚刚';
        } else if ($counttime > 10 && $counttime <= 30) {
            return '刚才';
        } else if ($counttime > 30 && $counttime <= 60) {
            return '刚一会';
        } else if ($counttime > 60 && $counttime <= 120) {
            return '1分钟前';
        } else if ($counttime > 120 && $counttime <= 180) {
            return '2分钟前';
        } else if ($counttime > 180 && $counttime < 3600) {
            return intval(($counttime / 60)) . '分钟前';
        } else if ($counttime >= 3600 && $counttime < 3600 * 24) {
            return intval(($counttime / 3600)) . '小时前';
        } else if ($counttime >= 3600 * 24 && $counttime < 3600 * 24 * 2) {
            return '昨天';
        } else if ($counttime >= 3600 * 24 * 2 && $counttime < 3600 * 24 * 3) {
            return '前天';
        } else if ($counttime >= 3600 * 24 * 3 && $counttime <= 3600 * 24 * 20) {
            return intval(($counttime / (3600 * 24))) . '天前';
        } else {
            return $posttime;
        }
    }

    /**
     * 导入数据库
     */
    if (!function_exists('importSqlData')) {
        /**
         * http 类型
         * @return string
         */
        function importSqlData($sqlFile)
        {
            $lines = file($sqlFile);
            $sqlLine = '';
            foreach ($lines as $line) {
                if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 2) == '/*')
                    continue;
                $sqlLine .= $line;
                if (substr(trim($line), -1, 1) == ';' and $line != 'COMMIT;') {
                    $sqlLine = str_ireplace('fun_', config('database.connections.mysql.prefix'), $sqlLine);
                    $sqlLine = str_ireplace('__PREFIX__', config('database.connections.mysql.prefix'), $sqlLine);
                    $sqlLine = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $sqlLine);
                    try {
                        Db::execute($sqlLine);
                    } catch (\PDOException $e) {
                        throw new PDOException($e->getMessage());
                    }
                    $sqlLine = '';
                }
            }
        }
    }

    /**
     * 动态永久修改 config 文件内容
     * @param $key
     * @param $value
     * @return bool|int
     */
    if (!function_exists('setConfig')) {
        function setConfig($configFile, $key, $value)
        {
            $config = file_get_contents($configFile); //加载配置文件
            $config = preg_replace("/'{$key}'.*?=>.*?'.*?'/", "'{$key}' => '{$value}'", $config);
            return file_put_contents($configFile, $config); // 写入配置文件
        }
    }

}

/**
 * 权限 文件内容
 * @param $key
 * @param $value
 * @return bool|int
 */
if (!function_exists('auth')) {
    function auth($url)
    {
        $auth = new \app\backend\service\AuthService();
        return $auth->authNode($url);
    }
}


/**
 * 是否登录
 * @param $key
 * @param $value
 * @return bool|int
 */
if (!function_exists('isLogin')) {
    function isLogin()
    {
        if (session('member')) {
            $_COOKIE['mid'] = session('member.id');
            return session('member');
        } else if (!empty($_COOKIE['mid'])) {
            $member = \app\common\model\Member::find($_COOKIE['mid']);
            request()->session()->set('member', $member);
            return $member;
        } else {
            return false;
        }
    }
}

/**
 * 获取版本号
 * @param $key
 * @param $value
 * @return bool|int
 */
if (!function_exists('getVersion')) {
    function getVersion()
    {
        return WEBMAN_VERSION;
    }
}