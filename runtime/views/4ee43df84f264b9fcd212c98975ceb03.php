<?php /*a:5:{s:69:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/login\index.html";i:1657106836;s:69:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/layout\main.html";i:1657116355;s:71:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/layout\header.html";i:1656390765;s:69:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/login\style.html";i:1657106765;s:71:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/layout\footer.html";i:1656390765;}*/ ?>
<!doctype html>
<html lang="en">
<head>
    <head>
        <meta charset="utf-8">
        <title>FunAdmin-首个支持php8.0的管理系统</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="keywords" content="FunAdmin">
        <meta name="description" content="FunAdmin">
        <meta name="csrf-token" content="<?php echo token(); ?>">
        <link rel="stylesheet" href="/static/index/css/comm.css">
        <link rel="stylesheet" href="/static/plugins/layui/css/layui.css">
        <script src="/static/plugins/layui/layui.js" charset="utf-8"></script>
    </head>
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>
        window.Config = <?php echo json_encode($config); ?>;
        window.Config.formData = <?php echo isset($formData)?(json_encode($formData)):'""'; ?>,
        window.STATIC ='__STATIC__'
        window.ADDONS = '__ADDONS__'
        window.PLUGINS = '__PLUGINS__';
    </script>
<body>

<div class="fly-header layui-bg-black">
  <div class="layui-container">
    <a class="fly-logo" href="/">
      <span style="color:#009688;
   ;font-size: 24px;">FunAdmin</span>
    </a>

    <ul class="layui-nav fly-nav-user">
      <?php if(!$member): ?>
      <!-- 未登入的状态 -->
      <li class="layui-nav-item">
        <a class="iconfont icon-touxiang layui-hide-xs" href="<?php echo __u('login/index'); ?>"></a>
      </li>
      <li class="layui-nav-item">
        <a href="<?php echo __u('login/index'); ?>">登入</a>
      </li>
      <li class="layui-nav-item">
        <a href="<?php echo __u('login/reg'); ?>">注册</a>
      </li>
<!--      <li class="layui-nav-item layui-hide-xs">-->
<!--        <a href="" onclick="layer.msg('正在通过QQ登入', {icon:16, shade: 0.1, time:0})" title="QQ登入" class="iconfont icon-qq"></a>-->
<!--      </li>-->
<!--      <li class="layui-nav-item layui-hide-xs">-->
<!--        <a href="" onclick="layer.msg('正在通过微博登入', {icon:16, shade: 0.1, time:0})" title="微博登入" class="iconfont icon-weibo"></a>-->
<!--      </li>-->

      <!-- 登入后的状态 -->
      <?php else: ?>
      <li class="layui-nav-item">
        <a class="fly-nav-avatar" href="javascript:;">
          <cite class="layui-hide-xs"><?php echo htmlentities($member['username']); ?></cite>
          <i class="iconfont icon-renzheng layui-hide-xs" title="<?php echo htmlentities($member['username']); ?>"></i>
          <i class="layui-badge fly-badge-vip layui-hide-xs">VIP<?php echo htmlentities($member['level_id']); ?></i>
          <img src="<?php echo htmlentities($member['avatar']); ?>">
        </a>
        <dl class="layui-nav-child">
          <dd><a href="<?php echo __u('member/set'); ?>"><i class="layui-icon">&#xe620;</i>基本设置</a></dd>
          <dd><a href="<?php echo __u('member/home'); ?>"><i class="layui-icon" style="margin-left: 2px; font-size: 22px;">&#xe68e;</i>我的主页</a></dd>
          <hr style="margin: 5px 0;">
          <dd><a href="javascript:;" data-url="<?php echo __u('member/logout'); ?>" id="logout" style="text-align: center;">退出</a></dd>
        </dl>
      </li>
    <?php endif; ?>
    </ul>
  </div>
</div>




<style>
    .layui-tab-title{
        text-align:center
    }
    .layui-form{
        max-width: 350px;text-align:center;margin: 0 auto;
    }
    .layui-form-label{
        text-align:left;
        padding:9px 0px;
        min-width: 100px;
    }
    .layui-form-item .layui-input-block{
        width:100%;
    }
    .layui-form-mid{
        position:relative;
        float:right;
        margin-right:0;
        top:-37px;
        right:0;
    }
    .layui-form-mid img{
        width:100px;
    }
    .layui-input-block{
        margin: 0 auto;
    }
