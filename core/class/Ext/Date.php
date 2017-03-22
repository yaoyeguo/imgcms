<?php
/**
 * 日期时间扩展
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-12-27 17:17
 * @version 1.0
 */
class Ext_Date {	
	
	public static $currentTime = 0;

	
	public static function getMicrotime() {
		return microtime(true);
	}
	
	
	public static function getTime() {
		if (0 == self::$currentTime) {
			self::$currentTime = time();	
		}
		return self::$currentTime;
	}
	 
	
	public static function now() {
		return self::getTime();	
	}
	
	
	public static function today() {
		return strtotime(date('Y-m-d 00:00:00'));	
	}
	
	
	public static function getInfo($sec = null) {
		if (!$sec) $sec = self::getTime();
		$rs = getdate($sec);
		$info = array (
			'year' => $rs['year'],
			'month' => $rs['mon'],
			'day' => $rs['mday'],
			'week' => $rs['wday'],
			'hour' => $rs['hours'],
			'minute' => $rs['minutes'],
			'second' => $rs['seconds'],
			'time' => $sec,
		);	
		foreach ($info as & $value) {
			if ($value < 10) {
				$value = '0' . $value;	
			}
		}	
		unset($value);
		return $info;	
	}
	
	
	public static function format($sec = null, $type = 'Y-m-d H:i:s') {
		if (!$sec) $sec = self::getTime();
		return date($type, $sec);
	}
	
	
	public static function life($sec) {
		if (!$sec) {
			return '';	
		}
		$now = self::getTime();
		$today = self::today();
		$limit = $now - $sec;
		if ($sec > $today) {
			if ($limit < 0) {
				$re = '';
			} elseif ($limit < 60) {
				$re = '刚刚';
			} elseif ($limit < 3600) {
				$re = floor($limit / 60) . ' 分钟前';	
			} elseif ($limit < 3600 * 24) {
				$re = floor($limit / 3600) . ' 小时前';	
			}	
		} else {
			if ($sec > ($today - 3600 * 24)) {
				$re = '昨天 ' . date('H:i', $sec);
			} elseif ($sec > ($today - 3600 * 24 *2)) {
				$re = '前天 ' . date('H:i', $sec);	
			} else {
				$re = date('Y-m-d', $sec);	
			}
		}
		return $re;
	}
	
	
	
	public static function add($time, $addTime = 0) {
		if (!is_numeric($time)) {
			$time = strtotime($time);
		}	
		$time += $addTime;
		return self::format($time); 
	}

	
	public static function getClock($sec) {
		$h = 0;
		$m = 0;
		if ($sec >= 3600) {
			$h = floor ($sec / 3600);
			$sec = $sec % 3600;
		}
		if ($sec >= 60) {
			$m = floor ($sec / 60);
			$sec = $sec % 60;
		}
		$reArr = array();
		if ($h < 10) {
			$h = '0' . $h;
		}
		if ($m < 10) {
			$m = '0' . $m;
		}
		if ($sec < 10) {
			$sec = '0' . $sec;
		}
		$restr = implode(':', array($h, $m, $sec));
		return $restr;
	}
}