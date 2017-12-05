<?php
namespace frame3\core;
/**
 *
 */
class model {

	// 数据库配置
	protected $_db_cfg_name; // 数据配置数组的key,app model中指定
	protected $_db_cfg; //数据库配置数组
	protected $_db_instance; // 当前库连接实例
	protected $_db_name; //数据库名
	protected $_table_name; // 表名

	protected $_last_sql; //上次执行的sql

	// sql拼接
	protected $_query_type;
	protected $_set;
	protected $_where; // where查询条件
	protected $_limit; // 设置结果集偏移量和数量
	protected $_col; //查询的字段
	protected $_bind;
	protected $_values; // insert查询值字段
	protected $_multi_execute;

	function __construct($model_name = '') {
		if ($model_name !== '') {
			$this->_table_name = config('database')[$this->_db_cfg_name]['table_prefix'] . $model_name;
		}
	}

	/**
	 * 获取数据库连接实用例
	 * @return [type] [description]
	 */
	protected function get_db_ins() {
		// update\delete\insert 均走主库
		$is_master = ($this->_query_type != 'SELECT');
		return db::get_instance($this->_db_cfg_name, $is_master);
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

	/**
	 * 设置查询条件
	 * @param  mixed  $where    一组where条件,字符串或者['列名'=>]
	 * @param  string $op       连接该组where条件的 AND或OR
	 * @param  string $inner_op 括号内运算符 连接组内各计算的AND或OR
	 * @return $this
	 */
	public function where($where, string $op = 'AND', string $inner_op = 'AND') {
		$this->_set_where($where, $op, $inner_op);
		return $this;
	}
	protected function _set_where($where, $op = 'AND', $inner_op = 'AND') {
		$this->_where[] = ['op' => $op, 'inner_op' => $inner_op, 'par' => $where];
	}

	/**
	 * 构建where查询条件
	 * @return [type] [description]
	 *  $op ( $where[0]->key $where[0]->val[0] $where[0]->val $inner_op )
	 */
	protected function _build_where() {
		if (!isset($this->_where)) {return null;}
		$where = 'WHERE (1=1) ';
		$i = 1;
		$bind = [];
		$is_select = 'SELECT' === $this->_query_type;
		foreach ($this->_where as $one) {
			if (is_array($one['par'])) {
				$par = []; // 每次where调用
				foreach ($one['par'] as $k => $v) {
					if (is_array($v)) {
						$op = $v[0];
						$bind[$i] = $v[1];
					} else {
						$op = '=';
						$bind[$i] = $v;
					}
					$value = '?';
					if (!$is_select) {
						$value = is_string($bind[$i]) ? "'{$bind[$i]}'" : $bind[$i];
					}
					$par[] = " `{$k}` {$op} {$value} ";
					$i++;
				}
				$where .= " {$one['op']} ( " . implode($one['inner_op'], $par) . " )";
				$this->_bind = $bind;
			} else {
				$where .= " {$op} ( {$one['par']} ) ";
			}
		}
		return $where;
	}

	// 查询符合查询条件的条数
	public function count() {
		$this->_query_type = 'SELECT';
		$this->_col = 'count(*) AS count ';
		$this->_limit = [0, 1];
		return intval($this->query()[0]['count']);
	}

	// 设置查询列
	public function col($row, $quoted = true) {
		if (!isset($this->_col)) {$this->_col = '';}
		if (is_array($row)) {
			if ($quoted) {
				$this->_col = '`' . implode('`,`', $row) . '`';
			} else {
				$this->_col = implode(',', $row);
			}
		} else {
			$this->_col = $row;
		}
		return $this;
	}

	// 增
	public function add($data = []) {
		if (empty($data) || !is_array($data)) {throw new \Exception("Data set should be array tobe inserted[" . $this->sql() . ']', 33303);return 0;}
		$i = 1;
		$col = array(); // 插入的列名
		foreach ($data as $k => $v) {
			$col[] = $k;
			$this->_bind[$i] = $v;
			$i++;
		}
		$this->_col = '`' . implode('`,`', $col) . '` ';
		$this->_values = '(' . substr(str_repeat('?,', count($col)), 0, -1) . ')';
		$this->_query_type = 'INSERT';
		$this->transaction_beg();
		$last_insert_id = $this->query();
		$this->commit();
		return $last_insert_id;
	}

	// 批量插库
	public function add_all($data = []) {
		$i = 1;
		array_walk_recursive($data, function ($v, $k) use (&$i) {
			$this->_bind[$i] = $v;
			$i++;
		});
		$this->_query_type = 'INSERT';
		$col = array_keys(current($data));
		$this->_col = '`' . implode('`,`', $col) . '` ';
		$data_val = '(' . substr(str_repeat('?,', count($col)), 0, -1) . ')';
		$this->_values = substr(str_repeat($data_val . ',', count($data)), 0, -1);
		$this->transaction_beg();
		$last_insert_id = $this->query();
		$this->commit();
		return $last_insert_id;
	}

	// 删
	public function del() {
		$this->_query_type = 'DELETE';
		return $this->exec();
	}

	// 改
	public function update($row_data = null) {
		if (null === $row_data) {
			throw new \Exception("No tablename or column specified in SQL[" . $this->sql() . ']', 33303);
			return false;
		}
		$this->_query_type = 'UPDATE';
		$this->_set = '';
		if (is_array($row_data) && count($row_data) > 0) {
			foreach ($row_data as $k => $v) {
				$fmt = is_string($v) ? '`%s`=\'%s\' ,' : '`%s`=%s ,';
				$this->_set .= sprintf($fmt, $k, $v);
			}
			$this->_set = substr($this->_set, 0, -1);
		} elseif (is_string($row_data)) {
			$this->_set .= $row_data . ' ';
		}
		return $this->exec();
	}

	// 查一条
	public function one() {
		if (!isset($this->_table_name)) {
			throw new \Exception("No tablename specified in SQL", 33303);
		}
		$this->_limit = [0, 1];
		$this->_query_type = 'SELECT';
		return $this->query();
	}

	// 查全部
	public function all() {
		if (!isset($this->_table_name)) {
			throw new \Exception("No tablename specified in SQL", 33303);
		}
		$this->_query_type = 'SELECT';
		return $this->query();
	}

	// 查看当前sql状态
	public function sql() {
		$this->_query_type = 'SELECT';
		return ['sql' => $this->_build_sql(), 'bind' => $this->_bind ?? 'empty'];
	}

	// 上一次执行的sql
	public function last_sql() {return $this->_last_sql;}

	// 组装sql
	protected function _build_sql() {
		$sql = '';
		switch ($this->_query_type) {
		case 'SELECT':
			// step.1 操作符
			$sql = 'SELECT ';
			// step.2 列
			$sql .= ($this->_col ?? '*') . ' ';
			// step.3 表名
			$sql .= "FROM `$this->_table_name` ";
			// step.4 JOIN
			// step.5 查询条件
			$sql .= $this->_build_where() ?? ' ';
			// step.6 group by
			// step.7 union
			// step.8 排序
			if (isset($this->_order_by)) {
				$sql .= "ORDER BY " . $this->_order_by . ' ';
			}
			// step.9 limit
			if (isset($this->_limit)) {
				$sql .= sprintf('LIMIT %d,%d ', intval($this->_limit[0]), intval($this->_limit[1]));
			}
			break;
		case 'INSERT':
			// step.1 操作符
			$sql = 'INSERT INTO ';
			// step.2 表名
			$sql .= "`$this->_table_name` ";
			// step.3 列
			$sql .= ($this->_col ? '(' . $this->_col . ') ' : '');
			// step.4 值
			$sql .= 'VALUES ' . $this->_values . ' ';
			break;
		case 'UPDATE':
			// step.1 操作符
			$sql = 'UPDATE ';
			// step.2 表名
			$sql .= '`' . $this->_table_name . '` ';
			// step.3 设置值
			$sql .= 'SET ' . $this->_set . ' ';
			// step.4 设置where条件
			$sql .= $this->_build_where() ?? ' ';
			// step.5 limit条件
			if (isset($this->_limit)) {
				$sql .= sprintf('LIMIT %d ', intval($this->_limit[1]));
			}
			break;
		case 'DELETE':
			// step.1 操作符
			$sql = 'DELETE ';
			// step.2 表名
			$sql .= 'FROM `' . $this->_table_name . '` ';
			// step.3 设置where条件
			$sql .= $this->_build_where() ?? ' ';
			// step.5 limit条件
			if (isset($this->_limit)) {
				$sql .= sprintf('LIMIT %d ', intval($this->_limit[1]));
			}
			break;
			break;
		default:
			# code...
			break;
		}
		return $sql;
	}

	// 执行查询
	public function query($sql = '') {
		if ($sql === '') {$sql = $this->_build_sql();}
		$this->_last_sql = ['sql' => $sql, 'bind' => isset($this->_bind) ? $this->_bind : null];
		$this->_db_instance = $this->get_db_ins();
		// vd($sql);
		$sth = $this->_db_instance->prepare($sql);
		if (isset($this->_bind)) {
			if (isset($this->_multi_execute) && $this->_multi_execute) {
				foreach ($this->_bind as $k => $v) {
					foreach ($v as $kk => $vv) {
						$sth->bindValue($kk, $vv);
					}
					$result[] = $sth->execute();
				}
			} else {
				foreach ($this->_bind as $k => $v) {
					$sth->bindValue($k, $v);
				}
				$result = $sth->execute();
			}
		} else {
			$result = $sth->execute();
		}

		/***************************************************************/
		//校验是否执行出错
		$err = $sth->errorInfo();
		if ($err[0] != '00000' || $err[0] === '01000') {
			throw new \Exception($err[2], $err[0]);
			return null;
		}
		/***************************************************************/

		if ($this->_query_type == 'INSERT') {
			return $result ? $this->_db_instance->lastInsertId() : 0;
		}
		return $result ? $sth->fetchAll(\PDO::FETCH_ASSOC) : null;

	}

	// 执行语句
	public function exec($sql = '') {
		if ($sql === '') {$sql = $this->_build_sql();}
		$this->_last_sql = ['sql' => $sql];
		$affected_row_count = $this->_db_instance->exec($sql);
		if (FALSE !== $affected_row_count) {
			$err = $this->_db_instance->errorInfo();
			if ($err[0] === '00000' || $err[0] === '01000') {
				return $affected_row_count;
			}
			throw new \Exception($err[2], $err[0]);
			return null;
		}
	}

	// 多次执行
	public function multi_execute($flag = TRUE) {
		$this->_multi_execute = $flag;
		return $this;
	}

	// 开始事务
	public function transaction_beg() {
		$this->_db_instance->beginTransaction();
		return $this;
	}
	// 提交事务
	public function commit() {
		$this->_db_instance->commit();
		return $this;
	}
	// 回滚事务
	public function rollBack() {
		$this->_db_instance->rollBack();
		return $this;
	}

	// 复用当前model，清理查询参数
	public function renew() {
		unset($this->_bind);
		if (isset($this->_multi_execute) && $this->_multi_execute) {return $this;}
		unset($this->_query_type);
		unset($this->_set);
		unset($this->_where); // where查询条件
		unset($this->_limit); // 设置结果集偏移量和数量
		unset($this->_col); //查询的字段
		unset($this->_values); // insert查询值字段
		unset($this->_multi_execute);
		return $this;
	}
}