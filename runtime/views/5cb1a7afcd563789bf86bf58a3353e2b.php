<?php /*a:3:{s:70:"F:\work\work\funadmin-webadmin\app/backend/view/sys\upgrade\index.html";i:1657015633;s:64:"F:\work\work\funadmin-webadmin\app/backend/view/layout\main.html";i:1657019448;s:68:"F:\work\work\funadmin-webadmin\app/backend/view/layout\logintpl.html";i:1657078823;}*/ ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo syscfg('site','sys_name'); ?>后台管理</title>
    <meta property="og:keywords" content="<?php echo syscfg('site','site_seo_keywords'); ?>" />
    <meta property="og:description" content="<?php echo syscfg('site','site_seo_desc'); ?>" />
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="referrer" content="never">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="/static/plugins/layui/css/layui.css" media="all" />
    <link rel="stylesheet" href="/static/backend/css/comm.css" media="all">
    <link rel="stylesheet" href="/static/backend/css/global.css" media="all" />
    <script src="/static/plugins/jquery/jquery-3.6.0.min.js"></script>
    <script src="/static/plugins/layui/layui.js" charset="utf-8"></script>
    <?php echo token_meta(); ?>
</head>
<script>
    window.Config = <?php echo json_encode($config); ?>;
    window.Config.formData = <?php echo isset($formData)?(json_encode($formData)):'""'; ?>,
    window.STATIC = Config.__STATIC__
    window.ADDONS = Config.__ADDONS__
    window.PLUGINS = Config.__PLUGINS__;
</script>
<body style="padding: 10px;background: #fff">

<div class="fun-container" id="app" style="">


<table class="layui-table" lay-skin="line">
    <thead>
    <tr>
        <th>系统信息</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>当前版本：FunAdmin v<?php echo htmlentities($now_version); ?>&nbsp;&nbsp;
            <button id="upgrade" data-auth="<?php echo htmlentities($auth); ?>" data-login="<?php echo __u('index'); ?>"
                    data-check="<?php echo __u('check'); ?>"
                    class="layui-btn layui-btn-sm layui-btn-warm" >点此检测版本</button>
        </td>
    </tr>
    <tr>
        <td>服务器域名：<?php echo htmlentities($url); ?></td>
    </tr>
    <tr>
        <td>服务器ip：<?php echo htmlentities($server_ip); ?></td>
    </tr>
    <tr>
        <td>站点目录：<?php echo htmlentities($document_root); ?></td>
    </tr>
    <tr>
        <td>当前协议：<?php echo htmlentities($document_protocol); ?></td>
    </tr>
    <tr>
        <td>端口：<?php echo htmlentities($server_port); ?></td>
    </tr>
    <tr>
        <td>PHP版本：<?php echo htmlentities($php_version); ?></td>
    </tr>
    <tr>
        <td>数据库版本：<?php echo htmlentities($mysql_version); ?></td>
    </tr>
    <tr>
        <td>Nginx：<?php echo htmlentities($server_soft); ?></td>
    </tr>
    <tr>
        <td>服务器时间：<?php echo date('Y-m-d H:i:s'); ?></td>
    </tr>
    </tbody>
</table>
<script type="text/html" id="upgrade_tpl">
    <table class="layui-table" lay-skin="line" style="text-align: left">
        <thead>
        <tr>
            <th>版本信息</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>可升级版本：FunAdmin <span class="layui-font-green">v{{ d.version }}</span>&nbsp;&nbsp;
            </td>
        </tr>
        <tr>
            <td>系统备份：<button data-url="<?php echo __u('backup'); ?>" class="layui-btn layui-btn-sm layui-btn-warm" id="backup">点击备份</button>
            </td>
        </tr>
        <tr>
            <td>系统升级：<button data-url="<?php echo __u('install'); ?>" class="layui-btn layui-btn-sm" id="install">点击升级</button>
            </td>
        </tr>
        <tr>
            <td>
                更新内容：
                <div style="padding: 20px;
    line-height: 30px;
    background: #dee8e5">
                    {{#if (d.content) {}}
                    {{#  layui.each(d.content, function(index, item){ }}
                    <li class="">
                        <span class="layui-font-blue">{{index+1}} : {{ item }}</span>
                    </li>
                    {{#  }); }}
                    {{#}else{ }}
                    若干问题
                    {{# } }}



                </div>
            </td>
        </tr>
        </tbody>
    </table>

</script>
<script type="text/html" id="login_tpl">
  <style>
    .layui-form-label {
      padding: 9px 0px;
      text-align:center;
    }
    .layui-form-item .required::after{
      position:unset;
    }
    .layui-card-header{
      text-align: left;margin-top: 10px;
    }
    .layui-elem-quote{
      padding: 5px;background: #409EFF ;
      border-left: 5px solid #409EFF;
      color: #fff;
    }
  </style>
  <div>
    <div class="layui-card">
      <div class="layui-card-header" style="">
        <blockquote class="layui-elem-quote" style="">温馨提示
          <br>
          此处账号为: <a class="layui-font-red" target="_blank" href="http://www.FunAdmin.com">FunAdmin云平台账号</a>
        </blockquote>
      </div>
      <br>
      <div class="layui-card-body">
        <form class="layui-form" action="">
          <div class="layui-form-item">
            <label class="layui-form-label required">账号<i class="fa fa-user"></i></label>
            <div class="layui-input-block">
              <input type="text" class="layui-input"  lay-verify="required" id="inputUsername" value=""
                     placeholder="<?php echo lang('username or email'); ?>">
            </div>
          </div>
          <div class="layui-form-item">
            <label class="layui-form-label required">密码<i class="fa fa-lock"></i></label>
            <div class="layui-input-block">
              <input type="password" class="layui-input"  lay-verify="required" id="inputPassword" value=""
                     placeholder="<?php echo lang('password'); ?>">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</script>

</div>
</body>
</html>
<!--[if lt IE 9]>
<script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
<script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<script defer src="/static/require.min.js?v=<?php echo syscfg('site','site_version'); ?>" data-main="/static/js/require-backend<?php echo syscfg('site','app_debug')?'':'.min'; ?>.js?v=<?php echo syscfg('site','app_debug')?time():syscfg('site','site_version'); ?>" charset="utf-8"></script>
