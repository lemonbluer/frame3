<?php
namespace frame3\core;
/**
 *
 */
class db {
	protected static $_instance;

	private function __construct() {}
	private function __clone() {}

	/**
	 * 获取数据库实例
	 * @param  string $db_cfg_name [description]
	 * @return [type]              [description]
	 */
	public static function get_instance($db_cfg_name = '') {
		if (empty(self::$_instance[$db_cfg_name])) {
			self::$_instance[$db_cfg_name] = self::connect(config('database')[$db_cfg_name]);
		}
		return self::$_instance[$db_cfg_name];
	}

	/**
	 * 建立到某数据库的连接
	 * @param  string $config [description]
	 * @return [type]         [description]
	 */
	public static function connect($config = '') {
		$dsn = sprintf('%s:dbname=%s;host=%s;charset=utf8', $config['type'], $config['db_name'], $config['hostname']);
		return new \PDO($dsn, $config['user'], $config['password']);
	}
}