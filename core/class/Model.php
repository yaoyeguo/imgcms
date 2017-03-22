<?php
/**
 * 模型基类
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-9-2 15:18
 * @version 1.0
 */
class Model {
		
	protected $db;	

	
	protected $cache;
	
	
	protected $input;

	
	protected $output;
	
	
	protected $table = null;
	
	
	protected $pk = null;
	
	
	public function __construct() {
		$this->input  = &Wee::$input;
		$this->output = &Wee::$output;
		if (Wee::$config['cache_auto_start']) {
			$this->cache = load_cache();
		}
		if (Wee::$config['db_auto_start']) {
			$this->db = load_db();	
		}
	}
	
	
	
	public function add($data) {
		$this->_checkTable();
		$rs = $this->db->table($this->table)->insert($data);
		return $rs;	
	}
	
	
	public function get($id) {
		$this->_checkTable();
		$rs = $this->db->table($this->table)
				->where(array($this->pkId => $id))
				->getOne();
		if (method_exists($this, 'getVo')) {
			$rs = $this->getVo($rs);	
		}
		return $rs;
	}
	
	
	public function getList($where = array(), $limit = '', $order = '') {
		$rs = $this->db->table($this->table)
				->where($where)
				->limit($limit)
				->order($order)
				->getAll();
		if (method_exists($this, 'getVo')) {
			foreach ($rs as & $value) {
				$value = $this->getVo($value);	
			}
			unset($value);
		}
		return $rs;
	}
	
	
	public function set($id, $data) {
		$this->_checkTable();
		$rs = $this->db->table($this->table)
			->where(array($this->pkId => $id))
			->update($data);
		return $rs;
	}	
	
	
	public function del($id) {
		$this->_checkTable();
		$rs = $this->db->table($this->table)
				->where(array($this->pkId => $id))
				->delete();
		return $rs;
	}
	
	
	protected function setTable($table, $pkId) {
		$this->table = $table;
		$this->pkId = $pkId;
	}
	
	
	
	private function _checkTable() {
		if (!$this->table || !$this->pkId) {
			show_error('$table or $pkId values are not set');
		}	
	}
}