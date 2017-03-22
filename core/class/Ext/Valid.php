<?php
/**
 * 数据验证
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-9-9 17:10
 * @version 1.0
 */
class Ext_Valid {
   
    public static $regex = array(
            'require'=> '/.+/', 
            'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            'phone' => '/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/',
            'mobile' => '/^((\(\d{2,3}\))|(\d{3}\-))?(13|15)\d{9}$/',
            'url' => '/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/',
            
            'img' => '^(http|https|ftp):(\/\/|\\\\)(([\w\/\\\+\-~`@:%])+\.)+([\w\/\\\.\=\?\+\-~`@\':!%#]|(&amp;)|&)+\.(jpg|bmp|gif|png)$',
            'currency' => '/^\d+(\.\d+)?$/',
            'number' => '/\d+$/',
            'zip' => '/^[1-9]\d{5}$/',
            'qq' => '/^[1-9]\d{4,12}$/',
            'int' => '/^[-\+]?\d+$/',
            'double' => '/^[-\+]?\d+(\.\d+)?$/',
            'english' => '/^[A-Za-z]+$/',
   );

    
    public static function check($value, $checkName) {
        $matchRegex = self::getRegex($checkName);
        return preg_match($matchRegex, trim($value));
    }

    
    public static function getRegex($name) {
        if (isset(self::$regex[strtolower($name)])) {
            return self::$regex[strtolower($name)];
        } else {
        	return $name;
        }
    }
    
	
	public static function haveInvalidChars( $str ) {
		$arr = array('\\', '/', ':', '*', '?', '"', '\'', '<', '>', ',', 
					'|', '%', '&', '&', ';', '#', '　', '');
		foreach ($arr as $ch) {
			if (false !== strstr($str, $ch)) {
				if('　' == $ch || '' == $ch) {
					return '不能显示的空字符';
				} else {
					return $ch;
				}
			}
		}
		return false;
	}
}