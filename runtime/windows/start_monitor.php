<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Workerman\Worker;
use Webman\Config;

ini_set('display_errors', 'on');
error_reporting(E_ALL);

if (is_callable('opcache_reset')) {
    opcache_reset();
}
    
Config::load(config_path(), ['route', 'container']);

worker_start('monitor', config('process')['monitor']);

if (DIRECTORY_SEPARATOR != "/") {
    Worker::$logFile = config('server')['log_file'] ?? Worker::$logFile;
}

Worker::runAll();
