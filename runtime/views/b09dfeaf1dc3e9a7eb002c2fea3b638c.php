<?php /*a:5:{s:70:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/member\index.html";i:1657106652;s:69:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/layout\main.html";i:1657116355;s:71:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/layout\header.html";i:1656390765;s:75:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/layout\member-nav.html";i:1656390765;s:71:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/layout\footer.html";i:1656390765;}*/ ?>
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



<div class="layui-container fly-marginTop fly-user-main">

    <ul class="layui-nav layui-nav-tree layui-inline" lay-filter="user">
  <li class="layui-nav-item">
    <a href="<?php echo __u('member/home'); ?>">
      <i class="layui-icon">&#xe609;</i>
      我的主页
    </a>
  </li>
  <li class="layui-nav-item  <?php if($action=='index'): ?>layui-this<?php endif; ?>">
    <a href="<?php echo __u('member/index'); ?>">
      <i class="layui-icon">&#xe612;</i>
      用户中心
    </a>
  </li>
  <li class="layui-nav-item <?php if($action=='set'): ?>layui-this<?php endif; ?>">
    <a href="<?php echo __u('member/set'); ?>">
      <i class="layui-icon">&#xe620;</i>
      基本设置
    </a>
  </li>
<!--  <li class="layui-nav-item <?php if($action=='bbs'): ?>layui-this<?php endif; ?>">-->
<!--    <a href="<?php echo __u('member/bbs'); ?>">-->
<!--      <i class="layui-icon">&#xe620;</i>-->
<!--      我的帖子-->
<!--    </a>-->
<!--  </li>-->
<!--  <li class="layui-nav-item <?php if($action=='message'): ?>layui-this<?php endif; ?>">-->
<!--    <a href="<?php echo __u('member/message'); ?>">-->
<!--      <i class="layui-icon">&#xe611;</i>-->
<!--      我的消息-->
<!--    </a>-->
<!--  </li>-->
</ul>

<div class="site-tree-mobile layui-hide">
  <i class="layui-icon">&#xe602;</i>
</div>
<div class="site-mobile-shade"></div>

<div class="site-tree-mobile layui-hide">
  <i class="layui-icon">&#xe602;</i>
</div>
<div class="site-mobile-shade"></div>

    <div class="fly-panel fly-panel-user" pad20="" style="padding-top:20px;">
        <?php if(!$member['email_validated']): ?>
        <div class="fly-msg" style="margin-bottom: 20px;"> 您的邮箱尚未验证，这比较影响您的帐号安全，<a href="<?php echo __u('user/activate'); ?>">立即去激活？</a>
        </div>
        <?php endif; ?>
        <div class="layui-row layui-col-space20">
            <div class="layui-col-md6">
                <div class="fly-panel fly-panel-border">
                    <div class="fly-panel-title"> 我的会员信息</div>
                    <div class="fly-panel-main layui-text" style="padding: 18px 15px; height: 50px; line-height: 26px;">
                        <p> 您的财富经验值：<span style="padding-right: 20px; color: #FF5722;">￥<?php echo htmlentities($member['scores']); ?></span> 您当前为：<i
                                class="layui-badge fly-badge-vip">VIP<?php echo htmlentities($member['level_id']); ?></i></p>
<!--                        <p> 您拥有社区L币：<span style="color: #FF7200; padding-right: 5px;"><?php echo htmlentities($member['scores']); ?>L币</span>-->
                            <!--              <a href="/order/bill?itemid=16" target="_blank"-->
                            <!--                 class="layui-btn layui-btn-warm layui-btn-xs">充值</a>-->
<!--                        </p>-->
                    </div>
                </div>
            </div>
            <div class="layui-col-md6">
                <div class="fly-panel fly-signin fly-panel-border">
                    <div class="fly-panel-title"> 签到 <i class="fly-mid"></i> <a href="javascript:;" class="fly-link"
                                                                                id="LAY_signinHelp">说明</a> <i
                            class="fly-mid"></i> <a href="javascript:;" class="fly-link" id="LAY_signinTop">活跃榜<span
                            class="layui-badge-dot"></span></a> <span
                            class="fly-signin-days">已连续签到<cite>0</cite>天</span></div>
                    <div class="fly-panel-main fly-signin-main">
                        <button class="layui-btn layui-btn-disabled">今日已签到</button>
                        <span>获得了<cite>0</cite>L币</span></div>
                </div>
            </div>
            <div class="layui-col-md12" style="margin-top: -20px;">
                <div class="fly-panel fly-panel-border">
                    <div class="fly-panel-title"> 快捷方式</div>
                    <div class="fly-panel-main">
                        <ul class="layui-row layui-col-space10 fly-shortcut">
                            <li class="layui-col-sm3 layui-col-xs4"><a href="<?php echo __u('member/set'); ?>"><i
                                    class="layui-icon"></i><cite>修改信息</cite></a></li>
                            <li class="layui-col-sm3 layui-col-xs4"><a href="<?php echo __u('member/set'); ?>#avatar"><i
                                    class="layui-icon"></i><cite>修改头像</cite></a></li>
                            <li class="layui-col-sm3 layui-col-xs4"><a href="<?php echo __u('member/set'); ?>#pass"><i
                                    class="layui-icon"></i><cite>修改密码</cite></a></li>
                            <!--              <li class="layui-col-sm3 layui-col-xs4"><a href="<?php echo __u('member/set'); ?>#bind"><i-->
                            <!--                      class="layui-icon"></i><cite>帐号绑定</cite></a></li>-->
                            <li class="layui-col-sm3 layui-col-xs4"><a href="<?php echo __u('bbs/add'); ?>"><i
                                    class="layui-icon"></i><cite>发表新帖</cite></a></li>
                            <!--              <li class="layui-col-sm3 layui-col-xs4"><a href="/column/share/"><i-->
                            <!--                      class="layui-icon"></i><cite>查看分享</cite></a>-->
                            <!--              </li>-->
                            <!--              <li class="layui-col-sm3 layui-col-xs4 LAY_search"><a href="javascript:;"><i-->
                            <!--                      class="layui-icon"></i><cite>搜索资源</cite></a></li>-->
                            <li class="layui-col-sm3 layui-col-xs4"><a href="<?php echo __u('member/bbs'); ?>#collection"><i
                                    class="layui-icon"></i><cite>我的收藏</cite></a></li>
                            <!--              <li class="layui-col-sm3 layui-col-xs4"><a href="/jie/15697/"><i-->
                            <!--                      class="layui-icon"></i><cite>成为赞助商</cite></a></li>-->
                            <!--              <li class="layui-col-sm3 layui-col-xs4"><a href="/jie/2461/"><i-->
                            <!--                      class="layui-icon"></i><cite>关注公众号</cite></a></li>-->
                            <li class="layui-col-sm3 layui-col-xs4"><a
                                    href="https://gitee.com/limingyue0312/funadmin/tree/master/docs"><i
                                    class="layui-icon"></i><cite>文档</cite></a></li>
                            <li class="layui-col-sm3 layui-col-xs4"><a
                                    href="https://demo.funadmin.com/index.php/admin"><i
                                    class="layui-icon"></i><cite>示例</cite></a></li>
                        </ul>
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