<?php
namespace frame3\core;

/**
 * 路由
 */
class router {

	/**
	 * 解析路由，定义常量
	 * @return [type] [description]
	 */
	public function parse() {

		$path = preg_replace('/\/+/', '/', $_SERVER['REQUEST_URI']);
		if ($par_pos = strpos($_SERVER['REQUEST_URI'], '?')) {
			$path = substr($_SERVER['REQUEST_URI'], 0, $par_pos);
		}

		/******************************************************/
		// 扩展后缀最多为5个字符，其余丢弃
		if ($extension_pos = strrpos($path, '.')) {
			$extension = substr($path, $extension_pos + 1, 5);
			$path = substr($path, 0, strrpos($path, '.'));
		}
		/******************************************************/

		$uri = explode('/', $path);

		/******************************************************/
		// 应用
		define('APP_NAME', $uri[1]);
		define('APP_URL', '/' . APP_NAME);
		define('APP_PATH', realpath(CORE_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . APP_NAME));
		define('APP_CONFIG_FILE', APP_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

		// 控制器
		define('CONTROLLER_NAME', $uri[2]);
		define('CONTROLLER_URL', APP_URL . '/' . CONTROLLER_NAME);
		// 方法
		// 访问controller默认index方法可以省略不写
		define('FUNCTION_NAME', ((count($uri) > 3) && $uri[3] != '') ? $uri[3] : 'index');
		define('FUNCTION_URL', CONTROLLER_URL . '/' . FUNCTION_NAME);
		// TODO : IS_AJAX
		// define('IS_AJAX', false);

		/******************************************************/

		/******************************************************/
		// 拆分路由中的隐式参数，合并到$_GET中
		if (count($uri) > 4) {
			$par = array_slice($uri, 4);
			while ($cur = current($par)) {
				if (!isset($_GET['' . $cur])) {
					$_GET['' . $cur] = urldecode(next($par));
				} else {
					next($par);
				}
				next($par);
			}
		}
		/******************************************************/
	}
}