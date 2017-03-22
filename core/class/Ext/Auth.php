<?php
/**
 * 权限管理
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-11-17 10:59
 * @version 1.0
 */
class Ext_Auth {
	
	const CONTENT_EDIT= 1;
	
	
	const CATE_EDIT = 2;

	
	const WEB_EDIT = 4;
	
	
	const SYS_EDIT = 8;
	
	
	public static $pres = array(
		'1' => '内容编辑',
		'3' => '栏目编辑',
		'7' => '整站编辑',
		'15' => '管理员'
	);

	
	
	public function getPre($arg) {
		$args = func_get_args();
		$pre = 0;
		foreach ($args as $value) {
			$pre |= $value;
		}
		return $pre;	
	}
	
	
	public function check($havePre, $needPre) {
		return $havePre & $needPre;	
	}
}