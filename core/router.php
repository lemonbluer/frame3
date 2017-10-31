<?php
namespace frame3\core;

/**
 * 路由
 */
class router {
	function __construct() {
	}

	/**
	 * 解析路由，定义常量
	 * @return [type] [description]
	 */
	public function parse() {
		$req_uri = explode('/', preg_replace('/\/+/', '/', $_SERVER['REQUEST_URI']));
		// 应用
		define('APP_NAME', $req_uri[1]);
		define('APP_URL', '/' . APP_NAME);
		define('APP_PATH', CORE_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . APP_NAME);
		// 控制器
		define('CONTROLLER_NAME', $req_uri[2]);
		define('CONTROLLER_URL', APP_URL . '/' . CONTROLLER_NAME);
		// 方法
		// 访问controller默认index方法可以省略不写
		define('FUNCTION_NAME', ((count($req_uri) > 3) && $req_uri[3] != '') ? $req_uri[3] : 'index');
		define('FUNCTION_URL', CONTROLLER_URL . '/' . FUNCTION_NAME);

	}
}