<?php

namespace app\middleware;

use think\helper\Str;
use support\View;
use Webman\Http\Request;
use Webman\Http\Response;

class ViewNode
{
    public function process(\Webman\Http\Request $request, callable $next): Response
    {

        if($request->app!='install'){
            [$modulename, $controllername, $actionname,$route,$url] = getNodeInfo();
            $jsname = '';
            empty($jsname) ? $jsname = strtolower(str_replace('\\','/',$controllername)):'';
            $controllername = strtolower(Str::camel(parse_name($controllername)));
            $actionname = strtolower(Str::camel(parse_name($actionname)));
            $autojs = file_exists(public_path().DS."static".DS."{$modulename}".DS."js".DS."{$jsname}.js") ? true : false;
            $jspath =$jsname?"{$modulename}/js/{$jsname}.js":'';
            $config = [
                '__STATIC__'=>'/static',
                '__ADDONS__'=>'/static/addons',
                '__PLUGINS__'=>'/static/plugins',
                'appname'    => request()->app,
                'addonname'    => '',
                'modulename'    => $modulename,
                'moduleurl'    => $url,
                'controllername'       =>$controllername,
                'actionname'           => $actionname,
                'requesturl'          => $url,
                'jspath' => "{$jspath}",
                'autojs'           => $autojs,
                'superAdmin'           => session('admin.id')==1 || session('admin.group') && in_array(1,explode(',',session('admin.group')))?true:false,
                'lang'           =>  locale(session('lang', 'zh-cn')),
                'site'           =>   syscfg('site'),
                'upload'           =>  syscfg('upload'),
            ];
            View::assign('config',$config);
        }
        return $next($request);
    }
}