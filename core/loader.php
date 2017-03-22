<?php
/*
	Copyright (c) 2008-2012 PicCMS.Com All rights reserved.
	This is NOT a freeware, use is subject to license terms
	$Author: 许仙 <QQ:1216560669 >$
	$Time: 2011-12-27 17:14 $
*/

if (!defined('APP_PATH')) {
	exit("APP_PATH undefined");	
}


define('CORE_PATH', rtrim(dirname(__FILE__), '/\\') . DIRECTORY_SEPARATOR);



require_once 'class/Wee.php';


require_once 'common/funs.php';



Wee::$box['_runStartTime'] = microtime(true);


spl_autoload_register('__autoload_class');


register_shutdown_function('__shutdown');


require CORE_PATH . 'common/config.php';


if (is_file(APP_PATH . 'data/config.php')) {
	require APP_PATH . 'data/config.php';	
}


if (Wee::$config['url_route']) {
	if (Wee::$config['url_route_rule']) {
		foreach (Wee::$config['url_route_rule'] as $key => $value) {
			Wee::$config['url_route_reverse'][$value[0] . '-' . $value[1]] = $key;
		}
	}		
}


if (Wee::$config['debug_mode']) {
	//Wee::$config['error_types'] = E_ALL & ~E_NOTICE;
} else {
	Wee::$config['error_types'] = 0;
}
error_reporting(Wee::$config['error_types']);

if (Wee::$config['error_exception']) {
	set_error_handler('__error_handler', Wee::$config['error_types']);
}
set_exception_handler('catch_error');


if (function_exists('date_default_timezone_set')) {
	date_default_timezone_set(Wee::$config['default_timezone']);
}


if (Wee::$config['form_auto_cache']) {
	header('Cache-Control: private,must-revalidate');
	session_cache_limiter('private,must-revalidate');
}


if (Wee::$config['session_auto_start']) {
	Session::start();	
}


header("Content-type: text/html; charset=" . Wee::$config['charset']);


ob_start();

