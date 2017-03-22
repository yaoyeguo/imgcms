<?php
/**
 * 数学处理扩展
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-12-27 17:18
 * @version 1.0
 */
class Ext_Math {
	
	public static function getLockKey() {
		return mt_rand(0, 65535);	
	}
	
	
	public static function getProbabilityKey ($array, $except = null, $degree = 100) {
		if (!is_null ($except)) {
			unset ($array[$except]);
		}
		$total = array_sum ($array) * $degree;
		if (!$total) {
			return false;
		}
		$intRand = mt_rand (0, $total);
		$offset = 0;
		foreach ($array as $key => $item) {
			$value = $item * $degree;
			if ($intRand <= $value + $offset) {
				$result = $key;
				break;
			}
			$offset += $value;
		}
		return $result;
	}

	
	public static function probability ($value, $base = 100, $degree = 100) {
		if ($value <= 0) return false;
		$value = $value * $degree;
		$rand = mt_rand (1, $degree * $base);
		return ($rand <= $value);
	}
	
	
	public static function evalString($str, $args) {
		if (is_array($args) && count($args) > 0) {
			extract($args);
		}
		eval ("\$result = ($str);");
		return $result;	
	}
	
	
	public static function lifeByte($byte) {
		if ($byte > 1024 * 1024 * 1024) {
			$lifeByte = sprintf('%0.2f', $byte / 1024 / 1024 / 1024) . 'G';
		} elseif ($byte > 1024 * 1204) {
			$lifeByte = sprintf('%0.2f', $byte / 1024 / 1024) . 'M';	
		} else {
			$lifeByte = sprintf('%0.2f', $byte / 1024) . 'K';	
		}
		return $lifeByte;
	}
}
