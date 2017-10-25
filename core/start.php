<?php
namespace frame3;

header('X-Powered-By: frame3');
// TODO : window linux 适配
define('OS_TYPE', strtoupper(substr(PHP_OS, 0, 3))); //服务器类型
define('DEBUG_MODE', true); // debug模式
define('RUN_DIR', __DIR__); // 运行目录
// define('CORE_PATH', RUN_DIR . '/../core'); // 框架根目录

require CORE_PATH . '/frame3.php'; // 框架变量：每个请求生命周期内一直存在
core\Frame3::start();
