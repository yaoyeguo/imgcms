<?php
/*
	Copyright (c) 2008-2012 PicCMS.Com All rights reserved.
	This is NOT a freeware, use is subject to license terms
	$Author: 许仙 <QQ:1216560669 > $
	$Time: 2011-12-27 17:21 $
*/


function __autoload_class($className) {
	if (class_exists($className, false)) {
		return true;	
	}
	$tmpArr = explode('_', $className);
	$suffix = null;
	if (count($tmpArr) > 1) {
		$suffix = end($tmpArr);
	}
	if ('Controller' == $suffix) { 
		array_pop($tmpArr);
		$classFile = Wee::$config['controller_path'] 
					. implode('/', $tmpArr)
					. '.php';
	} else if ('Model' == $suffix) {
		array_pop($tmpArr);
		$classFile = Wee::$config['model_path'] 
					. implode('/', $tmpArr)
					. '.php';
	} else {
		$classFile = CORE_PATH . 'class/' 
				. strtr($className, array('_' => '/')) 
				. '.php';	
	}
	import_file($classFile, false);
	return class_exists($className, false);
}


function __shutdown() {
}



function __error_handler($errno, $errmsg, $errfile, $errline) {
	if (!(error_reporting() & $errno)) {
		return;	
	}
	if (isset(Wee::$output)) {
		Wee::$output->setState(-1);	
		Wee::$output->set('errorMsg', $errmsg);
	}
	throw new Error($errmsg, Error::PHP_ERROR) ;
}


function catch_error($e) {
	$error = $e->getError();
	if (Error::USER_MSG == $error['code']) {
		return;	
	}
	
	require CORE_PATH . '/misc/show_error.php';
	/*
	if (Wee::$config['debug_mode']) {	
		require CORE_PATH . '/misc/show_error.php';
	} else {
		show_msg("{$error['type']}: {$error['message']} [File:{$error['file']} Line:{$error['line']}]");
	}
	*/
	
	
	if (Error::DB_ERROR == $error['code'] && Wee::$config['error_db_log']) {
		$logCon = "{$error['type']}: {$error['message']}\n" . implode("\n", $error['trace']);
		Logs::errorDbLog($logCon);
	}
	
	
	if ((Error::CODE_ERROR == $error['code'] || Error::PHP_ERROR == $error['code']) && Wee::$config['error_code_log']) {
		$logCon = "{$error['type']}: {$error['message']}\n" . implode("\n", $error['trace']);
		Logs::errorCodeLog($logCon);		
	}
} 



function set_config($name, $value = null) {
	if (is_array($name)) {
		Wee::$config = array_merge(Wee::$config, $name);	
	} else {
		if (is_null($value)) {
			return Wee::$config[$name];
		} else {
			Wee::$config[$name] = $value;	
		}
	}	
}


function write_config($file, $data) {
	$arr = array();
	foreach ($data as $key => $value) {
		$arr[] = "Wee::\$config['$key'] = " . var_export($value, true) . ';';
	}	
	$content = "<?php\n" . implode("\n", $arr);
	$rs = Ext_File::write($file, $content);
}



function dump($vars) {
	$varsArr = func_get_args();
	if (count($varsArr) > 1) {
		$vars = $varsArr;	
	}
	$content = (print_r($vars, true));
	$content = "<fieldset><pre>"
			 . $content
			 . "</pre></fieldset>\n";	
    echo $content;	
}


function url($module = '', $action = '', $args = array(), $mode = null) {
	if (is_null($mode)) {
		$mode = Wee::$config['url_mode'];
	}
	if (0 == $mode) {
		$tmpArr = array();
		if ($module) {
			$tmpArr[Wee::$config['controller_var_name']] = $module;
		}
		if ($action) {
			$tmpArr[Wee::$config['action_var_name']] = $action;
		}
		$tmpArr = array_merge($tmpArr, $args);
		if ($tmpArr) {
			$url = Wee::$config['url_index'] . '?' . http_build_query($tmpArr);
		} else {
			$url = '';	
		}
		return Wee::$config['web_url'] . $url;
	} 
	if (1 == $mode || 2 == $mode) {
		if (!$module) {
			$module = Wee::$config['default_controller'];
		}
		if (!$action) {
			$action = Wee::$config['default_action'];	
		}
		if (Wee::$config['url_route'] && isset(Wee::$config['url_route_reverse'][$module . '-' . $action])) {
			$routeName = Wee::$config['url_route_reverse'][$module . '-' . $action];
			$tmpArr = array($routeName);
			foreach ($args as $value) {
				$tmpArr[] = urlencode($value);	
			}
		} else {
			$tmpArr = array($module, $action);
			foreach ($args as $key => $value) {
				$tmpArr[] = $key;
				$tmpArr[] = urlencode($value);
			}
		}
		$url = implode(Wee::$config['url_delimiter'], $tmpArr) . Wee::$config['url_suffix'];
		if (1 == $mode) {
			return Wee::$config['web_url'] . Wee::$config['url_index'] . '?' . $url;
		} else {
			return Wee::$config['web_url'] . $url;	
		}
	}
}




