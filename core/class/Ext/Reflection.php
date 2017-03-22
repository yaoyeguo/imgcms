<?php
/**
 * 类反射
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-12-27 17:18
 * @version 1.0
 */
class Ext_Reflection {
	
	public function __construct($className) {
		$this->className = $className;
		$this->ReflectionClass = new ReflectionClass($this->className);	
	}
	
	
	public function parseDoc() {
		$classDoc = $this->getClass($this->ReflectionClass);
		return $classDoc;	
	}
	
	
	public function getClass($ReflectionClass) {
		$info = array();
		$info['name'] = $ReflectionClass->getName();
		$info['doc'] = $this->getDoc($ReflectionClass->getDocComment());
		$info['file'] = $ReflectionClass->getFileName();
		$info['line'] = $ReflectionClass->getStartLine() . '-' . $ReflectionClass->getEndLine();
		$info['modifier'] = $this->getModifier($ReflectionClass->getModifiers());
		
		$info['const'] = $ReflectionClass->getConstants();
		
		$info['vars'] = array();
		$Properties = $ReflectionClass->getProperties();
		foreach ($Properties as $ReflectionProperty) {
			$info['vars'][] = $this->getProperty($ReflectionProperty);
		}
		
		$info['method'] = array();
		$method = $ReflectionClass->getMethods();
		foreach ($method as $ReflectionMethod) {
			$temp = $this->getMethod($ReflectionMethod);
			if ($temp['isApi']) {
				$info['method'][$temp['name']] = $temp;
			}
		}
		//ksort($info['method']);
		return $info;
	}
	
	
	public function getProperty($ReflectionProperty) {
		$info['name'] = $ReflectionProperty->getName();
		$info['doc']  = $this->getDoc($ReflectionProperty->getDocComment());
		$info['modifier'] = $this->getModifier($ReflectionProperty->getModifiers());
		return $info;
	}
	
	
	public function getMethod($ReflectionMethod) {
		$info['name'] = $ReflectionMethod->getName();
		$info['doc']  = $this->getDoc($ReflectionMethod->getDocComment());
		$info['modifier'] = $this->getModifier($ReflectionMethod->getModifiers());
		$DeclaringClass = $ReflectionMethod->getDeclaringClass();
		$info['class'] = $DeclaringClass->getName();
		$info['file'] = $ReflectionMethod->getFileName();
		$info['line'] = $ReflectionMethod->getStartLine() . '-' . $ReflectionMethod->getEndLine();
		$info['isPublic'] = $ReflectionMethod->isPublic();
		$info['isStatic'] = $ReflectionMethod->isStatic();
		$info['isApi'] = false;
		if ($info['isPublic'] && false === strpos($info['name'], '__')) {
			$info['isApi'] = true;
		}	
		
		// 参数
		$info['args'] = array();
		$Parameters = $ReflectionMethod->getParameters();
		foreach ($Parameters as $ReflectionParameter) {
			$info['args'][] = $this->getArg($ReflectionParameter);	
		}
		$info['args'] = implode(", ", $info['args']);
		return $info;
	}
	
	
	public function getModifier($Modifiers) {
		return implode(" ", (Reflection::getModifierNames($Modifiers))); 
	}
	
	
	public function getDoc($DocComment) {
		if (trim($DocComment)) { 
			$DocComment = str_replace(array('/**', '**/', '*/', "*"), '', $DocComment);
			$DocComment = str_replace(array("\t", "  ",),  array("&nbsp;&nbsp;", "&nbsp;"), $DocComment);	
			$DocComment = nl2br(trim($DocComment));	
		} else {
			$DocComment = "暂无文档";	
		}
		return $DocComment;
	}
	
	
	public function getArg($ReflectionParameter) {
		$arg = '$' . $ReflectionParameter->getName();
		if ($ReflectionParameter->isArray()) {
			$arg = '(Array)' . $arg;	
		}
		if ($ReflectionParameter->isDefaultValueAvailable()) {
			$arg .= ' = ' . $ReflectionParameter->getDefaultValue();
		}
		return $arg;
	}
	
	
	public function export() {
		Wee::dump(Reflection::export($this->ReflectionClass, true));	
	}
}
