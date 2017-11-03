<?php
namespace frame3\application\app1\controller;

/**
 *
 */
class foo_c extends base {

	public function bar() {
		$par = I('id');
		vd($par);
	}
}