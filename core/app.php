<?php
namespace frame3\core;
/**
 *
 */
class app {

	public $router;

	function __construct() {

	}

	/**
	 * 应用启动
	 * @return [type] [description]
	 */
	public function start() {
		// step1.解析路由
		$this->router = new router();
		$this->router->parse();
		// step2.加载app的个性化设置
		$this->load_app_config();
		// step3.注册app类加载器(app自有lib)
		spl_autoload_register(function ($class_name) {
			$o = $class_name;
			$class_name = ltrim($class_name, '\\');
			$file_name = '';
			$name_space = '';
			if ($class_name_pos = strrpos($class_name, '\\')) {
				$name_space = substr($class_name, 0, $class_name_pos);
				$class_name = substr($class_name, $class_name_pos + 1);
				$file_name = str_replace(['frame3\application\\' . APP_NAME, '\\'], [APP_PATH, DIRECTORY_SEPARATOR], $name_space) . DIRECTORY_SEPARATOR . $class_name . '.php';
			}
			if (is_file($file_name)) {
				include $file_name;
			}
		});
		// step3.初始化对应controller
		$c_name_space = ('frame3\\application\\' . APP_NAME . '\\controller\\' . CONTROLLER_NAME);
		$c = new $c_name_space;
		// step4.执行对应方法
		$f = new \ReflectionMethod($c, FUNCTION_NAME);
		if ($f->isPublic()) {
			$f->invoke($c);
		} else {
			throw new \ReflectionException();
		}
	}

	// 初始化数据库连接
	public function init_db() {
		echo "initing db connection \n";
	}

	// 调用controller
	public function call_controller() {
		echo "exec controller \n";
	}

	// 日志记录
	public function log() {
		echo "log someting \n";
	}

	public function load_app_config($value = '') {
		# code...
	}
}