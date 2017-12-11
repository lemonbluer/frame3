<?php
namespace frame3\app1;
/**
 *
 */
class base {
	const BASE_ERR_CODE = 0;
	const BASE_ERR_MSG = [
		self::BASE_ERR_CODE + 1 => '参数错误',
	];

	function __construct() {
		session_start();
	}

	/**
	 * 返回success
	 * @param  string $msg  成功信息
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public static function resp_suc(string $msg = '', array $data = []) {
		$resp = [
			'code' => 0,
			'msg' => ('' == $msg) ? 'Success' : $msg,
			'data' => $data,
		];
		echo json_encode($resp);
		return;
	}

	/**
	 * 返回错误
	 * @param  int    $errno  错误号
	 * @param  string $errmsg 错误信息
	 * @param  array  $data   错误信息
	 * @return [type]         [description]
	 */
	public function resp_err(int $errno = 0, string $errmsg = '', array $data = []) {
		if (empty($errmsg)) {
			if (isset(static::$_err_message[$errno])) {
				$errmsg = static::$_err_message[$errno];
			} elseif (isset(self::BASE_ERR_MSG[$errno])) {
				$errmsg = self::BASE_ERR_MSG[$errno];
			} else {
				$errmsg = 'Error';
			}
		}
		$resp = [
			'code' => $errno,
			'msg' => $errmsg,
			'data' => $data,
		];
		echo json_encode($resp);
		return;
	}

	/**
	 * 直接jsonencode返回
	 * @param  [type] $raw_data [description]
	 * @return [type]           [description]
	 */
	public function resp_raw($raw_data) {
		echo json_encode($raw_data);
		return;
	}
}