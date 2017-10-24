<?php
namespace frame;
class Loader {
	public function __construct() {
		spl_autoload_register(array($this, 'loader'));
	}

	/**
	 * 自动include类
	 * @param  [type] $class_name [description]
	 * @return [type]             [description]
	 */
	private function loader($class_name) {
		$class_name = str_replace('\\', '/', $class_name);
		$file = CORE_PATH . DIRECTORY_SEPARATOR . $class_name . '.php';
		if (is_file($file) && !class_exists($class_name)) {
			include $file;
		} else {
			$file = CORE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $class_name . '.php';
			if (is_file($file) && !class_exists($class_name)) {
				include $file;
			} else {
				vd('class ' . $class_name . ' not found in ' . $file);
			}
		}

	}

}
