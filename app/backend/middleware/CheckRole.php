<?php
/**
 * +
 * | 后台中间件验证权限
 */

namespace app\backend\middleware;

use app\backend\service\AuthService;
use Webman\Http\Response;

class CheckRole
{
    public function process(\Webman\Http\Request $request, callable $next): Response
    {

        $auth = new AuthService();
        $response = $auth->checkNode();
        if($response!==true && !is_null($response)){
            return $response;
        }
        return $next($request);
    }


}