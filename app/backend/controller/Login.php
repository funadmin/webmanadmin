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
namespace app\backend\controller;
use app\backend\service\AuthService;
use app\common\controller\Controller;
use Exception;
use fun\helper\SignHelper;
use support\Request;
class Login extends Controller {

    public function beforeAction(Request $request) {
        parent::beforeAction($request);
    }

    public function index(){
        if (!request()->isPost()) {
            $admin= session('admin');
            $admin_sign= session('admin.token') == SignHelper::authSign($admin) ? $admin['id'] : 0;
            // 签名验证是否存在
            if ($admin) {
                redirect(url('index/index'));
            }
            $bg = '/static/backend/images/admin-bg.jpg';
            $view = ['bg'=>$bg];
            return fetch('index',$view);
        } else {
            $post  = request()->post() ;
            $username = request()->post('username', '', ['strip_tags','trim','htmlspecialchars']);
            $password = request()->post('password', '',['strip_tags','trim','htmlspecialchars']);
            $rememberMe = request()->post('rememberMe');
            $rule = [
                "username|用户名" => 'require',
                "password|密码" => 'require',
            ];
            if(config('funadmin.captcha.check')){
                if(!$this->verifyCheck()){
                    return $this->error(lang('captcha error'));
                }
                $rule["captcha|验证码"] = 'require';
            }
            $this->validate($post, $rule);
            // 用户信息验证
            try {
                $auth = new AuthService();
                $auth->checkLogin($username, $password, $rememberMe);
            } catch (Exception $e) {
                return $this->error(lang('Login Failed')."：{$e->getMessage()}");
            }
            return $this->success(lang('Login Success').'...',url('index/index'));
        }
    }

}