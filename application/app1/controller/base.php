<?php
namespace frame3\app1;
/**
 *
 */
class base {
	const BASE_ERR_CODE = 3330;
	const ERR_MSG = [
		self::BASE_ERR_CODE + 1 => '参数错误',
	];

	function __construct() {

	}

	public static function resp_suc(string $msg = '', $data = null) {
		$resp = [
			'code' => 0,
			'msg' => ('' == $msg) ? 'Success' : $msg,
			'data' => $data,
		];
		echo json_encode($resp);
		return;
	}

	public function resp_err(int $errno = 0, string $msg = '') {


	}

	public function resp_raw($raw_data) {

	}
}