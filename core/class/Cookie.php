<?php
/**
 * Cookie管理
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-12-27 17:15
 * @version 1.0
 */
class Cookie {
    
    public static function isExist($name) {
        return isset($_COOKIE[$name]);
    }

   	
    public static function get($name) {
    	$value = isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
    	if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; Network)';	
    	}
    	$encrypt_key = md5(Wee::$config['encrypt_key'] . $_SERVER['HTTP_USER_AGENT']);
    	$value = Ext_String::decrypt($value, $encrypt_key);
    	$value = @unserialize($value);
        return $value;
    }
    
    
    public static function getAll() {
    	$cookie = array();
    	foreach ($_COOKIE as $key => $value) {
    		$cookie[$key] = self::get($key);
    	}
    	return $cookie;	
    }

    
    public static function set($name, $value, $expire = 0, $path = '/', $domain = '') {
        if (!$domain) $domain = Wee::$config['cookie_domain'];
        if ($expire) $expire += Ext_Date::now(); 
        if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; Network)';	
    	}
        $encrypt_key = md5(Wee::$config['encrypt_key'] . $_SERVER['HTTP_USER_AGENT']);
        $value = serialize($value);
        $value = Ext_String::encrypt($value, $encrypt_key);
        $_COOKIE[$name] = $value;
        setcookie($name, $value, $expire, $path, $domain);
    }


	
    public static function delete($name, $path = '/', $domain = '') {
        if (!$domain) $domain = Wee::$config['cookie_domain'];
        unset($_COOKIE[$name]);
        setcookie($name, null, Ext_Date::now() - 3600, $path, $domain);
    }
    
    
    public static function clear($path = '/', $domain = '') {
    	foreach ($_COOKIE as $key => $value) {
    		self::delete($key, $path, $domain);
    	}
    }
}
