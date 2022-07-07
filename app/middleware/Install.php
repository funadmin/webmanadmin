<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Install implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {

        $lockfile = public_path().'/install.lock';
        if(!file_exists($lockfile) && request()->app!='install'){
            return redirect('/install');
        }
        if(file_exists($lockfile) && request()->app＝='install' && !request()->action=='step4'){
            return redirect('/');
        }
        return $handler($request);
    }
}