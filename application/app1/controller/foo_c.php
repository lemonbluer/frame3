<?php
namespace frame3\application\app1\controller;

/**
 *
 */
class foo_c extends base {

	public function bar() {
		$id = I('id');
		user::say();
	}
}