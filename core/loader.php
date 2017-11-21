<?php
namespace frame3\core;
/**
 * 自动加载class
 *
 * auto load class
 *
 */
class loader {

	/**
	 * 注册类自动加载方法
	 * @return [type] [description]
	 */
	public function init() {
		// step1. 优先查找app目录下
		// 切换到app对象__construct方法下注册
		// spl_autoload_register(array($this, 'app_class_loader'));
		// step2. 查找框架core下
		spl_autoload_register(array($this, 'core_class_loader'));
	}

	/**
	 * 自动include类，查找框架核心中的类
	 * @param  [type] $class_name [description]
	 * @return [type]             [description]
	 */
	private function core_class_loader($class_name) {
		$class_name = ltrim($class_name, '\\');
		$file_name = '';
		$name_space = '';
		if ($class_name_pos = strrpos($class_name, '\\')) {
			$name_space = substr($class_name, 0, $class_name_pos);
			$class_name = substr($class_name, $class_name_pos + 1);
			$file_name = str_replace(['frame3\core', '\\'], [CORE_PATH, DIRECTORY_SEPARATOR], $name_space) . DIRECTORY_SEPARATOR . $class_name . '.php';
		}
		if (is_file($file_name)) {
			include $file_name;
		}
	}

	/**
	 * 加载app中的自定义类
	 * @param  [type] $class_name [description]
	 * @return [type]             [description]
	 */
	private function app_class_loader($class_name) {
		// echo __METHOD__ . "\n";
	}

}
