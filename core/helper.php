<?php
/**
 * var_dump变量
 * @param  [type] $var [description]
 * @return [type]      [description]
 */
function vd() {
	$args = func_get_args();
	if (is_array($args)) {
		foreach ($args as $k => $v) {
			ob_start();
			var_dump($v);
			$log[] = ob_get_contents();
			ob_end_clean();
		}
	} else {
		ob_start();
		var_dump($v);
		$log[] = ob_get_contents();
		ob_end_clean();
	}
	// highlight_string("<?php\n" . implode("--------------------------------------------\n", $log));
	// $trace = debug_backtrace()[2];
	// echo $trace['file'] . '(' . $trace['line'] . ")\n";
	echo implode("--------------------------------------------\n", $log) . "\n";
	exit;
}

/**
 * 格式化时间
 * @param string $format [description]
 */
function T($time = 0, $format = 'Y-m-d H:i:s') {
	if ($time == 0) {
		$time = time();
	}
	return date($format, $time);
}

function L($data) {
	echo $data;
}

/**
 * 输入参数
 * @param [type] $name          [description]
 * @param [type] $default_value [description]
 */
function I($name, $default_value = '') {
	if ($name === '') {
		$par = [];
		return array_merge($par, $_GET, $_POST);
	}
	return isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $default_value);
}