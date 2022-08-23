<?php
/**
 * funadmin
 * ============================================================================
 * 版权所有 2018-2027 funadmin，并保留所有权利。
 * 网站地址: http://www.Funadmin.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * Author: yuege
 * Date: 2019/11/27
 */
namespace app\index\controller;

use app\common\controller\Controller;
use fun\helper\StringHelper;
use support\Request;
use think\exception\ValidateException;
use think\facade\Cache;
use think\facade\Db;
use support\View;
class Member extends Base {

    public function __construct()
    {
        parent::__construct();
        $this->modelClass = new \app\common\model\Member();
        View::assign(['member'=>session('member'),'action'=>$this->action]);
    }

    /**
     * @return \support\Response
     */
    public function index(){

        if(!session('member')) return $this->redirect(__u('login/index'));
        return fetch('index');

    }
    /**
     * @return \support\Response
     * 其他个人主页
     */
    public function home(){
        $id =  request()->input('id');
        $name =  request()->input('name');
        if($id){
            $ouser = $this->modelClass->find($id);
        }elseif($name){
            $ouser = $this->modelClass->where('username',$name)->find();
            if(!$ouser) return $this->error('error/err');
            $id = $ouser->id;
        }else{
            $id = session('member.id');
            $ouser = session('member');
        }
        if(!$ouser){
            return $this->redirect(__u('login/index'));
        }
        View::assign('ouser',$ouser);
        return fetch('home');

    }
    /**
     * @return \support\Response
     * 设置
     */
    public function set(){
        $member = $this->isLogin();
        if(!$member) return $this->redirect(__u('login/index'));
        if( request()->isPost()){
            $data =  request()->post();
            if(isset($data['avatar'])){
                $save = $member->save($data);
            }elseif($member->email==$data['email']){
                $save = $member->save($data);
            }else{
                $rule = [
                    'email'=>'require|unique:member'
                ];
                try {
                    $this->validate($data,$rule);
                }catch (ValidateException $e){
                    return $this->error(lang($e->getMessage()));
                }
                $save = $member->save($data);
            }
            if(!$save) return $this->error(lang('modified failed'));
            request()->session()->set('member',$member);
            return $this->success(lang('modified Successfully'));

        }
        $list = Db::name('provinces')->where('pid',0)->cache(true)->select();
        View::assign('province',$list);
        return fetch('set');
    }
    //修改密码
    public function repass(){
        if(!$this->isLogin()) $this->redirect(__u('login/index'));
        if(request()->isPost()){
            $member = $this->isLogin();
            $data = request()->all();
            $validate = new \app\frontend\validate\MemberValidate();
            $res = $validate->scene('setPass')->check($data);
            if(!$res){
                return $this->error($validate->getError());
            }
            $old =   strip_tags($data['oldpassword']);
            $pass = strip_tags($data['password']);
            $repass = strip_tags($data['repassword']);
            if(!password_verify($old,$member['password'])) return  $this->error(lang('Old password error'));
            if($pass!=$repass)return $this->error(lang('Repeat password error'));
            $member->password =   password_hash($data['password'],PASSWORD_BCRYPT);
            if(!$member->save())return $this->error(lang('edit failed'));
            return $this->success(lang('edit successful'));
        }
    }
    //获取地区
    public function getProvinces($pid=0){
        $pid = request()->input('pid')?request()->input('pid'):$pid;
        $list = Db::name('provinces')->where('pid',$pid)->cache(true)->select();
        return $this->success(lang('ok'),'',$list);
    }

    /**
     * 激活邮箱
     */
    public function activate(){
        $member = $this->isLogin();
        if (!$member) $this->redirect(__u('login/index'));
        if(request()->isPost()){
            try {
                $this->modelClass->sendEmail($member);
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
            return $this->success(lang("sendEmail successful"));
        }
        return fetch('activate');
    }
    //链接邮箱激活
    public function emailactive(){
        $token = request()->input('token');
        if($token){
            $check_token = cookie('activeToken');
            if($check_token){
                if($check_token['time']>time()-3600*2 && $check_token==$token){
                    $member = $this->modelClass->find($check_token['member_id']);
                    $member->email_validated=1;
                    if($member->save()) {
                        $info = ['code'=>1,'msg'=>'邮箱激活成功，去登录FUN社区,带来的快乐吧'];
                    }else{
                        $info = ['code'=>0,'msg'=>'激活失败，请重新发送链接激活'];
                    }
                }else{
                    $info = ['code'=>0,'msg'=>'链接过期或链接无效，请重新发送链接'];
                }

            }else{
                $info = ['code'=>0,'msg'=>'激活链接过期，请重新发送链接'];
            }
        }else{
            $info = ['code'=>0,'msg'=>'激活链接不正确'];
        }
        View::assign(['info'=>$info]);
        return fetch('emailactive');

    }
    //发送邮件
    public function sendEmail()
    {
        $member = $this->isLogin();
        $data = [$member->username, $member->email, $member->password];
        $token = request()->cookie('activeToken');
        $validity = 2 * 3600;//有限期
        if(!$token || ($token && $token['time']<time()-$validity)) {
            $token = StringHelper::getToken($data);//验证码
            $tokenData = ['time' => $validity, 'token' => $token,'member_id'=>$member->id];
        }
        $link = __u('member/emailactive',['token' => $token]);
        $content = $this->_geteamilContent($validity/3600, $link);
        $param = ['to'=>$member->email,'subject'=>'FunAdmin 社区激活邮件','content'=>$content];
        $mail = hook('sendEmail',$param);
        $mail = json_decode($mail,true);
        if($mail['code']>0){
            response()->cookie('activeToken', json_encode($tokenData));
        }else{
            throw new \Exception($mail['msg']);
        }

        return json($mail);
    }
    //邮箱内容
    protected function _geteamilContent($validity,$link){
        $str = "<div>请点击以下的链接验证您的邮箱，验证成功后就可以使用"
            .syscfg('site','site_name').
            "提供的服务了。</div>
            <tr> <td colspan='2' style='font-size:12px; line-height: 20px; padding-top: 14px;padding-bottom: 25px; color: #909090;'><div>该链接的有效期为"
            .$validity .
            "小时,如链接超过有效期请重新发送邮件  <a href='"
            .$link.
            "' style='color: #03c5ff; text-decoration:underline;' rel='noopener' target='_blank'>点击链接去激活邮箱
            </a></div><div style=\"padding-top:4px;\">(如果不能打开页面，请复制该地址到浏览器打开)</div></td></tr>";

        return $str;

    }
    /**
     * @return \support\Response
     * 退出
     */
    public function logout(){
        request()->session()->flush();;
        Cache::clear();
        response()->cookie('mid',null);
        return $this->success('退成成功！',__u('login/index'));

    }

}