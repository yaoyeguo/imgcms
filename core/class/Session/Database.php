<?php
/**
 * Session Database存贮
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-9-2 15:30
 * @version 1.0
 */
class Session_Database extends Session {	
	
	private $_table = '#@_session';
	
	
	private $_db = null;
	
	
	public function __construct($db, $table) {
		$this->_db = $db;
		$this->_table = $table;
		$this->_lifetime = ini_get('session.gc_maxlifetime');
		session_set_save_handler(
			array($this, '_open'),
		  	array($this, '_close'),	
		  	array($this, '_read'),	
		  	array($this, '_write'),	
		  	array($this, '_destroy'),	
		  	array($this, '_gc')	
		);	
	}
	

	
	public function _open($savePath, $name) {
		return true;	
	}
	
	
	public function _close() {
		$this->_gc($this->_lifetime);
		return true;	
	}
	
	
	public function _read($id) {		
		$res = $this->_db->table($this->_table)
						->where(array('id' => $id))
						->getOne();
		return $res ? $res['value'] : '';
	}
	
	
	public function _write($id, $value) {
		$data = array('id' => $id, 'value' => $value, 'exp' => time() + $this->_lifetime);
		$flag = $this->_db->table($this->_table)
						 ->replace($data);
		return $flag;
		
	}
	
	
	public function _destroy($id) {
		$flag = $this->_db->table($this->_table)->where("id = '$id'")->delete();
		return $flag;	
	}
	
	
	public function _gc($lifetime) {
		$flag = $this->_db->table($this->_table)->where("exp < ".time())->delete();
		return $flag;	
	}
}
