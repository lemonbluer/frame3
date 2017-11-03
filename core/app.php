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
			$class_name = ltrim($class_name, '\\');
			$name_space = $file_name = '';
			if ($class_name_pos = strrpos($class_name, '\\')) {
				$name_space = substr($class_name, 0, $class_name_pos);
				$class_name = substr($class_name, $class_name_pos + 1);
				$file_name = str_replace(['frame3\application\\' . APP_NAME, '\\'], [APP_PATH, DIRECTORY_SEPARATOR], $name_space) . DIRECTORY_SEPARATOR . $class_name . '.php';
			}
			if (is_file($file_name)) {
				include $file_name;
			}
		});
		try {
			// step4.初始化对应controller
			$c = new \ReflectionClass('frame3\\application\\' . APP_NAME . '\\controller\\' . CONTROLLER_NAME);
			$c_instance = $c->newInstance();
			$f = $c->getMethod(FUNCTION_NAME);
			// step5.执行对应方法
			if ($f->isPublic()) {
				$f->invoke($c_instance);
			}
		} catch (\ReflectionException $e) {
			// 控制器不存在
			if ($e->getCode() == -1) {
				vd(T() . ' class \'' . CONTROLLER_NAME . '\' not found!');
			}
			// 控制器中没有找到对应方法
			if ($e->getCode() == 0) {
				vd(T() . ' controller \'' . CONTROLLER_NAME . '\' do not have method \'' . FUNCTION_NAME . '\'!');
			}
			vd(T() . ' 捕获异常：' . $e->getMessage(), $e);
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