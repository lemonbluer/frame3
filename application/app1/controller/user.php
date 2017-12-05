<?php
namespace frame3\app1;
/**
 *	用户类
 */
class user extends base {
	const ERR_CODE = 3330;
	static $_err_message = [
		self::ERR_CODE + 1 => '用户不存在',
	];

	// uri : /app1/user  or  /app1/user/index
	// 参数示例 /app1/user?id=1  or /app1/user/index/id/1.html
	public function index() {
		$user = m('user')->where(['id' => intval(I('id'))])->one();
		if (!empty($user)) {
			$this->resp_suc('用户存在！', $user);
		} else {
			$this->resp_err(self::ERR_CODE + 1);
		}
	}
}