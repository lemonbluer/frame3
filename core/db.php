<?php
namespace frame3\core;
/**
 *
 */
class db {

    /**
     * 数据库连接配置信息
     * @var array
     *
     * 读写分离模式 rw_seperate==true
     * [
     *     'config文件中的数据库自定义名称，即database数组中的key'
     *         =>[
     *             'master'        =>     [],                // instances中第一个默认做主库
     *             'slave'         =>     [[],[]],        // instances都放入从库
     *             'slave_count'  =>     count(slave)    // 从库数量
     *         ]
     * ]
     *
     * 非读写分离模式(结构同上)
     * [
     *     'config文件中的数据库自定义名称，即database数组中的key'
     *         =>[
     *             'master'        =>     [],                // instances中最后is_master==true的那个做主库
     *             'slave'         =>     [[],[]],        // instances中is_master==false的项
     *             'slave_count'  =>     count(slave)    // 从库数量
     *         ]
     * ]
     */
    protected static $_cfg;
    protected static $_instance; // 各个库的连接实例

    private function __construct() {}
    private function __clone() {}

    /**
     * 获取数据库实例
     * @param  string $db_name [description]
     * @return [type]          [description]
     */
    public static function get_instance($db_cfg_name = null, $is_master = FALSE) {
        if (!(isset(self::$_cfg[$db_cfg_name]))) {self::load_cfg($db_cfg_name);}
        if ($is_master) {
            if (!isset(self::$_instance[$db_cfg_name]['master'])) {
                $config = self::$_cfg[$db_cfg_name]['master'];
                self::$_instance[$db_cfg_name]['master'] = self::connect($config);
            }
            return self::$_instance[$db_cfg_name]['master'];
        } else {
            // 随机拿一个从库,拿self::_cfg[rand(0,从库数目－1)]配置的库
            // TODO : 随机算法优化
            $slave_index = rand(0, self::$_cfg[$db_cfg_name]['slave_count'] - 1);
            if (!isset(self::$_instance[$db_cfg_name]['slave'][$slave_index])) {
                $config = self::$_cfg[$db_cfg_name]['slave'][$slave_index];
                self::$_instance[$db_cfg_name]['slave'][$slave_index] = self::connect($config);
            }
            return self::$_instance[$db_cfg_name]['slave'][$slave_index];
        }
    }

    /**
     * 建立到某数据库的连接
     * @param  string $config [description]
     * @return [type]         [description]
     */
    public static function connect($config = '') {
        $dsn = sprintf('%s:dbname=%s;host=%s;charset=utf8', $config['type'], $config['db_name'], $config['hostname']);
        // TODO : 什么鬼？？？？为什么int出来还是string？？？？
        $pdo = new \PDO($dsn, $config['user'], $config['password']);
        $pdo->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, FALSE);
        $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, FALSE);
        $pdo->setAttribute(\PDO::ATTR_TIMEOUT, 5);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    /**
     * 设置self::_cfg[$db_cfg_name]
     * @param  [type] $db_cfg_name config文件中database数组中的对应cfg信息
     *                             结构见顶部注释
     * @return [type]              [description]
     */
    public static function load_cfg($db_cfg_name) {
        $cfg = config('database')[$db_cfg_name];
        if (!$cfg['rw_seperate']) {
            self::$_cfg[$db_cfg_name] = [
                'master' => current($cfg['instances']),
                'slave' => $cfg['instances'],
            ];
        } else {
            foreach ($cfg['instances'] as $one) {
                if ($one['is_master']) {
                    self::$_cfg[$db_cfg_name]['master'] = $one;
                } else {
                    self::$_cfg[$db_cfg_name]['slave'][] = $one;
                }
            }
        }
        self::$_cfg[$db_cfg_name]['slave_count'] = count(self::$_cfg[$db_cfg_name]['slave']);
        // vd($db_cfg_name, self::$_cfg);
    }
}