</style>

<div class="layui-container fly-marginTop">
  <div class="fly-panel fly-panel-user"  pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
      <ul class="layui-tab-title" style="">
        <li class="layui-this">登入</li>
        <li><a href="<?php echo url('login/reg'); ?>">注册</a></li>
      </ul>
      <div class="layui-form layui-tab-content" id="LAY_ucm" style="padding: 20px 0;">
        <div class="layui-tab-item layui-show" style="">
          <div class="layui-form" style="padding: 20px 0;">
            <form method="post">
              <div class="layui-form-item">
                <div class="layui-input-block">
                  <label for="L_email" class="layui-form-label">邮箱或用户名</label>
                  <input type="text" id="L_email" name="username" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
              </div>
              <div class="layui-form-item">
                <div class="layui-input-block">
                  <label for="L_pass" class="layui-form-label">密码</label>
                  <input type="password" id="L_pass" name="password" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
              </div>
              <div class="layui-form-item">
                <div class="layui-input-block">
                  <label for="L_vercode" class="layui-form-label">验证码</label>
                  <div class="layui-form-item-inline">
                    <input type="text" id="L_vercode" name="vercode" required lay-verify="required" placeholder="请输入正确的答案" autocomplete="off" class="layui-input vercode">
                    <div class="layui-form-mid" style="padding: 0!important;">
                    <span style="color: #c00;">
                    <img style="height: 36px;border: 1px solid #ececec;" id="captchaPic" src="<?php echo url('index/verify',['t'=>time()]); ?>" alt="验证码" onclick="this.src='<?php echo url("index/verify"); ?>'+'?t='+Math.random()" />
                  </span>
                    </div>
                  </div>

                </div>

              </div>
              <div class="layui-form-item">

              </div>
              <?php echo token_field('__token__'); ?>
              <div class="layui-form-item">
                <button class="layui-btn" lay-filter="*" lay-submit>立即登录</button>
                <span style="padding-left:20px;">
                  <a href="<?php echo url('login/forget'); ?>">忘记密码？</a>
                </span>
              </div>
<!--              <div class="layui-form-item fly-form-app">-->
<!--                <span>或者使用社交账号登入</span>-->
<!--                <a href="" onclick="layer.msg('正在通过QQ登入', {icon:16, shade: 0.1, time:0})" class="iconfont icon-qq" title="QQ登入"></a>-->
<!--                <a href="" onclick="layer.msg('正在通过微博登入', {icon:16, shade: 0.1, time:0})" class="iconfont icon-weibo" title="微博登入"></a>-->
<!--              </div>-->
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .fly-footer{
    text-align: center;
    line-height: 30px;
    border-top: none;
    background: #393D49;
    color: #fff;
    padding: 0;
    margin: 0;
    width: 100%;
    position: fixed;
    bottom: 0;
  }
  .fly-footer a{
    color: #aaa;
  }
</style>

<div class="fly-footer">
  <p>
    <a href="javascript:;" target="_blank">Powered by FunAdmin</a>  <a href="javascript:;" target="_blank">2018-2028 &copy;</a></p>
  <p>
    <a href="javascript:;" >版权所有:FunAdmin版权所有</a>
    <a href="http://www.funadmin.com/" title="funadmin官网">funadmin官网</a>
    <a href="https://jq.qq.com/?_wv=1027&k=5wj2Wdy" title="获取源码">社区插件授权获取源码</a>
    <a href="http://www.Funadmin.com/" title="开源后台管理系统">开源后台管理系统</a>
    <a href="https://demo.funadmin.com/admin.php" title="php cms 后台管理系统">php后台管理系统</a>
    <a href="http://www.beian.miit.gov.cn">粤ICP备19106066号</a>
  </p>
</div>

<script src="/static/require<?php echo syscfg('site','app_debug')?'':'.min'; ?>.js?v=<?php echo syscfg('site','app_debug')?time():syscfg('site','site_version'); ?>" data-main="/static/js/require-frontend<?php echo syscfg('site','app_debug')?'':'.min'; ?>.js?v=<?php echo syscfg('site','app_debug')?time():syscfg('site','site_version'); ?>" charset="utf-8"></script>
</body>
</html>