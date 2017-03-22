<?php
/*
	Copyright (c) 2008-2014 IMGCMS.Com All rights reserved.
	This is NOT a freeware, use is subject to license terms
	$Author: 许仙 <QQ:1216560669 > $
	$Time: 2011-12-27 17:23 $
*/
	
define('APP_PATH', rtrim(dirname(__FILE__), '/\\') . DIRECTORY_SEPARATOR);

require_once './core/loader.php';

Wee::run('Attach', 'index');