<?php

header('X-Powered-By: frame3');
// TODO : window linux 适配
define('OS_TYPE', strtoupper(substr(PHP_OS, 0, 3))); //服务器类型
define('DEBUG_MODE', true); // debug模式
define('RUN_DIR', __DIR__); // 运行目录
define('FRAME_BASE_DIR', RUN_DIR . '/../core'); // 框架根目录

// 加载工具函数文件
include FRAME_BASE_DIR . '/lib/helper.php';
include FRAME_BASE_DIR . '/frame.php';

$frame = new Frame();

// 解析路由
$frame->url_pharse();
// 解析传递过来的参数
$frame->input_filter();
// 初始化数据库连接
$frame->init_db();
// 调用controller
$frame->call_controller();
// 日志记录
$frame->log();