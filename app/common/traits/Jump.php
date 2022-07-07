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

namespace app\common\traits;


use support\Request;

trait Jump
{
    /**
     * 操作成功跳转的快捷方法
     * @param $msg
     * @param string|null $url
     * @param $data
     * @param int $wait
     * @param array $header
     * @return \support\Response
     */
    protected function success($msg = '', string $url = null, $data = '', int $wait = 3, array $header = [])
    {
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
        }
        $result = [
            'code' => 1,
            'msg' => $msg,
            'data' => $data,
            'url' => $url,
            'wait' => $wait,
        ];
        $type = $this->getResponseType();
        $result['__token__'] = request()->buildToken();
        if ('html' == strtolower($type)) {
            $response = view(config('funadmin.dispatch_jump_tpl'), $result);
        } else {
            $response = json($result)->withHeaders($header);;
        }

        return $response;
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param mixed $msg 提示信息
     * @param string $url 跳转的URL地址
     * @param mixed $data 返回的数据
     * @param integer $wait 跳转等待时间
     * @param array $header 发送的Header信息
     * @return void
     */
    protected function error($msg = '', string $url = null, $data = '', int $wait = 3, array $header = [])
    {
        if (is_null($url)) {
            $url =  request()->isAjax() ? '' : 'javascript:history.back(-1);';
        }
        $result = [
            'code' => 0,
            'msg' => $msg,
            'data' => $data,
            'url' => $url,
            'wait' => $wait,
        ];
        $type = $this->getResponseType();
        $result['__token__'] = request()->buildToken();
        $result['type'] = $type;
        if ('html' == strtolower($type)) {
            $response = view(config('funadmin.dispatch_error_tmpl'), $result);
        } else {
            $response = json($result)->withHeaders($header);
        }
        return $response;
    }

    /**
     * 返回封装后的API数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param integer $code 返回的code
     * @param mixed $msg 提示信息
     * @param string $type 返回数据格式
     * @param array $header 发送的Header信息
     * @return void
     */
    protected function result($data, $code = 0, $msg = '', $type = '', array $header = [])
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'time' => time(),
            'data' => $data,
        ];
        $header['__token__'] =  request()->buildToken();

        return json($result)->withHeaders($header);
    }

    /**
     * URL重定向
     * @access protected
     * @param string $url 跳转的URL表达式
     * @param integer $code http code
     * @param array $with 隐式传参
     * @return void
     */
    protected function redirect($url, $code = 302, $headers = [])
    {
        return redirect($url, $code, $headers);
    }

    /**
     * 获取当前的response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType()
    {
        return request()->isAjax() ? 'json' : 'html';
    }
}
