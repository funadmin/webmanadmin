<?php /*a:4:{s:69:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/member\home.html";i:1657117468;s:69:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/layout\main.html";i:1657116355;s:71:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/layout\header.html";i:1656390765;s:71:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/layout\footer.html";i:1656390765;}*/ ?>
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




<div class="fly-home fly-panel" style="background-image: url('');">
  <img src="<?php echo htmlentities($ouser['avatar']); ?>" alt="<?php echo htmlentities($ouser['username']); ?>">
  <i class="iconfont icon-renzheng" title="funadmin-bbs社区认证"></i>
  <h1>
    <?php echo htmlentities($ouser['username']); ?>

    <i class="iconfont  <?php if($member['sex']==1): ?>icon-nan<?php elseif($member['sex']==2): ?>icon-nv<?php else: ?>保密<?php endif; ?>"></i>
    <!-- <i class="iconfont icon-nv"></i>  -->
    <i class="layui-badge fly-badge-vip">VIP<?php echo htmlentities($member['level_id']); ?></i>
    <!--
    <span style="color:#c00;">（管理员）</span>
    <span style="color:#5FB878;">（社区之光）</span>
    <span>（该号已被封）</span>
    -->
  </h1>
  <?php if($ouser['auth_info']): ?>
  <p style="padding: 10px 0; color: #5FB878;">认证信息：<?php echo htmlentities($ouser['auth_info']); ?> </p>
  <?php endif; ?>
  <p class="fly-home-info">
    <i class="iconfont icon-kiss" title="L币"></i><span style="color: #FF7200;"><?php echo htmlentities($ouser['scores']); ?> L币</span>
    <i class="iconfont icon-shijian"></i><span><?php echo timeAgo($ouser['create_time']); ?> 加入</span>
    <?php if($ouser['province']): ?><i class="iconfont icon-chengshi"></i><span>来自<?php echo _getProvicesByPid($ouser['province'])['name']; ?></span><?php endif; ?>
  </p>
  <?php if($ouser['sign']): ?>
  <p class="fly-home-sign">（<?php echo htmlentities($ouser['sign']); ?>）</p>
  <?php endif; ?>
<!--  <div class="fly-sns" data-user="">-->
<!--    <a href="javascript:;" class="layui-btn layui-btn-primary fly-imActive" data-type="addFriend">加为好友</a>-->
<!--    <a href="javascript:;" class="layui-btn layui-btn-normal fly-imActive" data-type="chat">发起会话</a>-->
<!--  </div>-->

</div>

<div class="layui-container">
  <div class="layui-row layui-col-space15">
    <div class="layui-col-md6 fly-home-jie">
      <div class="fly-panel">
        <h3 class="fly-panel-title"><?php echo htmlentities($ouser['username']); ?> 最近的提问</h3>

      </div>
    </div>
    
    <div class="layui-col-md6 fly-home-da">
      <div class="fly-panel">
        <h3 class="fly-panel-title"><?php echo htmlentities($ouser['username']); ?> 最近的文章</h3>

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