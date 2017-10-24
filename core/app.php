<?php
namespace frame;
/**
 *
 */
class App {
	function __construct() {
		echo "App initing \n";
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
}