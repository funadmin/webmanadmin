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
namespace fun\auth;

use support\Request;
use support\Response;

/**
 * api 入口文件基类，需要控制权限的控制器都应该继承该类
 */
class Api
{
    use Send;
    /**
     * @var \support\Request Request实例
     */
    protected $request;
    /**
     * @var
     * 客户端信息
     */
    protected $clientInfo;
    /**
     * 不需要鉴权方法
     */
    protected $noAuth = [];
    
    protected $member_id = '';

    /**
     * @param Request $request
     * @return Response|void
     */
    public function beforeAction(Request $request)
    {
        $this->request = $request;
        $this->group =  $request->input('group')?$request->input('group'):'api';
        //所有ajax请求的options预请求都会直接返回200，如果需要单独针对某个类中的方法，可以在路由规则中进行配置
        if ( request()->method() == 'OPTIONS' ) {
            return $this->success('success');
        }
        $oauth = new Oauth();
        if (!$oauth->match($this->noAuth) || $oauth->match($this->noAuth) && request()->header(config('api.authentication'))) {               //请求方法白名单
            $oauth = new Oauth();
            try {
                $this->clientInfo = $oauth->authenticate();
            }catch(\Exception $e) {
                return $this->error($e->getMessage(),[],401);
            }
        }
        if($this->clientInfo){
            $this->member_id = $this->clientInfo['member_id'];
        }
    }

    /**
     * 初始化
     * 检查请求类型，数据格式等
     */
    public function init()
    {

    }
    /**
     * 空方法
     */
    public function _empty()
    {
        return $this->error('empty method!');
    }
}