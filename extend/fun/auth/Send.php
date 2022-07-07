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
 * Date: 2019/9/22
 */
namespace fun\auth;

use think\facade\Db;
use support\Response;

trait Send
{
    /**
     * 时间差
     * @var int
     */
    public  $timeDif = 10000;
    /**
     * 刷新有效期
     * @var float|int
     */
    public  $refreshExpires = 3600 * 24 * 30;   //刷新token过期时间
    /**
     * 有效期
     * @var float|int
     */
    public  $expires = 7200*12;
    /**
     * 多个用户表
     * @var string
     */
    public  $tableName = 'member';

    /**
     * redis 对象
     * @var 
     */
    public  $redis ;
    /**
     * 客户端对象
     * @var 
     */
    public  $client ;
    /**
     * @var bool
     * 是否需要验证数据库账号
     */
    public $authapp = false;
    /**
     * 测试appid，正式请数据库进行相关验证
     */
    protected $appid = 'funadmin';
    /**
     * appsecret
     */
    protected $appsecret = '692ffa52429dd7e2b1df280be0f8c83f';
    /**
     * JWT key
     * @var string 
     */
    public $key = '';
    
    public $group = 'api';

    /**
     * 返沪格式
     * @var string
     */
    public  $responseType = 'json';
    /**
     * 操作成功返回的数据
     * @param string $msg 提示信息
     * @param mixed $data 要返回的数据
     * @param int $code 错误码，默认为1
     * @param string $type 输出类型
     * @param array $header 发送的 Header 信息
     */
    public  function success($msg = '', $data = [], $code = 200, $type = null, array $header = [])
    {
        return $this->result($msg, $data, $code, $type, $header);
    }

    /**
     * 操作失败返回的数据
     * @param string $msg 提示信息
     * @param mixed $data 要返回的数据
     * @param int $code 错误码，默认为0
     * @param string $type 输出类型
     * @param array $header 发送的 Header 信息
     */
    public function error($msg = '', $data = [], $code = 404, $type = null, array $header = [])
    {
        return $this->result($msg, $data, $code, $type, $header);
    }

    protected  function result($msg, $data = [], $code = 404, $type = null, array $header = [])
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'time' => time(),
            'data' => $data,
        ];
        return json($result)->withHeaders($header);
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
    protected function getClient($appid='',$appsecret='',$field='*'){

        return Db::name('oauth2_client')->where('appid',$appid)->where('appsecret',$appsecret)->field($field)->cache($appid.$appsecret,$this->refreshExpires)->find();
    }
}

