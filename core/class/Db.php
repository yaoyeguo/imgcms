<?php
/**
 * 数据库抽象层
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-9-2 15:25
 * @version 1.0
 */
abstract class Db {	
	
	private $_cond = array();

	
	protected $_host;
	
	
	protected $_port;

	
	protected $_user;

	
	protected $_pass;
	
	
	protected $_dbname;
	
	
	protected $_newLink = false;
	
	
	protected $_charset = 'utf8';
	
	
	protected $_queryError = true;
	
	
	private $_inTransaction = false;

	
	private $_transactionNum = 0;
	
	
	
	public function queryError($queryError = true) {
		$this->_queryError = $queryError;
		return $this;	
	}
	
	
	abstract public function connect();
	
	
	abstract public function useDb($db_name);
	
	
	abstract public function getCurrentDb();
	
	
	abstract public function getRows($sql, $asKey = null);

	
	abstract public function query($sql);	
	
	
	abstract public function fetch($query);
    
    
    abstract public function insertId();
    
    
    abstract public function affectRows();
    
    
    abstract public function lastSql();
    
    
    abstract public function close();
    
	
	public function getAll($asKey = null) {
		$res = $this->getRows($this->_dealSelectSql(), $asKey);
		return $res;
	}
	
	
	public function getOne() {
		$res = $this->getRows($this->_dealSelectSql());
		return isset($res[0]) ? $res[0] : false;	
	}
  
    
    public function update($data) {
    	if (is_array($data)) {
	    	$set = array();
	    	foreach ($data as $key => $value) {
	    		$set[] = "`$key` = '$value'";
	    	}	
	    	$set   = implode(", ", $set);
    	} else {
    		$set = $data;	
    	}
    	$where = $this->_dealWhere();
    	$table = $this->_dealTable();
    	if ('' == $where) {
    		$this->_showError(__METHOD__ . ' expects at least [where] parameters');
    		return false;
    	}
    	$sql   = "UPDATE $table SET $set $where";
    	return $this->query($sql);
    }
    
   
    public function delete() {
    	$where = $this->_dealWhere();
    	$table = $this->_dealTable();
    	if ('' == $where) {
    		$this->_showError(__METHOD__ . ' expects at least [where] parameters');
    		return false;
    	}
    	$sql   = "DELETE FROM $table $where";
    	return $this->query($sql);
    }
   
    
    public function insert($data) {
    	return $this->_insertFun($data, 'INSERT');	
    }
    
    
    public function replace($data) {
    	return $this->_insertFun($data, 'REPLACE');	
    }
    
    
    private function _insertFun($data, $fun = 'INSERT') {
    	$first = reset($data);
    	$cols  = array();
    	if (is_array($first)) {
    		$cols = array_keys($first);
    		$vals = array();
    		foreach ($data as $value) {
    			$vals[] = "('".implode("', '", $value)."')";	
    		}
    		$vals = implode(", ", $vals);
    	} else {
    		$cols = array_keys($data);
    		$vals = "('".implode("', '", $data)."')";	
    	}
    	$cols = "(`".implode("`, `", $cols)."`)";
    	$table  = $this->_dealTable();
    	$sql  = "$fun INTO $table $cols VALUES $vals";
    	return $this->query($sql);
    } 
    
    
	public function begin() {
		if (!$this->_inTransaction) {
			$this->query('BEGIN');	
			$this->_inTransaction = true;
			$this->_transactionNum = 1;
		} else {
			$this->_transactionNum++;
		}		
	}	
	
	
	public function rollBack() {
		if ($this->_inTransaction) {
			$this->query('ROLLBACK');
    		$this->_inTransaction = false;
    		$this->_transactionNum = 0;	
		}
	}
	
	
	public function commit() {
		if (1 == $this->_transactionNum && $this->_inTransaction) {
	    	$this->query('COMMIT');
	    	$this->_inTransaction = false;
	    	$this->_transactionNum = 0;
		}
		else {
    		$this->_transactionNum--;	
    	}
	}
	
	
	public function getTransactionNum() {
		return $this->_transactionNum;	
	}
    
    
	
	public function where($where) {
		$this->_cond['where'] = $where;
		return $this;
	}
	

	
	
