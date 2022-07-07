<?php /*a:2:{s:74:"D:\wwwroot\my-space\funadmin-webadmin\app/backend/view/sys\config\set.html";i:1657078814;s:71:"D:\wwwroot\my-space\funadmin-webadmin\app/backend/view/layout\main.html";i:1657019448;}*/ ?>
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


<blockquote class="layui-elem-quote layui-text">
    <legend><?php echo lang('config'); ?><?php echo lang('list'); ?></legend>
</blockquote>
<div class="layui-tab">
    <ul class="layui-tab-title">
        <li class="layui-this"><?php echo lang('site setting'); ?></li>
        <li class=""><?php echo lang('upload setting'); ?></li>
        <li class=""><?php echo lang('water setting'); ?></li>
    </ul>
    <div class="layui-tab-content">
        <!--网站配置-->
        <div class="layui-tab-item layui-show">
            <form class="layui-form" action="<?php echo __u('site'); ?>" lay-filter="form">
                <?php echo form_input('site_name','text',['label'=>"Site name",'verify'=>"required"]); ?>
                <?php echo form_input('site_domain','text',['label'=>"Site Domain",'verify'=>"required|url"]); ?>
                <?php echo form_input('site_email','text',['label'=>"Site Email"]); ?>
                <?php echo form_upload('site_logo',$formData,['type'=>'checkbox']); ?>
                <?php echo form_textarea('site_seo_title',['label'=>'Site title','verify'=>"required"]); ?>
                <?php echo form_textarea('site_seo_keywords',['label'=>'Meta keywords','verify'=>"required"]); ?>
                <?php echo form_textarea('site_seo_desc',['label'=>'Meta description','verify'=>"required"]); ?>
                <?php echo form_textarea('site_copyright',['label'=>'All right','verify'=>"required"],'2021 www.FunAdmin.com Apache2 license'); ?>
                <?php echo form_submitbtn(true,['show'=>1]); ?>
            </form>
        </div>
        <!--上传配置-->
        <div class="layui-tab-item">
            <form class="layui-form " lay-filter="form">
                <?php echo form_input('upload_file_type','text',['label'=>"File Type"],'bmp|png|gif|jpg|jpeg|zip|rar|txt|ppt|xls|doc|mp3|mp4'); ?>
                <?php echo form_input('upload_file_max','text',['label'=>"Maxfilesize",'verify'=>'number','tips'=>"M"],'50'); ?>
                <?php echo form_radio('upload_slice',[1=>'是',0=>'否'],['label'=>'是否分片']); ?>
                <?php echo form_input('upload_slicesize','text', ['label'=>"分片大小",'verify'=>'number']); ?>
                <div class="layui-form-item">
                    <label class="layui-form-label"><?php echo lang('Upload Driver'); ?></label>
                    <div class="layui-input-block">
                        <select name="upload_driver" lay-verify="required" lay-search="">
                            <option value="">--<?php echo lang('Select'); ?>--</option>
                            <option value="local"><?php echo lang('local'); ?></option>
                            <option value="alioss"><?php echo lang('AliOSS'); ?></option>
                            <option value="qiniuoss"><?php echo lang('QiniuOSS'); ?></option>
                            <option value="tencos"><?php echo lang('Tencos'); ?></option>
                            <option value="bos"><?php echo lang('baidu'); ?></option>
                            <option value="obs"><?php echo lang('huawei'); ?></option>
                        </select>
                        <div class="layui-form-mid layui-word-aux">M <?php echo lang('Driver'); ?>：1 M = 1024 KB</div>
                    </div>
                </div>
                <?php echo form_submitbtn(true,['show'=>1]); ?>
            </form>
        </div>
        <div class="layui-tab-item">
            <form class="layui-form " lay-filter="form">
                <?php echo form_radio('upload_water',[0=>'No',1=>'Image',2=>'text'],['label'=>"water"]); ?>
                <?php echo form_radio('upload_water_position',
                [1=>'左上角',2=>'左上角',3=>'右上角',4=>"左居中",5=>"居中",6=>"右居中",7=>"左下角",8=>"下居中",9=>"右下角"],
                ['label'=>"waterposition"]); ?>
                <?php echo form_input('upload_water_alpha','text',['label'=>"wateralpha",'verify'=>'number','tips'=>"0-100"],'50'); ?>
                <?php echo form_input('upload_water_size','text',['label'=>"waterSize",'verify'=>'number','tips'=>""],'50'); ?>
                <?php echo form_color('upload_water_color',['label'=>"watercolor"]); ?>
                <?php echo form_upload('upload_water_thumb',$formData,['label'=>'image/text']); ?>
                <?php echo form_submitbtn(true,['show'=>1]); ?>
            </form>
        </div>

    </div>
</div>




</div>
</body>
</html>
<!--[if lt IE 9]>
<script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
<script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<script defer src="/static/require.min.js?v=<?php echo syscfg('site','site_version'); ?>" data-main="/static/js/require-backend<?php echo syscfg('site','app_debug')?'':'.min'; ?>.js?v=<?php echo syscfg('site','app_debug')?time():syscfg('site','site_version'); ?>" charset="utf-8"></script>
