<?php
/**
 * 进程任务管理器
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-12-27 17:19
 * @version 1.0
 */
class Ext_Task {
	
	public static function getTaskId ($script, $bin) {
		$grepScript = preg_quote($script);
		exec ("ps -ef | grep '$grepScript'", $output);
		$procIds = array ();
		foreach ($output as $opKey => $opItem) {
			if (strstr ($opItem, "$bin $script")) {
				preg_match ("/^[^ ]+[ ]+([0-9]+).*$/", $opItem, $pregMatch);
				array_push ($procIds, $pregMatch[1]);
			}
		}
		return $procIds;
	}

	
	public static function getTaskInfo($script, $bin) {
		$grepScript = preg_quote($script);
		exec ("ps -ef | grep '$grepScript'", $output);
		$countProc = 0;
		$taskInfo = array('count' => 0, 'pid' => array(), 'cid' => array());
		foreach ($output as $opKey => $opItem) {
			if (strstr ($opItem, "$bin $script")) {
				preg_match ("/^[^ ]+\s+(\d+).*cid\=(\d+)$/", $opItem, $pregMatch);
				if (isset($pregMatch[1])) $taskInfo['pid'][] = $pregMatch[1];
				if (isset($pregMatch[2])) $taskInfo['cid'][] = $pregMatch[2];
				$taskInfo['count']++;
			}
		}
		return $taskInfo;			
	}
	
	
	public static function getTaskCount ($script, $bin) {
		$grepScript = preg_quote($script);
		exec ("ps -ef | grep '$grepScript'", $output);
		$countProc = 0;
		foreach ($output as $opKey => $opItem) {
			if (strstr ($opItem, "$bin $script")) $countProc ++;
		}
		return $countProc;
	}
	
	
	public static function paserExecTime($execTime) {
		$execTime = Ext_Array::serialToArray($execTime);
		foreach ($execTime as $key => $value) {
			$execTime[$key] = explode(',', $value);	
		}
		return $execTime;
	}	
	
	
	public static function checkExecTime($nowTime, $execTime) {
		$checked = true;
		foreach ($nowTime as $key => $val) {
			if (isset($execTime[$key]) && !in_array($val, $execTime[$key])) {
				$checked = false;	
			}
		}
		return $checked;
	}	
	
	
	public static function start($taskConfig, & $db) {
		$phpBin = $taskConfig['php_bin'];
		$waitTime = $taskConfig['wait_time'];
		$taskControlTbale = $taskConfig['table'];
		$logPath = Wee::$config['data_path'] . 'log/task_log/';
		
		
		if ($_SERVER['argc'] > 1) {
			exit('I do not need parameters...' . chr(10));	
		}
		$mainScript = $_SERVER['PHP_SELF'];
		$mainInfo = Ext_Task::getTaskInfo($mainScript, $phpBin);
		$mainCount  = $mainInfo['count'];
		if ($mainCount > 1) {
			exit('I am Already Run...' . chr(10));	
		}
		
		
		while(true) {
			$startUsec = microtime(true);
			$time  = time();
			$nowTime = Ext_Date::getInfo($time);
			
			
			$taskList = $db->table($taskControlTbale)->getAll('id');
			foreach ($taskList as $taskId => $taskControl) {			
				$maxCount = max(1, $taskControl['max_count']);
				$taskInfo = Ext_Task::getTaskInfo($taskControl['script'], $phpBin);
				$taskCount = $taskInfo['count'];
				
				
				if ($taskControl['status'] < 0) {
					if ($taskCount > 0) {
						$pids = Ext_Task::getTaskId($taskControl['script'], $phpBin);
						foreach ($taskInfo['pid'] as $pid) {
							$cmd = "kill -9 $pid";
							echo '[' . Ext_Date::format($time) . ']' . $cmd . chr(10);
							exec($cmd);	
						}
					}
					continue;	
				}
				
				
				if ($taskCount >= $maxCount) {
					continue;
				}
				
				
				if ('keep' != $taskControl['type']) { 
					
					if ('minute' == $taskControl['type']) {
						$timePart = 60;
					}
					elseif ('hour' == $taskControl['type']) {
						$timePart = 3600;	
					}
					else {
						$timePart = 86400;	
					}		
					
					if ($time < strtotime($taskControl['last_exec_time']) + $timePart) {
						continue;	
					}
					
					$checked = Ext_String::formula($taskControl['exec_time'], $nowTime);
					if (false == $checked) {
						continue;
					}
				}
			
				
				$timeStr = Ext_Date::format($time);
				$db->begin();
				$db->table($taskControlTbale)
					->where(array('id' => $taskId))
					->update(array('last_exec_time' => $timeStr, 'status' => 1));
				$db->commit();
				
				
				$logFile = Logs::getLogFile('task_' . $taskId, $time);
				$cids = array_diff(range(1, $maxCount), $taskInfo['cid']);
				foreach ($cids as $cid) {
					$cmd = "$phpBin {$taskControl['script']} cid=$cid >> $logFile &";
					echo '[' . Ext_Date::format($time) . ']' . $cmd . chr(10);
					exec($cmd);
				}
			}
			$endUsec = microtime(true);
			$waitUsec = 1000000 * ($waitTime - ($endUsec - $startUsec));
			if ($waitUsec > 0) {
				usleep($waitUsec);	
			}
		}
	}
}