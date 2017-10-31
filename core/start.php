<?php
namespace frame3\core;

header('X-Powered-By: frame3');
// TODO : window linux 适配
define('OS_TYPE', strtoupper(substr(PHP_OS, 0, 3))); //服务器类型
define('DEBUG_MODE', true); // debug模式
define('CORE_PATH', __DIR__);

require CORE_PATH . '/frame3.php'; // 框架变量：每个请求生命周期内一直存在
frame3::start();
