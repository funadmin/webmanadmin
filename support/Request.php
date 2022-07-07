<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace support;

/**
 * Class Request
 * @package support
 */
class Request extends \Webman\Http\Request
{

    public function buildToken(string $name = '__token__', $type = 'md5'): string
    {
        $type  = is_callable($type) ? $type : 'md5';
        $token = call_user_func($type, $_SERVER['REQUEST_TIME_FLOAT']);
        request()->session()->set($name, $token);
        return $token;
    }
    public function checkToken(string $token = '__token__', array $data = []): bool
    {
        if (in_array($this->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;
        }

        if (!request()->session()->has($token)) {
            // 令牌数据无效
            return false;
        }

        // Header验证
        if (request()->header('X-CSRF-TOKEN') && request()->session()->get($token) === request()->header('X-CSRF-TOKEN')) {
            // 防止重复提交
            request()->session()->delete($token); // 验证完成销毁session
            return true;
        }

        if (empty($data)) {
            $data = request()->input();
        }

        // 令牌验证
        if (isset($data[$token]) && request()->session()->get($token) === $data[$token]) {
            // 防止重复提交
            request()->session()->delete($token); // 验证完成销毁session
            return true;
        }
        // 开启TOKEN重置
        request()->session()->delete($token);
        return false;
    }
    /**
     * @return bool
     */
    public function isPost(): bool
    {
        $getMethod = Request::method();
        return $getMethod == 'POST';
    }
    /**
     * @return bool
     */
    public function isGet(): bool
    {
        $getMethod = Request::method();
        return $getMethod == 'GET';
    }
}