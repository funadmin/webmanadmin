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

use support\Response;
use support\Request;
use support\Container;
/**
 * 自定义服务基类
 * Class Service
 * @package think\admin
 */
class AbstractService
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var null
     */
    protected static $_instance = null;

    protected $config= [] ;

    public function beforeAction(Request $request){

    }

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

}