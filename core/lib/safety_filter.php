<?php
namespace frame3\core\lib;
/**
 *
 */
class safety_filter {
	function __construct() {}

	/**
	 * 安全过滤器
	 * @param  [type] $input [description]
	 * @param  string $type  [description]
	 * @return [type]        [description]
	 */
	public function filter($input = null, $type = '', $length = 0) {
		$input = ($length == 0) ? $input : mb_substr($input, 0, $length);
		switch ($type) {
		case 'int':
			return intval($input);
			break;
		case 'sql':
			return mysql_real_escape_string($input);
			break;
		case 'email':
			$mathch = [];
			$regx = '/^[a-z_0-9.-]{1,64}@([a-z0-9-]{1,200}.){1,5}[a-z]{1,6}$/i';
			preg_match($regx, $input, $mathch);
			return isset($mathch[0]) ? $mathch[0] : FALSE;
			break;
		case 'phone':
			$match = [];
			$regx = '/^1\\d{10}$/i';
			preg_match($regx, $input, $mathch);
			return isset($mathch[0]) ? $mathch[0] : FALSE;
			break;
		default:
			break;
		}
		return false;
	}
}