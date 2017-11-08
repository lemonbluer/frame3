<?php
namespace frame3\core;
/**
 *
 */
class db {
	protected static $_instance;

	public static function get_instance($db_cfg_name = '') {
		if (empty(self::$_instance[$db_cfg_name])) {
			self::$_instance[$db_cfg_name] = self::connect(config('database')[$db_cfg_name]);
		}
		return self::$_instance[$db_cfg_name];
	}

	public static function connect($config = '') {
		$dsn = sprintf('%s:dbname=%s;host=%s;charset=utf8', $config['type'], $config['db_name'], $config['hostname']);
		try {
			return new \PDO($dsn, $config['user'], $config['password']);
		} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}
	}
}