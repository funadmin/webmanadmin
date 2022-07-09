<?php
namespace Webman\ActionHook;

use support\Container;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use Webman\Route;

class ActionHook implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        if ($request->controller) {
            // 禁止直接访问beforeAction afterAction
            if ($request->action === 'beforeAction' || $request->action === 'afterAction') {
                $callback = Route::getFallback() ?? function () {
                    return new Response(404, [], \file_get_contents(public_path() . '/404.html'));
                };
                $reponse = $callback($request);
                return $reponse instanceof Response ? $reponse : \response($reponse);
            }
            $controller = Container::get($request->controller);
            if (method_exists($controller, 'beforeAction')) {
                $before_response = call_user_func([$controller, 'beforeAction'], $request);
                if ($before_response instanceof Response) {
                    return $before_response;
                }
            }
            $response = $next($request);
            if (method_exists($controller, 'afterAction')) {
                $after_response = call_user_func([$controller, 'afterAction'], $request, $response);
                if ($after_response instanceof Response) {
                    return $after_response;
                }
            }
            return $response;
        }
        return $next($request);
    }
}