	public function table($table) {
		if (Wee::$config['db_table_prefix'] && false !== strpos($table, '#@_')) {
			$table = str_replace('#@_', Wee::$config['db_table_prefix'], $table);	
		}
		$this->_cond['table'] = $table;
		return $this;
	}
	
	
	public function order($order) {
		$this->_cond['order'] = $order;
		return $this;
	}
	
	
	public function limit($limit) {
		$this->_cond['limit'] = $limit;
		return $this;
	}
	
	
	public function offset($offset) {
		$this->_cond['offset'] = $offset;
		return $this;	
	}
	
	
	public function field($field) {
		$this->_cond['field'] = $field;
		return $this;
	}
	
	
	public function group($group) {
		$this->_cond['group'] = $group;
		return $this;
	}	
	
	
	protected function _dealDbName($dbName) {
		if (isset(Wee::$config['db_name_alias'][$dbName])) {
			$dbName = Wee::$config['db_name_alias'][$dbName];	
		}
		return $dbName;	
	}
	
	
	protected function _dealSelectSql() {
		$table = $this->_dealTable();
		$field = $this->_dealField();
		$where = $this->_dealWhere();
		$order = $this->_dealOrder();
		$group = $this->_dealGroup();
		$limit = $this->_dealLimit();
		$sql   = "SELECT $field FROM $table $where $group $order $limit";
		return $sql;	
	} 
	
	
	protected function _clearCond($key) {
		$this->_cond[$key] = null;	
	}
	
    
    protected function _dealTable() {
    	return $this->_cond['table'];
    }
    
    
    protected function _dealField() {
    	$fieldList = '*';
		if (!empty($this->_cond['field'])) {
			$fieldList = is_array($this->_cond['field']) ? implode(', ', $this->_cond['field']) : $this->_cond['field'];		
		}
		$this->_clearCond('field');
		return $fieldList;
    }
    
    
    protected function _dealWhere() {
    	$where = '';
		if (!empty($this->_cond['where'])) {
			if (is_array($this->_cond['where'])) {
				$tmpArr = array();
				foreach ($this->_cond['where'] as $key => $value) {
					if (is_numeric($key)) {
						$tmpArr[] = $value;
					} else {
						if (is_array($value)) {
							$tmpArr[] = "$key IN ('" . implode("', '", $value) . "')";	
						} else {
							$tmpArr[] = "$key = '$value'";
						}
					}
				}	
				$where = "WHERE " . implode(' AND ', $tmpArr);	
			} else {
				$where = "WHERE {$this->_cond['where']}";
			}
		}
		$this->_clearCond('where');
		return $where;
	}
    
    
    protected function _dealOrder() {
    	$order = '';
		if (!empty($this->_cond['order'])) {
			$order = "ORDER BY {$this->_cond['order']}";
		}
		$this->_clearCond('order');
		return $order;
    }
    
    
    protected function _dealGroup() {
    	$group = '';
		if (!empty($this->_cond['group'])) {
			$group = "GROUP BY {$this->_cond['group']}";
		}
		$this->_clearCond('group');
		return $group;
    }
    
   
    protected function _dealLimit() {
    	$limit = '';
    	if (!empty($this->_cond['limit'])) {
			$limit = " LIMIT {$this->_cond['limit']}";
		}
		$this->_clearCond('limit');
		return $limit;	
    }
    	
    
   
    protected function _dealId($id, $is_str = false) {
    	$str = '';
    	if (is_array($id)) {
    		if ($is_str) {
    			$str = "IN ('".implode("', '", $id)."')";
    		} else {
    			$str = "IN (".implode(", ", $id).")";
    		}
    	} else {
    		$str = "= $id";
    	}	
    	return $str;
    }
    
    
	protected function _showError($errmsg = 'error') {
		throw new Error($errmsg, Error::DB_ERROR);
	} 
	
	
	protected function _recordSqlQuery($sql, $startTime, $endTime) {
		$runTime = round($endTime - $startTime, 5);
		
		if (Wee::$config['debug_mode']) {
			Wee::$box['sqlQuery'][] = array(
				'sql' => $sql,
				'time' => date('Y-m-d H:i:s'),
				'runTime' => $runTime
			);	
    	}
    	
    	if (true == Wee::$config['db_sql_log']) {
			if ($runTime > Wee::$config['db_slow_sql_time']) {
				$sqlInfo = array(
		    		'host' => $this->_host,
		    		'db' => $this->_dbname,
		    		'sql' => $sql,
		    		'runTime' => $runTime,
		    		'time' => date('Y-m-d H:i:s')
		    	);
				Logs::sqlQuery($sqlInfo);		
			}	
		}
	}
}