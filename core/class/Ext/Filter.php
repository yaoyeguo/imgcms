<?php
/**
 * 字符过滤扩展
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-12-27 17:17
 * @version 1.0
 */
class Ext_Filter {
	
	public static function sqlChars($str) {
		$arr = array('\\', '/', ':', '*', '?', '"', '\'', '<', '>', ',', '|', '%', '&', '&', ';', '#', '　', '',' ');
		return str_replace($arr, '', $str);
	}
	
	
	public static function checkInvalidChars($str) {
		$arr = array('\\', '/', ':', '*', '?', '"', '\'', '<', '>', ',', '|', '%', '&', '&', ';', '#', '　', '',' ');
		foreach ($arr as $ch) {
			if (false !== strstr($str, $ch)) {
				if ('　' == $ch || '' == $ch) return '不能显示的空字符';
				else return $ch;
			}
		}
		return false;
	}
	
	
	public static function checkBadWord($str, & $match = array ()) {
		$badWord = require(WEE_PATH . 'misc/lang/badword_' . Wee::$config['lang']['name'] . '.php');
		$filterChar = '/|\s|\*|＊|\&|＆|\$|@|＠|\!|！|#|＃|\%|％|\^|\;|\=|\.|_|\-|\(|（|）|\)|。|「|」|『|』|〖|〗|【|】|《|》｛| ｝|¨|　|,|｜|；|‘|\"|\'|’|~|～|`|“|”|、|·|ˉ|‖|－|\/|\\\/';
		$newStr = preg_replace($filterChar, '', $str);
		$re = preg_match ($badWord, $newStr, $match);
		if (false == $re) {
			if (preg_match ("/\.\w/", $str)) {
				$urlStr = "/((https?:\/\/)?([\w%\-]+\.)+\w+\/?[&\/?=%\-\w:.]*)/iu";
				$re = preg_match ($urlStr, $str, $match);
			}
		}
		return $re;	
	}
}
