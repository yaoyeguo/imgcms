<?php
/**
 * 基础控制器
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-8-30 15:53
 * @version 1.0
 */
class Base_Controller extends Controller {
	private $_isLogin = false;
	
	protected $adminInfo = null;
	
	public function __construct($checkLogin = true) {
		parent::__construct();
		$this->assignConfig();
	}
	
	protected function checkLogin($needPre = 0) {
		$adminMod = load_model('Admin');
		if ('Main' == $this->input->getControllerName()
				&& 'login' == $this->input->getActionName()) {
			return;			
		}
		$this->adminInfo = $adminMod->getAdminInfo();
		if (!$this->adminInfo) {
			show_msg('登陆后才能继续操作', '?c=Main&a=login', 0);
		}
		if ($needPre) {
			if (!Ext_Auth::check($this->adminInfo['pre'], $needPre)) {
				show_msg('权限不足');	
			}
		}
		return true;		
	}
		

	protected function assignConfig() {
		$this->output->set(array(
			'sys_name' => Wee::$config['sys_name'],
			'admin_name' => Wee::$config['admin_name'],
			'led_values' => Wee::$config['led_values'],
			'led_ok' => Wee::$config['led_ok'],
			'led_link' => Wee::$config['led_link'],
			'sys_url' => Wee::$config['sys_url'],
			'sys_ver' => Wee::$config['sys_ver'],
			'web_uri' => Wee::$config['web_uri'],
			'web_dir' => Wee::$config['web_dir'],
			'web_script' => Wee::$config['web_script'],
			'web_host' => Wee::$config['web_host'],
			'upload_max_num' => Wee::$config['upload_max_num'],
		));	
	}
}