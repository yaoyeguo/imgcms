<?php
/**
 * 传出数据对象
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-12-27 17:20
 * @version 1.0
 */
class Request_Output {
	
	public $controllerName = null;
	
	 
	public $actionName = null;
		
	
	public $state = 0;
	
	
	public $dataMode = false;
	
	public $data = array();
	

	
	public function __construct() {
	}
	
	
	
	public function getCompiler() {
		if (!isset(Wee::$box['CompilerInstance'])) {
			Wee::$box['CompilerInstance'] = new Template_Compiler();
		}
		return Wee::$box['CompilerInstance'];
	}
	
	
	public function registerTag($name, $callback = null) {
		$this->getCompiler()->registerTag($name, $callback);
	}
	
	
	public function display($tplFile, $absPath = false) {
		if (Wee::ENTRANCE_INDEX == Wee::$config['entrance']) {
			if (is_file(Wee::$config['view_path'] . Wee::$config['template_skin'] . '/' . $tplFile)) {
				$skin = Wee::$config['template_skin'];
			} else {
				$skin = 'default';	
			}
		} elseif (Wee::ENTRANCE_ADMIN == Wee::$config['entrance']) {
			$skin = 'admin';	
		} elseif (Wee::ENTRANCE_INSTALL == Wee::$config['entrance']) {
			$skin = 'install';	
		}
		$realTplFile = Wee::$config['view_path'] . $skin . '/' . $tplFile;
		$compileFile = Wee::$config['data_path'] . 'tpl_compile/' . $skin. '/' . $tplFile . '.php';
		if ($this->getCompiler()->getCompileFile($realTplFile, $compileFile)) {
			error_reporting(E_ALL & ~E_NOTICE);
			include $compileFile;	
		}
	}
	
	
	public function makeHtml($tplFile = null, $htmlFile = null, $absPath = false) {
		$this->display($tplFile, $absPath);
		$content = ob_get_contents();
		ob_end_clean();
		if ($htmlFile) {
			Ext_File::write($htmlFile, $content);
		}
		return $content;
	}
	
	
	public function setActionName ($actionName) {
		$this->actionName = $actionName;
	}
	

	public function setControllerName($controllerName) {
		$this->controllerName = $controllerName;
	}
	
	
	public function setData ($data) {
		$this->data = $data;
	}
	
	
	
	public function setState($state = 1) {
		$this->state = $state;
	}
	
	
	public function setDataMode($mode = true) {
		$this->dataMode = $mode;	
	}
	
	
	public function get($name) {
		return isset($this->data[$name]) ? $this->data[$name] : null;	
	}
	
	
	public function set($name, $value = null) {
		if (is_object($name)) {
			$name = get_object_vars($name);	
		}
		if (is_array($name)) {
			$this->data = array_merge((array) $this->data, $name);
		} else {
			$this->data[$name] = $value;
		} 
	}
	
	
	public function __get($name) {
		return $this->get($name);
	}
	
	public function __set($name, $value) {
		$this->set($name, $value);	
	}
	
	public function __isset($name) {
		return isset($this->data[$name]);
	}
}