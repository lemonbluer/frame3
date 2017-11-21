<?php
namespace frame3\core\lib;
/**
 *
 */
class tool {

	public function bark($v = 'dog') {
		echo "\\frame3\lib $v bark bark bark!";
	}

	public static function bark1($v = 'dog') {
		echo "\\frame3\lib $v bark bark bark!";
	}
}