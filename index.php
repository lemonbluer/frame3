<?php
echo date('Y-m-d H:i:s');

vd($_SERVER);


/**
 * 打印变量
 * @return [type] [description]
 */
function vd(){
	echo '<pre>';
	$vars = func_get_args();
	foreach ($vars as $var) {
		highlight_string("<?php\n" . var_export($var, true));
		echo '<hr />';
	}
	echo '</pre>';
}