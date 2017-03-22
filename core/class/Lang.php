<?php
/**
 * 语言包管理
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-12-27 17:15
 * @version 1.0
 */
class Lang {	
	
	private static $_table = '#@_lang';

	
	private static $_type = 'chs';
	
	
	private static $_db;
	

	
	public function __construct($type, $table) {
		$this->_type = $type;
		$this->_table = $table; 
		$this->_db = load_db();
	}
	
	private function _getLangPack() {
		$table = Wee::$config['lang_table'] . '_' . Wee::$config['lang_type'];
		$package = load_db()->table($table)->getAll();
		$package = Ext_Array::format($package, 'name', 'value');
		return $package;	
	}
	
	
	public static function get($name, $args = array()) {
		if (!isset(Wee::$box['lang'])) {
			if (Wee::$config['lang_cache']) {
				$langFile = Wee::$config['data_path'] . 'cache/' 
								.'lang_' . Wee::$config['lang_type'] . '.php';
				if (is_file($langFile)) {
					Wee::$box['lang'] = require $langFile;
				} else {
					$langPack = self::_getLangPack();
					$rs = Ext_File::writeArray($langFile, $langPack); 
					if (!$rs) {
						show_error("Written language pack cache file failed.");	
					}
					Wee::$box['lang'] = $langPack;
				}
			} else {
				$langPack = self::_getLangPack();
				Wee::$box['lang'] = $langPack;
			}	
		}
		if (empty(Wee::$box['lang'][$name])) {
			return $name;	
		}
		$text = Wee::$box['lang'][$name];
		if ($args) {	
			if (!is_array($args)) {
				$args = func_get_args();
				array_shift($args);	
			}
			$reArr = array();
			foreach ($args as $key => $value) {
				$reArr["%$key"] = $value;	
			}
			$text = strtr($text, $reArr);
		}
 		return $text;
	}	
	
	
	public static function set($langName, $name, $value, $package = '') {
		self::init();
		self::$_db->useDb(self::$_dbname);
		$tableName = self::getRealTableName($langName);
		$info = self::$_db->table($tableName)
						  ->where(array('name' => $name))
						  ->getOne();
		$data = array('value' => $value);
		if ($package) {
			$data['package'] = $package;
		}
		if ($info) {
			self::$_db->table($tableName)
					  ->where(array('name' => $name))
					  ->update($data);	
		}	
		else {
			$data['name'] = $name;
			self::$_db->table($tableName)
					  ->insert($data);	
		}
		if (Wee::$config['ini_use_cache']) {
			$cache = Cache_Mem::factory();
			$cache->del($cache->makeName($name, 'lang', false));
		}		
		return self::$_db->affectRows();
	}
	
	
	public static function clear() {
		self::init();
		if (Wee::$config['ini_use_cache']) {
			$cache = Cache_Mem::factory();
			$cache->clear();
		}
	}
	
	
	public static function getRealTableName($langName) {
		return self::$_table . '_' . $langName;	
	}
}