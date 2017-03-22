<?php
/**
 * 传入数据对象
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-9-2 15:28
 * @version 1.0
 */
class Request_Input {
	
	public $controllerName;

	
	public $actionName;

	
	public $args;
	
	
	public function __construct ($args = null) {
		$this->controllerName = !empty($args[Wee::$config['controller_var_name']]) ? 
				$args[Wee::$config['controller_var_name']] : Wee::$config['default_controller'];
		$this->actionName = !empty($args[Wee::$config['action_var_name']]) ? 
				$args[Wee::$config['action_var_name']] : Wee::$config['default_action'];
		$this->args = $args;
	}

	
	public function getControllerName () {
		return $this->controllerName;
	}
	
	
	public function getActionName () {
		return $this->actionName;
	}
	
	
	public function getArgs() {
		if (!is_array($this->args)) {
			$this->args = Ext_Array::objectToArray($this->args);
		}
		return $this->args;
	}
	
	
	public function get($name) {
		return isset($this->args[$name]) ? $this->args[$name] : null;	
	}
	
	
	public function set($name, $value = null) {
		if (is_object($name)) {
			$name = get_object_vars($name);	
		}
		if (is_array($name)) {
			$this->args = array_merge((array) $this->args, $name);
		} 
		else {
			$this->args[$name] = $value;
		}	
	}
	
	public function __get($name) {
		return $this->get($name);
	}
	
	public function __set($name, $value) {
		$this->set($name, $value);	
	}
	
	public function __isset($name) {
		return isset($this->args[$name]);
	}	
	
	
	public function getIntval($name) {
		return intval($this->get($name));	
	}

	
	public function getTrim($name) {
		return trim($this->get($name));	
	}
}