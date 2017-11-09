<?php
namespace frame3\app1;
/**
 *
 */
class user extends base {
	public function say() {
		$result = m('user')
			->where(['redeem_code' => '100001kpbtkn'])->all();
		vd($result, m('user')->sql());
	}
	public function say1() {
		$result = m('user')
			->where(['openid' => ['LIKE', I('openid') . '%']])->all();
		// ->where('openid LIKE \'' . I('openid') . '%\'')->all();
		vd($result, m('user')->sql());
	}
}