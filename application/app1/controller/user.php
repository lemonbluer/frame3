<?php
namespace frame3\app1;
/**
 *
 */
class user extends base {
	public function say() {
		$result = m('user')->where('1=1')->all();
		vd($result);
	}
}