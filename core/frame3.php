<?php
namespace frame3\core;
/**
 *
 */
class frame3 {

	public static function start() {
		// step1.加载默认配置
		include CORE_PATH . '/default_config.php';
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
		$exp_code = $e->getCode();
		if ($exp_code == 33301 && APP_ONLINE) {
			R('/', '当前页面被外星人拿走了!', 3);
			return;
		}
		// R('', T() . ' <br>捕获异常:' . $e->getMessage(), 0, '<hr>' . highlight_string("<?php\n" . var_export($e->getTrace(), true), true));
		$msg = '捕获异常:' . $e->getMessage() . "<br>File: " . $e->getFile() . '(' . $e->getLine() . ")";
		tuning(['msg' => $msg, 'trace' => $e->getTrace()]);
	}

	// php报错处理
	public static function error_handler(int $errno, string $errstr, string $errfile, int $errline, array $errcontext) {
		$e = error_get_last();
		// vd(T() . __METHOD__ . '捕获出错', ['errno' => $errno, 'errstr' => $errstr, 'errfile' => $errfile, 'errline' => $errline, 'errcontext' => $errcontext]);
		$msg = "Fatal Error ({$errno}): {$errstr}<br>File:{$errfile}({$errline}) ";
		tuning(['msg' => $msg, 'trace' => $errcontext]);
		die();
	}

	public static function shutdown() {
		// vd(T() . '捕获异常:' . $e->getMessage(), $e->getFile() . '(' . $e->getLine() . ')', $e);
		// vd(T() . ' shutting down');
	}
}