function check_submit($name = 'submit') {
	return !empty($_POST[$name]);		
}


function inajax() {
	if (!empty($_REQUEST['inajax'])) {
		return true;
	}
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    	return true;
    } 
	return false;
}



function load_model($modelName, $single = true) {
	$className = $modelName . '_Model';
	if (!$single) {
		return new $className();	
	}
	if (!isset(Wee::$box['ModelInstance'][$className])) { 
		Wee::$box['ModelInstance'][$className] = new $className();
	}
	return Wee::$box['ModelInstance'][$className];		
}



function show_error($errorMsg) {
	if (Wee::$output) {
		Wee::$output->setState(-1);	
		Wee::$output->set('errorMsg', $errorMsg);
	}
	throw new Error($errorMsg, Error::CODE_ERROR);
}



function show_msg($errorMsg, $url = null, $refresh = 3, $backUrl = "javascript:history.go(-1)") {
	if (isset(Wee::$output)) {
		Wee::$output->setState(-2);	
		Wee::$output->set('errorMsg', $errorMsg);
	}
	if ($url && 0 == $refresh) {
		ob_end_clean();
		header("Location: $url");	
	}
	if (!$url) {
		$url = $backUrl;	
	}
	$inajax = Wee::$input->get('inajax');
	$inframe = Wee::$input->get('inframe');
	if (Wee::$config['show_msg_tpl']) {
		require Wee::$config['show_msg_tpl'];
	} else {
		require CORE_PATH . '/misc/show_msg.php';
	}
	throw new Error($errorMsg, Error::USER_MSG);
}


function js_run($js) {
	echo "<script>$js</script>";
	ob_flush();
	flush();	
}




function import_file($fileName, $blackout = false) {
	if (!isset(Wee::$box['importFiles'][$fileName])) {	
		if (is_file($fileName)) {
			require $fileName;	
		} else {
			if ($blackout) {
				exit("$fileName: File not exists");	
			}
			return false;
		}
		Wee::$box['importFiles'][$fileName] = true;
	}
	return true;	
}
	

function load_cache() {
	if (!isset(Wee::$box['CacheInstance'])) {
		$obj = new Cache();
		Wee::$box['CacheInstance'] = $obj;
	}
	return Wee::$box['CacheInstance'];
}

	

function load_db($tag = 'main') {
	if (!isset(Wee::$box['DbInstance'][$tag])) {
		$cfgkey = 'db_config_' . $tag;
		if (isset(Wee::$config[$cfgkey])) {
			$dbCfg = Wee::$config[$cfgkey];	
		} else {
			exit("$tag: The dbtag does not exist");	
		}
		if ('Db_Mysql' == Wee::$config['db_driver']) {
			$driverName = 'Db_Mysql';
		} else if ('Db_Mysqli' == Wee::$config['db_driver']) {
			$driverName = 'Db_Mysqli';	
		} else {
			exit(Wee::$config['db_driver'] . ": The DB driver does not exist");
		}
		$db	= new $driverName( 
				$dbCfg['host'],
				$dbCfg['port'], 
				$dbCfg['user'], 
				$dbCfg['pass'], 
				$dbCfg['dbname']	
		);
		Wee::$box['DbInstance'][$tag] = $db;	
	}
	return Wee::$box['DbInstance'][$tag];
}
	

function get_runtime($more = true) {
	Wee::$box['_runEndTime'] = microtime(true);
	Wee::$box['_runTime'] = round(Wee::$box['_runEndTime'] - Wee::$box['_runStartTime'], 4);
	if ($more) {
		$data = array(
			'startTime' => Wee::$box['_runStartTime'],
			'endTime' => Wee::$box['_runEndTime'],
			'runTime' => Wee::$box['_runTime'],
			'sqlQueryNum' => 0,
			'sqlQueryTime' => 0
		);
		if (isset(Wee::$box['sqlQuery'])) {
			$data['sqlQueryNum'] = count(Wee::$box['sqlQuery']);
			foreach (Wee::$box['sqlQuery'] as $value) {
				$data['sqlQueryTime'] += $value['runTime'];
			}	
		}
		return $data;
	} else {
		return Wee::$box['_runTime'];
	}
}

