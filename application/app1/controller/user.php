<?php
namespace frame3\app1;
/**
 *
 */
class user extends base {
	public function say() {
		$a = ['asdasd' => '123'];
		$result = m('user')->limit(['1', '-2'])->all();
		// ->where(['redeem_code' => '100001kpbtkn'])->all();
		vd($result, m('user')->sql());
	}
	public function say1() {
		$result = m('user')
			->where(['openid' => ['LIKE', I('openid') . '%']])->all();
		// ->where('openid LIKE \'' . I('openid') . '%\'')->all();
		vd(m('user')->sql());
	}
}