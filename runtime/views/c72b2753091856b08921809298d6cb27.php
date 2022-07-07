<?php /*a:5:{s:68:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/member\set.html";i:1657106651;s:69:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/layout\main.html";i:1657151094;s:71:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/layout\header.html";i:1656390765;s:75:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/layout\member-nav.html";i:1656390765;s:71:"D:\wwwroot\my-space\funadmin-webadmin\app/index/view/layout\footer.html";i:1656390765;}*/ ?>
<!doctype html>
<html lang="en">
<head>
    <head>
        <meta charset="utf-8">
        <title>FunAdmin-webman-基于webman和funadmin前台开发的高速管理系统</title>
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
    <div class="fly-panel fly-panel-user" pad20>
        <div class="layui-tab layui-tab-brief" lay-filter="user">
            <ul class="layui-tab-title" id="LAY_mine">
                <li class="layui-this" lay-id="info">我的资料</li>
                <li lay-id="avatar">头像</li>
                <li lay-id="pass">密码</li>
                <li lay-id="bind">帐号绑定</li>
            </ul>
            <div class="layui-tab-content" style="padding: 20px 0;">
                <div class="layui-form layui-form-pane layui-tab-item layui-show">
                    <form method="post">

                        <div class="layui-form-item">
                            <label for="L_username" class="layui-form-label">用户名</label>
                            <div class="layui-input-inline">
                                <input type="text" id="L_username" name="username" readonly required lay-verify="required"
                                       autocomplete="off" value="<?php echo htmlentities($member['username']); ?>" class="layui-input layui-disabled">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label for="L_username" class="layui-form-label">昵称</label>
                            <div class="layui-input-inline">
                                <input type="text" id="L_nickname" name="nickname"  required lay-verify="required"
                                       autocomplete="off" value="<?php echo htmlentities($member['nickname']); ?>" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label for="L_email" class="layui-form-label">邮箱</label>
                            <div class="layui-input-inline">
                                <input type="text" id="L_email" name="email" value="<?php echo htmlentities($member['email']); ?>" required lay-verify="email"
                                       autocomplete="off" value="" class="layui-input">
                            </div>
                            <!--              <div class="layui-form-mid layui-word-aux">如果您在邮箱已激活的情况下，变更了邮箱，需<a href="activate.html" style="font-size: 12px; color: #4f99cf;">重新验证邮箱</a>。</div>-->
                        </div>
                        <div class="layui-form-item">
                            <label for="L_sex" class="layui-form-label">性别</label>
                            <div class="layui-inline">
                                <div class="layui-input-inline-block">
                                    <input type="radio" name="sex" value="2" <?php if($member['sex']==2): ?> checked <?php endif; ?> title="保密">
                                    <input type="radio" name="sex" value="1"  <?php if($member['sex']==1): ?> checked <?php endif; ?> title="女">
                                    <input type="radio" name="sex" value="0"  <?php if($member['sex']==0): ?> checked <?php endif; ?> title="男" >
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label for="L_city" class="layui-form-label">城市</label>
                            <div class="layui-input-inline">
                                <select name="province" lay-filter="province"
                                        id="province">
                                    <option value="">请选择省</option>
                                    <?php if(is_array($province) || $province instanceof \think\Collection || $province instanceof \think\Paginator): $i = 0; $__LIST__ = $province;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                    <option value="<?php echo htmlentities($vo['id']); ?>" <?php if($member['province']==$vo['id']): ?> selected <?php endif; ?>><?php echo htmlentities($vo['name']); ?></option>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select name="city" lay-filter="city" lay-search="" id="city">
                                    <option value="">请选择城市</option>
                                    <?php if($member['city']): ?>
                                    <option value="<?php echo htmlentities($member['city']); ?>" selected><?php echo _getProvicesByPid($member['city'])['name']; ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select name="district" lay-filter="district" lay-search=""
                                        id="district" val>
                                    <option value="">请选择地区</option>
                                    <?php if($member['district']): ?>
                                    <option value="<?php echo htmlentities($member['district']); ?>"  selected ><?php echo _getProvicesByPid($member['district'])['name']; ?></option>
                                    <?php endif; ?>

                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item layui-form-text">
                            <label for="L_sign" class="layui-form-label">签名</label>
                            <div class="layui-input-block">
                                <textarea placeholder="随便写些什么刷下存在感" id="L_sign" name="sign" autocomplete="off"
                                          class="layui-textarea" style="height: 80px;" value="<?php echo htmlentities($member['sign']); ?>"><?php echo htmlentities($member['sign']); ?></textarea>
                            </div>
                        </div>
                        <?php echo token_field('__token__', 'sha1'); ?>
                        <div class="layui-form-item">
                            <button class="layui-btn"  data-request="<?php echo __u('member/set'); ?>" key="set-mine" lay-filter="set" lay-submit>确认修改</button>
                        </div>
                    </form>
                </div>

                <div class="layui-form layui-form-pane layui-tab-item">
                    <form class="layui-form" method="post">
                        <div class="layui-form-item">
                            <div class="avatar-add">
                                <p>建议尺寸168*168，支持jpg、png、gif，最大不能超过 <span class="layui-bg-red">500K</span> </p>
                                <button type="button" class="layui-btn upload-img">
                                    <i class="layui-icon">&#xe67c;</i>上传头像
                                    <input type="hidden" name="avatar" id="" value="" />
                                </button>
                                <img src="<?php echo htmlentities($member['avatar']); ?>">
                                <span class="loading"></span>
                            </div>
                        </div>
                        <?php echo token_field('__token__', 'sha1'); ?>
                        <div class="layui-form-item">
                            <button class="layui-btn"  data-request="<?php echo __u('member/set'); ?>" key="set-mine" lay-filter="avatar" lay-submit>确认修改</button>
                        </div>
                    </form>
                </div>

                <div class="layui-form layui-form-pane layui-tab-item">
                    <form action="{('member/repass')}" method="post">
                        <div class="layui-form-item">
                            <label for="L_nowpass" class="layui-form-label">当前密码</label>
                            <div class="layui-input-inline">
                                <input type="password" id="L_nowpass" name="oldpassword" required lay-verify="required"
                                       autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label for="L_pass" class="layui-form-label">新密码</label>
                            <div class="layui-input-inline">
                                <input type="password" id="L_pass" name="password" required lay-verify="required"
                                       autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-form-mid layui-word-aux">6到16个字符</div>
                        </div>
                        <div class="layui-form-item">
                            <label for="L_repass" class="layui-form-label">确认密码</label>
                            <div class="layui-input-inline">
                                <input type="password" id="L_repass" name="repassword" required lay-verify="required"
                                       autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <?php echo token_field('__token__', 'sha1'); ?>
                        <div class="layui-form-item">
                            <button class="layui-btn" key="set-mine"  data-request="<?php echo url('member/repass'); ?>" lay-filter="pass" lay-submit>确认修改</button>
                        </div>
                    </form>
                </div>

                <div class="layui-form layui-form-pane layui-tab-item">
                    <ul class="app-bind">
                        <!--                        <li class="fly-msg app-havebind">-->
                        <!--                            <i class="iconfont icon-qq"></i>-->
                        <!--                            <span>已成功绑定，您可以使用QQ帐号直接登录Fly社区，当然，您也可以</span>-->
                        <!--                            <a href="javascript:;" class="acc-unbind" type="qq_id">解除绑定</a>-->

                        <!--                            &lt;!&ndash; <a href="" onclick="layer.msg('正在绑定微博QQ', {icon:16, shade: 0.1, time:0})" class="acc-bind" type="qq_id">立即绑定</a>-->
                        <!--                            <span>，即可使用QQ帐号登录Fly社区</span> &ndash;&gt;-->
                        <!--                        </li>-->
                        <!--                        <li class="fly-msg">-->
                        <!--                            <i class="iconfont icon-weibo"></i>-->
                        <!--                            &lt;!&ndash; <span>已成功绑定，您可以使用微博直接登录Fly社区，当然，您也可以</span>-->
                        <!--                            <a href="javascript:;" class="acc-unbind" type="weibo_id">解除绑定</a> &ndash;&gt;-->
                        <!--                            <a href="" class="acc-weibo" type="weibo_id"-->
                        <!--                               onclick="layer.msg('正在绑定微博', {icon:16, shade: 0.1, time:0})">立即绑定</a>-->
                        <!--                            <span>，即可使用微博帐号登录Fly社区</span>-->
                        <!--                        </li>-->
                    </ul>
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