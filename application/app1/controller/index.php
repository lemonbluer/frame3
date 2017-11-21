<?php
namespace frame3\app1;
/**
 *
 */
class index extends base {
	public function index() {
		$a = ['123'];
		foreach ($a as $k) {
			vd($k);
		}
		vd(T());
	}
	public function test() {
		$one = [['openid' => '1', 'name' => '刘先森'], ['openid' => '2', 'name' => '刘同学']];
		$result = m('user')->add_all($one);
		vd($result, m('user')->last_sql());
		// $r = m('user')->where(['id' => ['<=', 73], 'name' => '吴'], 'AND', 'OR')->limit(3)->del();
		// vd($r, m('user')->last_sql());
	}
}