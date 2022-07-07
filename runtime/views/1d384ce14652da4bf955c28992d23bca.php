<?php /*a:2:{s:74:"D:\wwwroot\my-space\funadmin-webadmin\app/backend/view/auth\admin\add.html";i:1657081161;s:71:"D:\wwwroot\my-space\funadmin-webadmin\app/backend/view/layout\main.html";i:1657019448;}*/ ?>
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



<?php if($formData): ?>
<style>
.layui-list-item{
    padding-bottom: 8px;
}
</style>
<div class="layui-fluid" style="background: #eee">
    <div class="layui-row layui-col-space15">
        <!-- 左 -->
        <div class="layui-col-sm12 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-body" style="padding: 25px;">
                    <div class="layui-text-center layui-text">
                        <div class="user-info-head" id="avatar" lay-upload="" lay-images="avatar" >
                            <img src="<?php echo !empty($formData['avatar']) ? htmlentities($formData['avatar']) : '/favicon.ico'; ?>" width="80" class="avatar">
                        </div>
                        <h2 style="padding-top: 20px;"><?php echo htmlentities($formData['realname']); ?></h2>
                    </div>
                    <div class="layui-text" style="padding-top: 30px;">
                        <div class="layui-list-item">
                            <p><i class="layui-icon layui-icon-username"></i>   <?php echo htmlentities($formData['username']); ?></p>
                        </div>
                        <div class="layui-list-item">
                            <p>
                                <i class="layui-icon layui-icon-release"></i>
                                <?php if(is_array($authGroup) || $authGroup instanceof \think\Collection || $authGroup instanceof \think\Paginator): $i = 0; $__LIST__ = $authGroup;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if(in_array($vo['id'],$formData['group_id'])): ?>
                                <?php echo htmlentities($vo['title']); ?>
                                <?php endif; ?>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </p>
                        </div>
                        <div class="layui-list-item">
                            <p><i class="layui-icon layui-icon-location"></i>   <?php echo htmlentities($formData['ip']); ?></p>
                        </div>
                        <div class="layui-list-item">
                            <p><i class="layui-icon layui-icon-email"></i>  <?php echo htmlentities($formData['email']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 右 -->
        <div class="layui-col-sm12 layui-col-md9">
            <div class="layui-card">
                <div class="layui-card-body">

                    <div class="layui-tab layui-tab-brief" lay-filter="userInfoTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this">基本信息</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <form class="layui-form" lay-filter="form">
                                    <?php echo form_input('username','text',['verify'=>'required','tips'=>'Username is between 4 and 25 characters'] ); ?>
                                    <?php echo form_input('realname','text',['verify'=>'required','tips'=>'realname'] ); ?>
                                    <?php echo form_input('password','password',['verify'=>'','tips'=>'If you do not fill in, you will not change the password']); ?>
                                    <?php echo form_select('group_id',$authGroup,['verify'=>'required','multiple'=>1,'search'=>0] ,'id,title',(isset($formData) and isset($formData['group_id']) ?$formData['group_id']:'')); ?>
                                    <?php echo form_upload('avatar',$formData,['mime'=>'image']); ?>
                                    <?php echo form_input('email','text',['verify'=>'email','tips'=>'For password retrieval, please fill in carefully.'] ); ?>
                                    <?php echo form_input('mobile','text',['verify'=>'mobile','tips'=>'mobile'] ); if((request()->input('type'))): ?>
                                    <?php echo form_submitbtn(true,['show'=>1]); ?>
                                    <!--    菜单个人信息编辑-->
                                    <?php else: ?>
                                    <?php echo form_submitbtn(); ?>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>

<form class="layui-form" lay-filter="form">
    <?php echo form_input('username','text',['verify'=>'required','tips'=>'Username is between 4 and 25 characters'] ); ?>
    <?php echo form_input('realname','text',['verify'=>'required','tips'=>'realname'] ); if(!$formData): ?>
    <?php echo form_input('password','password',['verify'=>'required','tips'=>'Password must be greater than 6 characters and less than 15 characters'] ); ?>
    <?php endif; ?>
    <?php echo form_select('group_id',$authGroup,['verify'=>'required','multiple'=>1,'search'=>0] ,'id,title',(isset($formData) and isset($formData['group_id']) ?$formData['group_id']:'')); ?>
    <?php echo form_upload('avatar',$formData,['mime'=>'image']); ?>
    <?php echo form_input('email','text',['verify'=>'email','tips'=>'For password retrieval, please fill in carefully.'] ); ?>
    <?php echo form_input('mobile','text',['verify'=>'mobile','tips'=>'mobile'] ); if((request()->input('type'))): ?>
    <?php echo form_submitbtn(true,['show'=>1]); ?>
    <!--    菜单个人信息编辑-->
    <?php else: ?>
    <?php echo form_submitbtn(); ?>
    <?php endif; ?>
</form>
<?php endif; ?>


</div>
</body>
</html>
<!--[if lt IE 9]>
<script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
<script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<script defer src="/static/require.min.js?v=<?php echo syscfg('site','site_version'); ?>" data-main="/static/js/require-backend<?php echo syscfg('site','app_debug')?'':'.min'; ?>.js?v=<?php echo syscfg('site','app_debug')?time():syscfg('site','site_version'); ?>" charset="utf-8"></script>
