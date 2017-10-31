<?php
namespace frame3\application\app1\controller;

/**
 *
 */
class foo_c extends base {

	function __construct() {
		echo "foo_c Controller";
	}

	public function bar() {
		echo 'Hello , this is app1/foo_c/bar()';
	}
}