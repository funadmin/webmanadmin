<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

return [
    '' => [
    // ... 这里省略其它中间件 全局
        app\middleware\AccessControl::class,
        app\middleware\Lang::class,
        app\middleware\Install::class,
        app\middleware\ViewNode::class,
    ],
    'backend' => [
        //角色权限
        app\backend\middleware\CheckRole::class,
//      //节点
        app\backend\middleware\SystemLog::class,
    ],];