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
	protected $_where; // where查询条件
	protected $_limit; // 设置结果集偏移量和数量
	protected $_column; //查询的字段
	protected $_bind;

	function __construct($model_name = '') {
		if ($model_name === '') {
			// 直接使用m()->*****的方式调用，不指定表名
		} else {
			$this->_table_name = config('database')[$this->_db_name]['table_prefix'] . $model_name;
			$this->_db_instance = db::get_instance($this->_db_name);
		}
	}

	// 设置数据集偏移量和数量
	public function limit($num = 1) {
		if (is_array($num)) {
			$this->_limit = [$num[0], $num[1]];
		}
		if ($num > 0) {
			$this->_limit = [0, $num];
		} else {
			return false;
		}
		return $this;
	}
	// 排序
	public function order($order_by) {
		$this->_order_by = $order_by;
		return $this;
	}
	// 设置查询条件
	public function where($where, $op = 'AND') {
		$this->_set_where($where, $op);
		return $this;
	}
	protected function _set_where($where, $op = 'AND') {
		$this->_where[] = ['op' => $op, 'par' => $where];
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
	// 查看sql
	public function sql() {
		return ['sql' => $this->_build_sql('SELECT'), 'bind' => $this->_bind];
	}
	protected function _build_sql($query_type) {
		$sql = '';
		switch ($query_type) {
		case 'SELECT':
			// step.1 操作符
			$sql = 'SELECT ';
			// step.2 列
			$sql .= isset($this->_column) ? '`' . implode('`,`', $this->_column) . '` ' : '* ';
			// step.3 表名
			$sql .= "FROM `$this->_table_name` ";
			// step.4 查询条件
			if (isset($this->_where)) {
				$where = 'WHERE (1=1) ';
				$i = 1;
				$bind = [];
				foreach ($this->_where as $one) {
					if (is_array($one['par'])) {
						$par = []; // 每次where调用
						array_walk($one['par'], function ($v, $k) use (&$par, &$i, &$bind) {
							if (is_array($v)) {
								$op = $v[0] . ' ?';
								$bind[$i++] = $v[1];
							} else {
								$op = '=' . ' ?';
								$bind[$i++] = $v;
							}
							$par[] = " `{$k}` {$op} ";
						});
						$where .= "{$one['op']} ( " . implode('AND', $par) . " )";
						$this->_bind = $bind;
					} else {
						$where .= "AND ( {$one['par']} ) ";
					}
				}
				$sql .= $where;
			}
			// step.5 排序
			if (isset($this->_order_by)) {
				$sql .= "ORDER BY " . $this->_order_by . ' ';
			}
			// step.6 limit
			if (isset($this->_limit)) {
				$sql .= "LIMIT {$this->_limit[0]},{$this->_limit[1]} ";
			}
			break;
		default:
			# code...
			break;
		}
		return $sql;
	}
	// 查询数据
	public function query($sql) {
		$sth = $this->_db_instance->prepare($sql);
		if (isset($this->_bind)) {
			foreach ($this->_bind as $k => $v) {
				$sth->bindValue($k, $v);
			}
		}
		return $sth->execute() ? $sth->fetchAll(\PDO::FETCH_ASSOC) : null;
	}
	// 执行语句
	public function exec() {
	}
}