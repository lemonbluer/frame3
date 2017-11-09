<?php
namespace frame3\core;
/**
 *
 */
class model {

	// 数据库配置
	protected $_db_name; //数据库名
	protected $_db_instance; // 当前库连接实例
	protected $_table_name; // 表名

	protected $_last_sql; //上次执行的sql

	// sql拼接
	protected $_query_type;
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
			if ((intval($num[0]) <= 0) || (intval($num[1]) <= 0)) {
				throw new \Exception("limit parameter illegal", 1);
				return false;
			}
			$this->_limit = [intval($num[0]), intval($num[1])];
		} else if (intval($num) > 0) {
			$this->_limit = [0, intval($num)];
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
	protected function _build_where() {
		if (!isset($this->_where)) {return null;}
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
		return $where;
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
		$this->_query_type = 'SELECT';
		return $this->query($this->_build_sql());
	}
	// 查全部
	public function all() {
		$this->_query_type = 'SELECT';
		return $this->query($this->_build_sql());
	}
	// 查看当前sql状态
	public function sql() {
		$this->_query_type = 'SELECT';
		return ['sql' => $this->_build_sql(), 'bind' => $this->_bind ?? 'empty'];
	}
	// 上一次执行的sql
	public function last_sql() {return $this->last_sql;}
	protected function _build_sql() {
		$sql = '';
		switch ($this->_query_type) {
		case 'SELECT':
			// step.1 操作符
			$sql = 'SELECT ';
			// step.2 列
			$sql .= isset($this->_column) ? '`' . implode('`,`', $this->_column) . '` ' : '* ';
			// step.3 表名
			$sql .= "FROM `$this->_table_name` ";
			// step.4 查询条件
			$sql .= $this->_build_where() ?? ' ';
			// step.5 排序
			if (isset($this->_order_by)) {
				$sql .= "ORDER BY " . $this->_order_by . ' ';
			}
			// step.6 limit
			if (isset($this->_limit)) {
				$sql .= sprintf('LIMIT %d,%d ', intval($this->_limit[0]), intval($this->_limit[1]));
			}
			break;
		case 'INSERT':

			break;
		case 'UPDATE':
			break;
		case 'DELETE':
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
		$this->_last_sql = ['sql' => $sql, 'bind' => $this->_bind];
		return $sth->execute() ? $sth->fetchAll(\PDO::FETCH_ASSOC) : null;
	}
	// 执行语句
	public function exec() {
	}
	/**
	 * 复用当前model，清理查询参数
	 * @return [type] [description]
	 */
	public function renew() {
		unset($this->_query_type); // 查询类型
		unset($this->_where); // where查询条件
		unset($this->_limit); // 设置结果集偏移量和数量
		unset($this->_column); //查询的字段
		unset($this->_bind);
		return $this;
	}
}