<?php
	

if (!defined('APP_PATH')) {
	exit("APP_PATH undefined");	
}


Wee::$config = array(
	'sys_name' => 'IMGCMS',	
	'sys_url' => 'http://www.imgcms.com',
	'sys_ver'  => 'V1.6',
	'default_timezone' => 'Asia/Shanghai', 
	'charset' => 'utf-8',	
	'error_types' 	=> E_ALL,		
	'debug_mode' => false,			
	'template_debug' => false,	
	'template_skin' => 'default', 	
	'error_db_log' => false,		
	'error_code_log' => false,	
	'error_exception' => true,		
	'error_source_line' => 4,		 
	'show_msg_tpl' => null,			
	'form_auto_cache' => true, 	
	
	'entrance' => 0,	
	'hack_path' => APP_PATH . 'hack/',	
	'data_path' => APP_PATH . 'data/',	
	'model_path' => APP_PATH . 'model/',  
	'controller_path' => APP_PATH . 'controller/',  
	'view_path' => APP_PATH . 'template/',	
	'config_path'	=> APP_PATH . 'config/', 
	'default_controller' => 'Main', 
	'default_action' => 'index',  
	'controller_var_name' => 'c', 	
	'action_var_name' => 'a',	
	'view_var_name' => 'v',	

	// Session_Database / Session_Memcache / Session_File
	'session_auto_start'   => false,		
	'session_handle_class' => 'Session_File',	
	'session_db_tag' => 'main',		
	'session_table' => 'session',	
	
		
	'cookie_prefix' => 'Wee',	
	'cookie_domain' => '',		
	'encrypt_key'	=> 'Gi5TzRhUjL7GzAvT',	
		
	
	'cache_auto_start' => true,			
	'cache_compress' => false,			
	'cache_config' => array('192.168.1.125:11211'),	
	'cache_table' => '#@_cache',	
		
	
	'db_auto_start' => true,  
	'db_driver' => 'Db_Mysql',		
	'db_charset' => 'utf8',		
	'db_sql_log' => false,		
	'db_slow_sql_time' => 0,	
	'db_new_link' => true,		
	'db_table_prefix' => 'my_',	
	'db_config_main' => array(		
		'host' => 'localhost', 
		'port' => '3306',		
		'user' => 'root',       
		'pass' => 'root',      
		'dbname' => 'imgcms',      		
	),
	'db_config_ini' => array(	
		'host' => 'localhost', 
		'port' => '3306',		
		'user' => 'root',       
		'pass' => 'root',      
		'dbname' => 'imgcms',      		
	),
	'db_name_alias'	=> array(		
		'ini'  => 'dev_ini',
		'log'	=> 'dev_log',	
	),
		
	
	'lang_type' => 'chs',	
	'lang_table' => '#@_lang',		
	'lang_cache' => false,	
	
		
	
	'task_sleep_time' => 1, 	
	'task_php_bin' => './php',	
	'task_db_tag'  => 'main',	
	'task_table' => 'task_control', 	
		
	'url_delimiter' => '-',		
	'url_suffix' => '.html',		
	'url_mode'	=> 0,		
	'url_rewrite' => 0,		
	'url_index' => 'index.php', 	
	'url_route' => false,	
	'url_route_rule' => array(),	
);