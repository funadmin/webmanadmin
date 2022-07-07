<?php
/**
 * +
 * | 后台中间件验证权限
 */
namespace app\backend\middleware;


use app\common\service\AdminLogService;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
class SystemLog implements MiddlewareInterface
{

    public function process(Request $request, callable $next): Response
    {
        //进行操作日志的记录
        $AdminLogService = new AdminLogService();
        $AdminLogService->save();
        //中间件handle方法的返回值必须是一个Response对象。
        return $next($request);
    }
}