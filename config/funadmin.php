<?php

return [
    'dispatch_success_tmpl' => public_path().'/tpl/dispatch_jump.tpl',
    'dispatch_error_tmpl' => public_path().'/tpl/dispatch_jump.tpl',
    'captcha'=>[
        'check'=>true,
    ],
    'backend'=>[
        //不需要验证权限的控制器
        'noRightController'=>[ '/backend/ajax', '/backend/login', '/backend/index'],
        //不需要登录控制器
        'noLoginController'=>['/backend/login'],
        // 不需要鉴权
        'noRightNode'    =>['/backend/login/index', '/backbackend/login/logout','/backend/ajax/getLang','/backend/ajax/verify','/backend/login/verify','/backend/ajax/clearcache','/backend/ajax/setConfig'],
        // 不需要登陆
        'noLoginNode' => ['/backend/login/index', '/backend/login/logout', '/backend/ajax/getLang', '/backend/ajax/clearData','/backend/ajax/verify'],
        //超级管理员id
        'superAdminId'=>1,
        //是否演示站点
        'isDemo'=>0,
    ],
    'auth_on'=>true,
    'version'=>'1.0',
    'layui_version' => '2.7.2'
]
;