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

use app\common\service\PredisService;
use fun\auth\Send;
use fun\auth\Oauth;
use think\facade\Db;
use support\Request;
use support\Response;

/**
 * 生成token
 */
class Token
{
    use Send;

    /**
     * @var string
     */
    public function __construct()
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Headers:Accept,Referer,Host,Keep-Alive,User-Agent,X-Requested-With,Cache-Control,Content-Type,Cookie,token');
        header('Access-Control-Allow-Credentials:true');
        header('Access-Control-Allow-Methods:GET, POST, PATCH, PUT, DELETE,OPTIONS');
        $this->request = request();
        $this->timeDif = config('api.timeDif')??$this->timeDif;
        $this->refreshExpires =config('api.timeDif')??$this->refreshExpires;
        $this->expires =config('api.timeDif')??$this->expires;
        $this->responseType = config('api.responseType')??$this->responseType;
        $this->responseType = config('api.responseType')??$this->responseType;
        $this->authapp = config('api.authapp')??$this->authapp;
        $this->group =  $this->request->input('group')?$this->request->input('group'):'api';
    }

    /**
     * 生成token
     */
    public function accessToken(Request $request)
    {
        //参数验证
        $validate = new \fun\auth\validate\Token;
        if ($this->authapp) {
            if (!$validate->scene('authapp')->check($request->post())) {
                return $this->error(lang($validate->getError()), '', 500);
            }
        } else {
            if (!$validate->scene('noauthapp')->check($request->post())) {
                return $this->error(lang($validate->getError()), '', 500);
            }
        }
        try {
            $this->checkParams($request->post());  //参数校验
            //数据库已经有一个用户,这里需要根据input('mobile')去数据库查找有没有这个用户
            $memberInfo = $this->getMember($request->post('username'), $request->post('password'));
            $client = $this->getClient($this->appid,$this->appsecret,'id,group');
            $accessToken = $this->setAccessToken(array_merge($memberInfo, ['client_id' => $client['id'],'appid'=>$request->post('appid')]));  //传入参数应该是根据手机号查询改用户的数据
        }catch (\Exception $e){
            return $this->error(lang($e->getMessage()), [], 401);
        }
        return $this->success(lang('get token success'), $accessToken);
    }

    /**
     * token 过期 刷新token
     */
    public function refresh()
    {
        $refresh_token = $this->request->post('refresh_token');
        $refresh_token_info = Db::name('oauth2_access_token')
            ->where('refresh_token',$refresh_token)
            ->where('tablename',$this->tableName)
            ->where('group',$this->group)->order('id desc')->find();
        if (!$refresh_token_info) {
            return $this->error('refresh_token is error', '', 401);
        } else {
            if ($refresh_token_info['refresh_expires_time'] <time()) {
                return $this->error('refresh_token is expired', '', 401);
            } else {    //重新给用户生成调用token
                $member =  Db::name($this->tableName)->where('status',1)->cache(3600)->find($refresh_token_info['member_id']);
                $client =  Db::name('oauth2_client')
                    ->field('appid')->find($refresh_token_info['client_id']);
                $memberInfo = array_merge($member,$client);
                $accessToken = $this->setAccessToken($memberInfo,$refresh_token);
                return $this->success('success', $accessToken);
            }
        }
    }

    /**
     * 参数检测和验证签名
     */
    protected function checkParams($params = [])
    {
        //时间戳校验
        if (abs($params['timestamp'] - time()) > $this->timeDif) {
            throw new \Exception(lang('请求时间戳与服务器时间戳异常'));
        }
        if ($this->authapp && $params['appid'] !== $this->appid) {
            //appid检测
            throw new \Exception(lang('appid 错误'));
        }
        if ($this->authapp && $params['appsecret'] !== $this->appsecret) {
            //appsecret，检测
            throw new \Exception(lang('appsecret 错误'));
        }
        if($this->authapp){
            $oauth2_client = Db::name('oauth2_client')
                ->where('appid', $params['appid'])
                ->where('appsecret', $params['appsecret'])
                ->field('id')
                ->find();
            if (!$oauth2_client) {
                throw new \Exception(lang('Invalid authorization client'));
            }
        }
        //签名检测
        $Oauth = new Oauth();
        $sign = $Oauth->makeSign($params, $this->appsecret);
        if ($sign !== $params['sign']) {
            throw new \Exception(lang('sign错误'));
        }
        return true;
    }

    /**
     * 设置AccessToken
     * @param $memberInfo
     * @return int
     */
    protected function setAccessToken($memberInfo,$refresh_token='')
    {
        $accessTokenInfo = [
            'access_token' => '',//访问令牌
            'expires_time' => time() + $this->expires,      //过期时间时间戳
            'refresh_token' => $refresh_token,//刷新的token
            'refresh_expires_time' => time() + $this->refreshExpires,      //过期时间时间戳
        ];
        $accessTokenInfo = array_merge($accessTokenInfo,$memberInfo);
        $driver = config('api.driver');
        if($driver =='redis'){
            $accessTokenInfo['access_token'] = $this->buildAccessToken();
            $accessTokenInfo['refresh_token'] = $this->buildAccessToken();
            $this->redis = new PredisService();
            $this->redis->set(config('api.redisTokenKey').$this->appid. $this->tableName .  $accessTokenInfo['access_token'],serialize($accessTokenInfo),$this->expires);
            $this->redis->set(config('api.redisRefreshTokenKey') . $this->appid . $this->tableName . $accessTokenInfo['refresh_token'],serialize($accessTokenInfo),$this->refreshExpires);
        }else{
            $token =  Db::name('oauth2_access_token')->where('member_id',$memberInfo['member_id'])
                ->where('tablename',$this->tableName)
                ->where('group',$this->group)
                ->order('id desc')->limit(1)
                ->find();
            if($token && $token['expires_time']>time() && !$refresh_token) {
                $accessTokenInfo['access_token'] = $token['access_token'];
                $accessTokenInfo['refresh_token'] = $token['refresh_token'];
                $accessTokenInfo['expires_time'] = $token['expires_time'];
                $accessTokenInfo['refresh_expires_time'] = $token['refresh_expires_time'];
            }else{
                $accessTokenInfo['access_token'] = $this->buildAccessToken();
                $accessTokenInfo['refresh_token'] = $this->getRefreshToken($memberInfo,$refresh_token);
            }
            $this->saveToken($accessTokenInfo);  //保存本次token
        }
        return $accessTokenInfo;
    }

    /**
     * 生成AccessToken
     * @return string
     */
    protected function buildAccessToken(string $name = '__token__', string $type = 'md5')
    {
        return buildToken($name,$type);
    }
    /**
     * 获取刷新用的token检测是否还有效
     */
    protected function getRefreshToken($memberInfo,$refresh_token)
    {
        if(!$refresh_token){
            return $this->buildAccessToken();
        }
        $accessToken =Db::name('oauth2_access_token')->where('member_id',$memberInfo['member_id'])
            ->where('refresh_token',$refresh_token)
            ->where('tablename',$this->tableName)
            ->where('group',$this->group)
            ->field('refresh_token')
            ->find();
        return $accessToken?$refresh_token:$this->buildAccessToken();
    }
    /**
     * 存储token
     * @param $accessTokenInfo
     */
    protected function saveToken($accessTokenInfo)
    {
        $accessToken =Db::name('oauth2_access_token')->where('member_id',$accessTokenInfo['member_id'])
            ->where('tablename',$this->tableName)
            ->where('group',$this->group)
            ->find();
        $data = [
            'client_id'=>$accessTokenInfo['client_id'],
            'member_id'=>$accessTokenInfo['member_id'],
            'tablename'=>$this->tableName,
            'group'=>$this->group,
            'openid'=>isset($accessTokenInfo['openid'])?$accessTokenInfo['openid']:'',
            'access_token'=>$accessTokenInfo['access_token'],
            'expires_time'=>time() + $this->expires,
            'refresh_token'=>$accessTokenInfo['refresh_token'],
            'refresh_expires_time' => time() + $this->refreshExpires,      //过期时间时间戳
            'create_time' => time()      //创建时间
        ];
        if(!$accessToken){
            Db::name('oauth2_access_token')->save($data);
        }else{
            Db::name('oauth2_access_token')->where('member_id',$accessTokenInfo['member_id'])
                ->where('group',$this->group)
                ->update($data);
        }
        return true;
    }

    protected function getMember($membername, $password)
    {
        $member = Db::name($this->tableName)
            ->where('status',1)
            ->where('username', $membername)
            ->whereOr('mobile', $membername)
            ->whereOr('email', $membername)
            ->field('id as member_id,password')
            ->cache($this->appid.$membername,3600)
            ->find();
        if ($member) {
            if (password_verify($password, $member['password'])) {
                unset($member['password']);
                return $member;
            } else {
                throw new \Exception(lang('Password is not right'));
            }
        } else {
            throw new \Exception(lang('Account is not exist'));
        }
    }
}
