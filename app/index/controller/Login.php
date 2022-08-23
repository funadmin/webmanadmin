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
use support\Request;
use support\View;
use app\index\validate\MemberValidate;
use think\exception\ValidateException;

class Login extends Base
{
    protected $callback;
    public function __construct()
    {
        parent::__construct();
        $this->modelClass = new \app\common\model\Member();
        $this->initialize();
    }
    public function initialize()
    {
        if (isLogin()) {
            $this->redirect(__u('/'));
        }
        $view = [
            'member'=>isLogin(),
        ];
        $this->callback =  request()->input('callback');
        View::assign($view);
    }
    /************************************注册登陆*******************************/

    /**
     * @return \support\Response
     * 登录
     */
    public function index()
    {
        if (isLogin()) {
            return redirect("/" . request()->app);
        }
        if ( request()->isPost()) {
            try {
                $this->modelClass->login();
            }catch(\Exception $e){
                return $this->error(lang($e->getMessage()));
            }
            $url = $this->callback?:__u('index/index');
            return $this->success(lang('Login Successful'),$url);
        }
        $view = [];
        return fetch('login/index', $view);
    }

    /**
     * @return \support\Response
     * 注册
     */
    public function reg()
    {
        if ( request()->isPost()) {
            try {
                $this->modelClass->reg();
            }catch(\Exception $e){
                return $this->error(lang($e->getMessage()));
            }
            return $this->success(lang('reg successful'), __u('login/index'));
        }
        if ( request()->input('type') == 1) {
            response()->cookie('code', null);
            response()->cookie('email', null);
            response()->cookie('username', null);
        }
        return fetch('reg');

    }

    //注册激活
    public function regActive()
    {
        if ( request()->isPost()) {
            $data =  request()->all();
            //校验场景中重置密码的方法
            try {
                $this->validate("app\\index\\validate\\MemberValidate.RegActive");
            } catch (ValidateException $e) {
                return $this->error($e->getError());
            }
            if (!request()->cookie('code')) {
                return $this->error('验证码错误！', __u('login/reg'));

            }
            if ($data['vercode'] != request()->cookie('code')) {
                return $this->error('验证码错误！');
            }
            $data = session('regData');
            $data['email_validated'] = 1;
            $member = $this->modelClass->save($data);
            if ($member) {
                response()->cookie('code', null);
                response()->cookie('email', null);
                response()->cookie('username', null);
                return  $this->success('激活成功', __u('login/index'));
            } else {
                return $this->error('激活失败');
            }
        }

    }

    /*
     * 忘记密码
     */


    public function forget()
    {
        if ( request()->isPost()) {
            $data =  request()->all();
            if (!captcha_check($data['vercode']))return $this->error('验证码错误');
            $member = $this->modelClass->where('email', $data['email'])->find();
            if (!$member)return $this->error('邮箱不存在');
            $code = mt_rand('100000', '999999');
            $time = 10 * 60;
            $content = '亲爱的FunAdmin用户:' . $member['name'] . '<br>您正在重置密码，您的验证码为:' . $code . '，请在' . $time / 60 . '分钟内进行验证';
            $param = ['to'=>$member->email,'subject'=>'FunAdmin重置密码邮件','content'=>$content];
            $mail = hook('sendEmail',$param);
            $mail = json_decode($mail,true);
            if($mail['code']>0){
                response()->cookie('forget_code', $code, $time);
                response()->cookie('forget_uid', $member->id, $time);
                response()->cookie('forget_email', $member->email, $time);
                return $this->success('发送成功', __u('login/forget'));
            } else {
                return $this->error('发送失败');
            }
        }
        if ( request()->input('type') == 1) {
            response()->cookie('forget_code', null);
            response()->cookie('forget_uid', null);
            response()->cookie('forget_email', null);
        }
        return fetch('forget');
    }

    //重置密码
    public function repass()
    {
        if ( request()->isPost()) {

            $data =  request()->post();
            //校验场景中重置密码的方法
            try {
                $validate = new MemberValidate();
                $validate->scene('Repass')->check($data);
            } catch (ValidateException $e) {
                return $this->error($e->getError());
            }
            if (!request()->cookie('forget_code')) {
                return $this->error('验证码错误！', __u('login/forget'));
            }
            if ($data['vercode'] != request()->cookie('forget_code')) {
                return $this->error('验证码错误！');
            }
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            $member = $this->modelClass->find($data['id']);
            $member->password = $data['password'];
            $res = $member->save();
            response()->cookie('forget_code', null);
            response()->cookie('forget_uid', null);
            response()->cookie('forget_email', null);
            if ($res) {
                return $this->success('修改成功', __u('login/index'));
            } else {
                return $this->error('修改失败');
            }
        }
    }

}
