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
use support\Request;
use think\facade\Db;
use Firebase\JWT\JWT;
/**
 * 生成token
 */
class JwtToken
{
    use Send;
    /**
     * 构造方法
     * @param Request $request Request对象
     */
    public function __construct()
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Headers:Accept,Referer,Host,Keep-Alive,User-Agent,X-Requested-With,Cache-Control,Content-Type,Cookie,token');
        header('Access-Control-Allow-Credentials:true');
        header('Access-Control-Allow-Methods:GET, POST, PATCH, PUT, DELETE,OPTIONS');
        $this->request = request();
        $this->key = md5(config('api.jwt_key'));
        $this->timeDif = config('api.timeDif')??$this->timeDif;
        $this->refreshExpires =config('api.refreshExpires')??$this->refreshExpires;
        $this->expires =config('api.expires')??$this->expires;
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
        if($this->authapp){
            if (!$validate->scene('authappjwt')->check($request->all())) {
                return $this->error(lang($validate->getError()), '', 500);
            }
        }else {
            if (!$validate->scene('jwt')->check($request->all())) {
                return $this->error(lang($validate->getError()), '', 500);
            }
        }
        //参数校验
        try {
            $result = $this->checkParams($request->all());
            $memberInfo = $this->getMember($request->post('username'),$request->post('password'));
            $client = $this->getClient($this->appid,$this->appsecret,'id,group');
            $accessToken = $this->setAccessToken(array_merge($memberInfo, ['client_id' => $client['id'],'appid'=>$this->appid]));
        }catch(\Exception $e){
            return $this->error(lang($e->getMessage()), [], 401);
        }
        return $this->success(lang('get token success'), $accessToken);

    }
    /**
     * 设置AccessToken
     * @param $memberInfo
     * @return int
     */
    protected function setAccessToken($memberInfo,$refresh_token='')
    {
        $accessTokenInfo = [
            'expires_time'=>time()+$this->expires,
            'refresh_expires_time'=>time()+$this->refreshExpires,
        ];
        $accessTokenInfo = array_merge($accessTokenInfo,$memberInfo);
        $driver = config('api.driver');
        if($driver =='redis'){
            $accessTokenInfo['access_token'] = $this->buildAccessToken($memberInfo,$this->expires);
            $accessTokenInfo['refresh_token'] = $this->buildAccessToken($memberInfo,$this->refreshExpires);
            //可以保存到数据库 也可以去掉下面两句,本身jwt不需要存储
            $this->redis = new PredisService();
            $this->redis->set(config('api.redisTokenKey').$this->appid. $this->tableName .  $accessTokenInfo['access_token'],serialize($accessTokenInfo),$this->expires);
            $this->redis->set(config('api.redisRefreshTokenKey') . $this->appid . $this->tableName . $accessTokenInfo['refresh_token'],serialize($accessTokenInfo),$this->refreshExpires);
        }else{
            $token =  Db::name('oauth2_access_token')->where('member_id',$memberInfo['member_id'])
                ->where('tablename',$this->tableName)
                ->where('group',$this->group)
                ->order('id desc')->limit(1)
                ->find();
            if($token && $token['expires_time'] > time() && !$refresh_token) {
                $accessTokenInfo['access_token'] = $token['access_token'];
                $accessTokenInfo['refresh_token'] = $token['refresh_token'];
                $accessTokenInfo['expires_time'] = $token['expires_time'];
                $accessTokenInfo['refresh_expires_time'] = $token['refresh_expires_time'];
            }else{
                $accessTokenInfo['access_token'] = $this->buildAccessToken($memberInfo,$this->expires);
                $accessTokenInfo['refresh_token'] = $this->getRefreshToken($memberInfo,$refresh_token);
            }
            $this->saveToken($accessTokenInfo);  //保存本次token
        }
        return $accessTokenInfo;
    }

    /**
     * token 过期 刷新token
     */
    public function refresh(Request $request)
    {
        $refresh_token = $request->input('refresh_token');
        if(config('api.driver')=='redis'){
            $this->redis = new PredisService();
            $refresh_token_info = $this->redis->get(config('api.redisRefreshTokenKey').$this->appid.$this->tableName.$refresh_token);
            $refresh_token_info = unserialize($refresh_token_info);
        }else{
            $refresh_token_info = Db::name('oauth2_access_token')
                ->where('refresh_token',$refresh_token)
                ->where('tablename',$this->tableName)
                ->where('group',$this->group)
                ->order('id desc')->find();
        }
        if (!$refresh_token_info) {
            return $this->error('refresh_token is error or expired', '', 401);
        }
        if ($refresh_token_info['refresh_expires_time'] <time()) {
            return $this->error('refresh_token is error or expired', '', 401);
        }
        //重新给用户生成调用token
        $member =  Db::name($this->tableName)->where('status',1)
            ->field('id as member_id')->find($refresh_token_info['member_id']);
        $client =  Db::name('oauth2_client')
            ->field('id as client_id,appid,group')->find($refresh_token_info['client_id']);
        $memberInfo = array_merge($member,$client);
        $accessToken = $this->setAccessToken($memberInfo,$refresh_token);
        return $this->success('success', $accessToken);
    }

    /**
     * 参数检测和验证签名
     */
    public function checkParams($params = [])
    {
        //时间戳校验
        if (abs($params['timestamp'] - time()) > $this->timeDif) {
            throw new \Exception('请求时间戳与服务器时间戳异常');
        }
        if ($this->authapp && $params['appid'] !== $this->appid) {
            //appid检测，查找数据库或者redis进行验证
            throw new \Exception('appid 错误');
        }
        if ($this->authapp && $params['appsecret'] !== $this->appsecret) {
            //appid检测，查找数据库或者redis进行验证
            throw new \Exception('appsecret 错误');
        }
        if($this->authapp){
            $oauth2_client = Db::name('oauth2_client')
                ->where('appid', $params['appid'])
                ->where('appsecret', $params['appsecret'])
                ->field('id')
                ->find();
            if (!$oauth2_client) {
                throw new \Exception('Invalid authorization app');
            }
        }
        return true;

    }

    /**
     * 生成AccessToken
     * @return string
     */
    protected function buildAccessToken($memberInfo,$expires)
    {
        $time = time(); //签发时间
        $expire = $time + $expires; //过期时间
        $scopes = 'role_access';
        if($expires==$this->refreshExpires)  $scopes = 'role_refresh';
        $token = array(
            "member_id" => $memberInfo['member_id'],
            'appid'=>$this->appid,
            'appsecret'=>$this->appsecret,
            "iss" => "https://www.funadmin.com",//签发组织
            "aud" => "https://www.funadmin.com", //签发作者
            "scopes" => $scopes, //刷新
            "iat" => $time,
            "nbf" => $time,
            "exp" => $expire,      //过期时间时间戳
        );
        return   JWT::encode($token,  $this->key, 'HS256');
    }

    /**
     * 获取刷新用的token检测是否还有效
     */
    protected function getRefreshToken($memberInfo,$refresh_token)
    {
        if(!$refresh_token){
            return $this->buildAccessToken($memberInfo,$this->refreshExpires);
        }
        $accessToken =Db::name('oauth2_access_token')->where('member_id',$memberInfo['member_id'])
            ->where('refresh_token',$refresh_token)
            ->where('tablename',$this->tableName)
            ->where('group',$this->group)
            ->field('refresh_token')
            ->find();
        return $accessToken?$refresh_token:$this->buildAccessToken($memberInfo,$this->refreshExpires);
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
