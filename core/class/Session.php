<?php
/**
 * Session会话管理
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-12-27 17:16
 * @version 1.0
 */
abstract class Session {		
	
	protected $_lifetime = 1440;
	
	
	public static function start() {
		$handleClass = Wee::$config['session_handle_class'];
		if ('Session_Database' == $handleClass) {
			$db = load_db(Wee::$config['session_db_tag']);
			$handle = new Session_Database($db, Wee::$config['session_table']);
		} elseif ('Session_Memcache' == $handleClass) {
			$cache = load_cache();
			$handle = new Session_Memcache($cache);	
		}
		session_start();
	}
	
	
	abstract public function _open($save_path, $name);

	
	abstract public function _close();
	
	
	abstract public function _read($id);
	
	
	abstract public function _write($id, $value);
	
	
	abstract public function _destroy($id);
	
	
	abstract public function _gc($lifetime);

    
    public static function pause() {
        session_write_close();
    }


    
    public static function clear() {
        $_SESSION = array();
		session_destroy();
    }

    
    public static function name($name = null) {
        return isset($name) ? session_name($name) : session_name();
    }

    
    public static function id($id = null) {
        return isset($id) ? session_id($id) : session_id();
    }

   
    public static function path($path = null) {
        return !empty($path) ? session_save_path($path) : session_save_path();
    }

    
    public static function get($name) {
    	return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    
    public static function set($name, $value) {
        if (is_null($value)) {
            unset($_SESSION[$name]);
        } 
        else {
            $_SESSION[$name] = $value;
        }
        return true;
    }

    
    public static function isExist($name) {
        return isset($_SESSION[$name]);
    }
    
    
    public static function delete($name) {
    	unset($_SESSION[$name]);
    }
    
    
    public static function setLifeTime($time = 3600) {
    	$now = time();
    	if ($time > 0) $time = $now + $time;
    	elseif ($time < 0) $time = $now - $time;
    	setcookie(self::name(), self::id(), $time, "/");
    }
}
