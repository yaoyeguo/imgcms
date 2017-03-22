<?php
/**
 * MYSQLI 数据库驱动
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-9-2 15:27
 * @version 1.0
 */
class Db_Mysqli extends Db {	
	
	private $_currentDb;

	
	private $_link;

	
	private $_sql;

	
	private $_affectRows = 0;
	
	
	private $_insertId = 0;



	
	public function __construct($host, $port, $user, $pass, $dbname = null) {
		if (!function_exists('mysqli_connect')) {
			show_error('Mysqli support is disabled');	
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
		if (!$this->_link) {
			$mysqli = @mysqli_connect($this->_host, $this->_user, $this->_pass, '', $this->_port);
			if ($mysqli->connect_error) {
				$this->_showError("$this->_host: 数据库服务器连接失败");
			}
			$this->_link = $mysqli;
			mysqli_autocommit($this->_link, true);
			if ($this->_dbname) {
				$this->useDb($this->_dbname);
			}
		}	
	}
	

	
	
	public function usedb($dbname) {
		$dbname = $this->_dealDbName($dbname);
		if ($dbname != $this->_currentDb) {
			if (!$this->_link) {
				$this->connect();
			} 
			$flag = mysqli_select_db($this->_link, $dbname);
			if (!$flag) {
				$this->_showError($dbname.': 数据库不存在或者无法使用');	
			}
			$this->_currentDb = $dbname;
			mysqli_query($this->_link, "SET NAMES '{$this->_charset}'");
		}
		return $this;
	}
	
	
	public function getCurrentDb() {
		return $this->_currentDb;	
	}
		
	
	public function getRows($sql, $asKey = null) {
		$query  = $this->query($sql);
		$res 	= array();
		while($value = mysqli_fetch_assoc($query)) {
			if ($asKey) {
				$res[$value[$asKey]] = $value;
			} else {
				$res[] = $value;
			}
		}
		return $res;
	}		
	
	
	public function fetch($query) {
		return mysqli_fetch_assoc($query);
	}	
	
	
    public function query($sql) {
    	if (!$this->_link) {
    		$this->connect();	
    	}
    	$sql 		   = trim($sql);
    	$this->_sql    = $sql;
    	$_runStartTime = microtime(true);	
    	$isInsert 	   = (0 === stripos($sql, 'INSERT INTO'));
    	$rs 		   = mysqli_query($this->_link, $sql);
    	$_runEndTime	   = microtime(true);
    	
    	
    	$this->_recordSqlQuery($sql, $_runStartTime, $_runEndTime);
    	
    	
		if ($rs) {
			$this->_affectRows = mysqli_affected_rows($this->_link);
			if ($isInsert) {
	        	$this->_insertId = mysqli_insert_id($this->_link);	
	        }
	        return $rs;	
		} else {
        	$errno = mysqli_errno($this->_link);
        	if ($this->_queryError) {
        		$this->_showError("SQL Query Error(" 
        						. mysqli_error($this->_link) 
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
        if ($this->_link) {
            $rs = mysqli_close($this->_link);
            if ($rs) {
            	$this->_link = null;
            }
        }
    }
    
    
    public function lastSql() {
    	return $this->_sql;	
    }
}