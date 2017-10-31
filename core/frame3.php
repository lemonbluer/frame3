<?php
namespace frame3\core;
/**
 *
 */
class frame3 {

	public static function start() {
		// step1.加载默认配置
		include CORE_PATH . '/frame_global_config.php';
		// step2.加载工具函数
		include CORE_PATH . '/helper.php';
		// step3.接管php异常处理，错误、exception等
		self::handler_register();
		// step4.类自动加载器
		include CORE_PATH . '/loader.php';
		(new loader())->init();
		//step5.启动应用
		(new app())->start();
	}

	/**
	 * 自定义接管
	 * @return [type] [description]
	 */
	public static function handler_register() {
		set_exception_handler('\frame3\core\frame3::exception_handler'); // 异常处理
		set_error_handler('\frame3\core\frame3::error_handler', E_ALL); // 错误处理
		register_shutdown_function('\frame3\core\frame3::shutdown');
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
		$e = error_get_last();
		// vd(T() . ' shutting down');
	}
}