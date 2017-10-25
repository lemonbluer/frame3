<?php
namespace frame3\core;
/**
 * 自动加载class
 *
 * auto load class
 *
 */
class Loader {
	public function __construct() {
		// step1. 优先查找app目录下
		spl_autoload_register(array($this, 'app_class_loader'));
		// step2. 查找框架core下
		spl_autoload_register(array($this, 'core_class_loader'));
	}

	/**
	 * 自动include类，查找框架核心中的类
	 * @param  [type] $class_name [description]
	 * @return [type]             [description]
	 */
	private function core_class_loader($class_name) {
		// vd(func_get_args(), $class_name);
		$className = ltrim($class_name, '\\');
		$fileName = '';
		$namespace = '';
		if ($lastNsPos = strrpos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			// $fileName = str_replace('frame3', CORE_PATH, $namespace);
			$fileName = str_replace(['frame3\core', '\\'], [CORE_PATH, DIRECTORY_SEPARATOR], $namespace) . DIRECTORY_SEPARATOR . $className . '.php';
		}
		// vd(['namespace' => $namespace, 'className' => $className, 'fileName' => $fileName]);
		require $fileName;
	}

	/**
	 * 加载app中的自定义类
	 * @param  [type] $class_name [description]
	 * @return [type]             [description]
	 */
	private function app_class_loader($class_name) {
		echo __METHOD__ . "\n";
	}

}
