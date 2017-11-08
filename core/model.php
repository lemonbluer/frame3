<?php
namespace frame3\core;
/**
 *
 */
class model {

	// 数据库配置
	protected $_db_name;
	protected $_db_instance;
	protected $_table_name;

	// sql拼接
	protected $_where;
	protected $_limit;

	function __construct($model_name = '') {
		if ($model_name === '') {
			// 直接使用m()->*****的方式调用，不指定表名
		} else {
			$this->_table_name = config('database')[$this->_db_name]['table_prefix'] . $model_name;
			$this->_db_instance = db::get_instance($this->_db_name);
		}
	}

	// 设置数据集偏移量和数量
	public function limit() {
		echo 'limit';
	}
	// 排序
	public function order() {
	}
	// 设置查询条件
	public function where($where) {
		$this->_set_where($where);
		return $this;
	}
	protected function _set_where($where) {
		$this->_where = $where;
	}
	// 查询符合查询条件的条数
	public function count() {
	}

	// 增
	public function add() {
	}
	// 删
	public function del() {
	}
	// 改
	public function update() {
	}
	// 查一条
	public function one() {
		$this->_limit = [0, 1];
		$sql = $this->_build_sql('SELECT');
		return $this->query($sql);
	}
	// 查全部
	public function all() {
		$sql = $this->_build_sql('SELECT');
		return $this->query($sql);
	}
	protected function _build_sql($query_type) {
		return $query_type . ' * FROM `' . $this->_table_name . '` WHERE ' . $this->_where;
	}
	// 查询数据
	public function query($sql) {
		$sth = $this->_db_instance->query($sql);
		return $sth ? $sth->fetchAll(\PDO::FETCH_ASSOC) : null;
	}
	// 执行语句
	public function exec() {
	}
}