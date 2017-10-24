<?php
namespace frame;
/**
 *
 */
class Frame {
	private static $app;

	function __construct() {
		echo 'Frame initiating!';

	}
	public static function start() {

		/* ------------------------ */
		/* 接管php异常处理			*/
		set_exception_handler('\frame\Frame::exception_handler'); // 异常处理
		set_error_handler('\frame\Frame::error_handler'); // 错误处理
		register_shutdown_function('\frame\Frame::shutdown');
		/* ------------------------ */

		include CORE_PATH . '/helper.php'; // 加载工具函数文件
		include CORE_PATH . '/loader.php'; // 加载框架全局默认配置
		$a = new loader();
		// 解析路由
		self::url_pharse();
		// 解析传递过来的参数
		self::input_filter();
		include CORE_PATH . '/frame_global_config.php'; // 加载框架全局默认配置
		/* --------------  应用部分 beg --------------  */
		self::$app = new app();
		// 初始化数据库连接
		self::$app->init_db();
		// 调用controller
		self::$app->call_controller();
/* --------------  应用部分 end --------------  */
		// 日志记录
		self::log();
	}
	// 解析路由
	public static function url_pharse() {
		echo "pharseing url \n";
	}

	// 解析传递过来的参数
	public static function input_filter() {
		echo "parseing input \n";
	}

	public static function exception_handler($e) {
		vd(T() . '捕获异常', $e);
	}

	// php报错处理
	public static function error_handler(int $errno, string $errstr, string $errfile, int $errline, array $errcontext) {
		$e = error_get_last();
		vd(T() . '出错', ['errno' => $errno, 'errstr' => $errstr, 'errfile' => $errfile, 'errline' => $errline, 'errcontext' => $errcontext]);
		die();
	}

	public static function shutdown() {

	}
}