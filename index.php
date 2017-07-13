<?php
echo date('Y-m-d H:i:s');

vd($_SERVER,[0,1,'2']);



function vd(){
	echo '<pre>';
	$vars = func_get_args();
	foreach ($vars as $var) {
		highlight_string("<?php\n" . var_export($var, true));
		echo '<hr />';
	}
	echo '</pre>';
}