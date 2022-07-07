<?php /*a:2:{s:71:"F:\work\work\funadmin-webadmin\app/backend/view/sys\adminlog\index.html";i:1657015605;s:64:"F:\work\work\funadmin-webadmin\app/backend/view/layout\main.html";i:1657019448;}*/ ?>
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



<table class="layui-table" id="list" lay-filter="list"
       data-node-add="<?php echo auth(__u('add')); ?>"
       data-node-edit="<?php echo auth(__u('edit')); ?>"
       data-node-delete="<?php echo auth(__u('delete')); ?>"
       data-node-destroy="<?php echo auth(__u('destroy')); ?>"
       data-node-modify="<?php echo auth(__u('modify')); ?>"
       data-node-recycle="<?php echo auth(__u('recycle')); ?>"
       data-node-restore="<?php echo auth(__u('restore')); ?>"
       data-node-import="<?php echo auth(__u('import')); ?>"
       data-node-export="<?php echo auth(__u('export')); ?>"
>
</table>


</div>
</body>
</html>
<!--[if lt IE 9]>
<script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
<script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<script defer src="/static/require.min.js?v=<?php echo syscfg('site','site_version'); ?>" data-main="/static/js/require-backend<?php echo syscfg('site','app_debug')?'':'.min'; ?>.js?v=<?php echo syscfg('site','app_debug')?time():syscfg('site','site_version'); ?>" charset="utf-8"></script>
