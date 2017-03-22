<?php
/**
 * 日志管理类
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-9-2 15:26
 * @version 1.0
 */
class Logs {
	
	public static $maxFileSize = 10000000;
	
	
	public static function write($logContent, $logFile) {
		if (is_file($logFile) && filesize($logFile) > self::$maxFileSize) {
			Ext_File::write($logFile, $logContent);
		} else {
			Ext_File::write($logFile, $logContent, FILE_APPEND);
		}	
	}

	
	public static function errorDbLog($err) {
		if (is_array($err)) $err = implode("\n", $err);
		$logFile = Logs::getLogFile('error_db');
		$logContent = "\n[ " . date('Y-m-d H:i:s') . " ]\n" . $err . "\n";
		self::write($logContent, $logFile);
	}
	
	
	public static function errorCodeLog($err) {
		if (is_array($err)) $err = implode("\n", $err);
		$logFile = Logs::getLogFile('error_code');
		$logContent = "\n[ " . date('Y-m-d H:i:s') . " ]\n" . $err . "\n";
		self::write($logContent, $logFile);
	}	
	
	
	public static function sqlQuery($info) {
		$logContent = "HOST: {$info['host']} | DB: {$info['db']} | Run time: {$info['runTime']} | SQL: {$info['sql']}\n";
		$logFile = Logs::getLogFile('sql_query');
		$logContent = "\n[ " . $info['time'] . " ]\n" . $logContent;
		self::write($logContent, $logFile);			
	}

	
	
	public static function getLogFile($logId, $time = null) {
		if (!$time) $time = Ext_Date::now();
		if (Wee::REQUEST_SERVER == Wee::$requestType) {
			$logPath = Wee::$config['data_path'] . 'server/' . Ext_Date::format($time, 'Ymd');
		} else {
			$logPath = Wee::$config['data_path'] . 'client/' . Ext_Date::format($time, 'Ymd');	
		}
		if (!is_dir($logPath)) {
			Ext_Dir::mkdirs($logPath);	
		}
		$logFile = $logPath . '/' . $logId . '.log';
		return $logFile;
	}
}