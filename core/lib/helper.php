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
	highlight_string("<?php\n" . implode("--------------------------------------------\n", $log));
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