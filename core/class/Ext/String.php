<?php
/**
 * 字符串处理扩展
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-12-27 17:18
 * @version 1.0
 */
class Ext_String {
	
	public static function strlen($str, $charset = '') {
		if (!$charset) {
			$charset = 'utf-8';
		}
		if (function_exists('iconv_strlen')) {
			return iconv_strlen($str,$charset);
		} elseif (function_exists('mb_strlen')) {
			return mb_strlen($str, $charset);
		} 
	}

	
	public static function stripTags($str) {
		return preg_replace ('/<[^>]*>/', '', $str);	
	}
	
	
	public static function cut($str, $length, $suffix = '', $charset = 'utf-8') {
		if (Ext_String::strlen($str) > $length) {
			if (function_exists('iconv_substr')) {
				$str = iconv_substr($str, 0, $length, $charset);
			} 
			elseif (function_exists('mb_substr')) {
				$str = mb_substr($str, 0, $length, $charset);
			} 
			$suffix && $str .= $suffix;
		}	
		return $str;
	}
	
	
	public static function hash ($str, $level = 1, $length = 2) {
		$hash = hash ('md5', strtolower ($str));
		for ($i = 0; $i < $level; $i ++) {
			$hashParts[] = substr ($hash, $i * $length, $length);
		}
		$hash = implode ('/', $hashParts);	
		return $hash;
	}
	
	
	public static function formula ($formula, $assignVars) {
		if (!trim ($formula)) return null;
		if (is_array ($assignVars) && count ($assignVars) > 0) extract ($assignVars);
		eval ("\$formulaResult = ($formula);");
		return $formulaResult;
	}
		
	
	public static function getSalt($num = 4) {
	    $str = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVW";
	    $rs  = '';
	    $len = strlen($str) - 1;
	    for ($i = 0; $i < $num; $i++) {
	    	$rs .= $str[mt_rand(0, $len)];
	    }
		return $rs;
	}
	
	
	public static function encrypt ($string, $key) {
		srand ((double)microtime () * 1000000);
		$encryptKey = md5 (mt_rand (0, 32000));
		$ctr = 0;
		$tmp = "";
		for ($i = 0; $i < strlen ($string); $i++) {
			if ($ctr == strlen ($encryptKey)) {
				$ctr=0;
			}
			$tmp .= substr ($encryptKey, $ctr, 1) . (substr ($string, $i, 1) ^ substr ($encryptKey, $ctr, 1));
			$ctr ++;
		}
		return base64_encode (self::_keyed ($tmp, $key));
	}

	
	public static function decrypt ($string, $key) {
		$string = self::_keyed (base64_decode ($string), $key);
		$tmp = "";
		for ($i=0; $i < strlen ($string); $i++) {
			$md5 = substr ($string, $i, 1);
			$i ++;
			$tmp.= (substr ($string, $i, 1) ^ $md5);
		}
		return $tmp;
	}

	private static function _keyed ($string, $encryptKey) {
		$encryptKey = md5 ($encryptKey);
		$ctr = 0;
		$tmp = "";
		for ($i = 0; $i < strlen ($string); $i++) {
			if ($ctr == strlen ($encryptKey)) {
				$ctr=0;
			}
			$tmp .= substr ($string, $i, 1) ^ substr ($encryptKey, $ctr, 1);
			$ctr ++;
		}
		return $tmp;
	}
	
	
	public static function passHash($password) {
		$hash = md5($password);
		return md5(substr($hash, 16, 16) . substr($hash, 0, 16));
	}
	
	
    public static function cutPreg($startFlag, $endFlag, $str, $mode = 's') {
    	if (preg_match('/'.preg_quote($startFlag).'(.*?)'.preg_quote($endFlag)."/{$mode}", $str, $result)) {
    		$str = $result[0];
    	} 
    	return $str;
    }
    
    
    public static function fetchLinks($str) {
    	$flag = "/<\s*a\s.*?href\s*=\s*([\"\'])?(?(1)(.*?)\\1|([^\s\>]+))(?:.*?)>(.*?)<\/a>/is";
    	$match = array();
    	if (preg_match_all($flag, $str, $links)) {
	    	foreach ($links[0] as $key => $val) {
	    		if (!empty($links[2][$key])) {
	    			$value['url'] = $links[2][$key];
	    			$value['title'] = $links[4][$key];
	    			$match[] = $value;
	    		} elseif (!empty($links[3][$key])) {
	    		    $value['url'] = $links[3][$key];
	    			$value['title'] = $links[4][$key];
	    			$match[] = $value;
	    		}	
	    	}
    	}
    	return $match;	
    }
    
    
    public static function fetchImages($str) {
    	$patten = '/<img.*?src=[\'\"]?([^\s>\"\']+)[\'\"][^>]*>/is';
    	$match = array();
		if (preg_match_all($patten, $content, $arr)) {
			foreach ($arr[1] as $value) {
				$match[] = array(
					'src' => $value,
					'title' => '',
				);
			}
		}
		return $match;	
    }
    
    
    public static function base64UrlEncode($input) {
	    return strtr(base64_encode($input), '+/=', '-_!');
	}

	public static function base64UrlDecode($input) {
	    return base64_decode(strtr($input, '-_!', '+/=')); 
	} 

}
