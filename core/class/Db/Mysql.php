<?php
/**
 *  MYSQL数据库驱动
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-9-2 15:27
 * @version 1.0
 */
class Db_Mysql extends Db {	
	
	private $_currentDb;

	
	private $_link;

	
	private $_sql;

	
	private $_affectRows = 0;
	
	
	private $_insertId = 0;



	
	public function __construct($host, $port, $user, $pass, $dbname = null) {
		if (!function_exists('mysql_connect')) {
			show_error('Mysql support is disabled');	
		}
		$this->_host = $host;
		$this->_port = $port;
		$this->_user = $user;
		$this->_pass = $pass;
		$this->_dbname = $dbname;
		$this->_newLink = Wee::$config['db_new_link'];
		$this->_charset = Wee::$config['db_charset'];
	}			
	
	
	public function connect() {
		if (!$this->_link || !is_resource($this->_link)) {
			$this->_link = @mysql_connect($this->_host . ':' . $this->_port, $this->_user, $this->_pass, $this->_newLink);
			if (!$this->_link) {
				$this->_showError("$this->_host: 数据库服务器连接失败");	
			}
			if ($this->_dbname) {
				$this->useDb($this->_dbname);
			}
		}	
	}
	

	
	
	public function usedb($dbname) {
		$dbname = $this->_dealDbName($dbname);
		if ($dbname != $this->_currentDb) {
			if (!$this->_link || !is_resource($this->_link)) {
				$this->connect();
			} 
			$flag = mysql_select_db($dbname, $this->_link);
			if (!$flag) {
				$this->_showError($dbname.': 数据库不存在或者无法使用');	
			}
			$this->_currentDb = $dbname;
			mysql_query("SET NAMES '{$this->_charset}'", $this->_link);
		}
		return $this;
	}
	
	
	public function getCurrentDb() {
		return $this->_currentDb;	
	}
		
	
	public function getRows($sql, $asKey = null) {
		$query  = $this->query($sql);
		$res 	= array();
		while($value = mysql_fetch_assoc($query)) {
			if ($asKey) {
				$res[$value[$asKey]] = $value;
			} else {
				$res[] = $value;
			}
		}
		return $res;
	}
	
	
	public function fetch($query) {
		return mysql_fetch_assoc($query);
	}		
	
	
    public function query($sql) {
    	if (!$this->_link || !is_resource($this->_link)) {
    		$this->connect();	
    	}
    	$sql 		   = trim($sql);
    	$this->_sql    = $sql;
    	$_runStartTime = microtime(true);	
    	$isInsert 	   = (0 === stripos($sql, 'INSERT INTO'));
    	$rs 		   = mysql_query($sql, $this->_link);
		$_runEndTime   = microtime(true);
		
		
		$this->_recordSqlQuery($sql, $_runStartTime, $_runEndTime);
    	
    	
		if ($rs) {
			$this->_affectRows = mysql_affected_rows($this->_link);
			if ($isInsert) {
	        	$this->_insertId = mysql_insert_id($this->_link);	
	        }
	        return $rs;	
		} else {
        	$errno = mysql_errno($this->_link);
        	if ($this->_queryError) {
        		$this->_showError("SQL Query Error(" 
        						. mysql_error($this->_link) 
        						. " [$errno]): $sql");	
        	}
        	return false;
        }
    }
    
    
    public function insertId() {
    	return $this->_insertId;	
    }

    
    public function affectRows() {
    	return $this->_affectRows;
    }

    
    public function close() {
        if ($this->_link && is_resource($this->_link)) {
            return mysql_close($this->_link);
        }
    }
    
    
    public function lastSql() {
    	return $this->_sql;	
    }
}