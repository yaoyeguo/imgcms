<?php
/**
 * 模板编译引擎
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-10-11 14:55
 * @version 1.0
 */
class Template_Compiler {
	
	
	private $_compileFile;
	
	
	private $_tplFile;
	
	
	public $_debug = false;
	
	
	private $_tags = array();
	
	
	private $_pattern = array(
		'/\{\$([\w\'\"\[\]\$\-\.\x7f-\xff]+)\}/',		//{$abc}
		'/\{@([^(]+\(.*?\))\}/',	// {@tag()}
		'/\{if\s*([^}]+)\}/',	//<!--{ if $a>$b }--> 
		'/\{else\}/',		//<!--{ else }-->
		'/\{elseif\s*([^}]+)\}/', //<!--{ elseif $a<$b }-->
		'/\{foreach\s+(.+?)\s+as\s+(\S+)\}/',  //<!--{ loop $list as $val }-->
		'/\{foreach\s+(.+?)\s+as\s+(\S+)\s*\=\>\s*(\S+)\s*\}/',  //<!--{ loop $list as $val }-->
		'/\{(\/if)\}/',		//<!--{ /if  }-->
		'/\{(\/foreach)\}/',		//<!--{ /foreach }-->	
		'/\{include\s*[\"\']?([^}\"\']+)[\"\']?\}/is', // <!--{ view ..}-->
		'/\{echo\s*([^}]+)\}/is', // <!--{ echo ..}-->
		'/\{#\s*([^}]+)\#}/is', // <!--{ # ..}-->
		'/\{eval\s*([^}]+)\}/is', // <!--{ eval ..}-->
		'/\{php\}/is',	//<!--{ php }-->
		'/\{\/php\}/is',	//<!--{ /php}-->
	);
		
	private $_replace = array(
		"<?php echo \$\\1;?>",
		"<?php echo @\\1;?>",
		"<?php if(\\1){?>",
		"<?php } else{?>",
		"<?php } elseif(\\1){?>",
		"<?php foreach(\\1 as \\2){?>",
		"<?php foreach(\\1 as \\2 => \\3){?>",
		"<?php }\n?>",
		"<?php }\n?>",
		"<?php \$this->display('\\1');?>",
		"<?php echo \\1;?>",
		"<?php echo \\1;?>",
		"<?php \\1;?>",
		"<?php \n",
		"\n?>",	
	);	
	
	
	public function __construct() {
		$this->_debug = Wee::$config['template_debug'];
		$this->registerTag(array(
			'url' => "url",
			'cutstr' => "Ext_String::cut",
			'idate' => "Ext_Date::format"
		));
	}
	
	
	public function registerTag($name, $callback = null) {
		if (is_array($name)) {
			$this->_tags = array_merge($this->_tags, $name);	
		} else {
			$this->_tags[$name] = $callback;	
		}
	}
	
	
	public function getCompileFile($tplFile, $compileFile) {
		$this->_tplFile = $tplFile;	
		$this->_compileFile = $compileFile;
		if ($this->_debug || !is_file($this->_compileFile) 
			|| filemtime($this->_compileFile) < filemtime($this->_tplFile)) {
			if (is_file($this->_tplFile)) {
				$tplCon = Ext_File::read($this->_tplFile);
				$tplCon = $this->compile($tplCon);
				if (!Ext_File::write($this->_compileFile, $tplCon)) {
					show_error($this->_tplFile . ' :编译失败');	
				}
			} else {
				show_error($this->_tplFile . ' :文件不存在');
			}	
		}
		return true;
	}
	
	
	public function compile($tplCon) {
		// <!--{}--> {}
		$pattern = '/<!--\{\s*([^\}]+)\s*\}-->/';
		$tplCon = preg_replace($pattern, '{$1}', $tplCon );
		// 
		$tplCon = preg_replace($this->_pattern, $this->_replace, $tplCon);
		
		/*
		// 
		$tmptags = implode('|', array_keys($this->_tags));
		$pattern = "/\{($tmptags)\b([^}]*)\}(.+?)\{\/\\1\}/se";
		$tplCon = preg_replace($pattern, "\$this->_parseLoopTag('$1', '$2', '$3')", $tplCon);
		
		// 
		$pattern = "/\{($tmptags)\b([^}]*)\}/se";
		$tplCon = preg_replace($pattern, "\$this->_parseTag('$1', '$2')", $tplCon);
		*/

		
		$pattern = '/(\<\?php(.+?)\?\>)/se';
		$tplCon = preg_replace($pattern, "\$this->_parseCode('$0')", $tplCon);
		
		
		$tplCon = "<?php\n /* compiled by (WeePHP) at (" . date('Y-m-d H:i:s') . ") */\n?>\n" . $tplCon;
		
			
		$pattern = '/\?\>\s*\<\?php/s';
	 	$tplCon = preg_replace($pattern, "\n", $tplCon);
		return $tplCon;
	}
	
	
	private function _parseLoopTag($name, $args, $content) {
		$argsArr = $this->_parseArgs($args);
		$str = "<?php \$_tmpList = "
				. $this->_tags[$name]
				. "("
				. $this->_getVarExport($argsArr)
				. "); foreach(\$_tmpList as \$key => \$val){?>"
				. stripslashes($content)
				. "<?php }?>";	
		return $str;	
	}
	
	
	private function _parseTag($name, $args) {
		$argsArr = $this->_parseArgs($args);
		$str = "<?php echo "
				. $this->_tags[$name]
				. "("
				. $this->_getVarExport($argsArr)
				. ")?>";	
		return $str;
	}
	
	
	private function _parseArgs($args) {
		$pattern = '/(\S+)\s*=\s*?([^\s\}]+)/se';
		$argsArr = array();
		if (preg_match_all($pattern, $args, $arr)) {
			$argsArr = array_combine($arr[1], $arr[2]);
		}
		return $argsArr;		
	}

	
	private function _getVarExport($var) {
		$str = '';
		if (is_array($var)) {
			$str = 'array(';
			foreach ($var as $key => $value) {
				$str .= "'$key' => $value,";
			}
			$str .= ')';
		} else {
			$str = $var;	
		}
		return $str;	
	}

	
	private function _parseCode($code) {
		
		$pattern = '/@([\w\-\>\:\x7f-\xff]+?)\(/se';
		$code = preg_replace($pattern, "\$this->_parseTags('$1')", $code);
		
		$pattern = '/((\$[\w\-\>\x7f-\xff]+)(\.[\w\-\>\.\"\'\[\]\$\x7f-\xff]+)+?)/se';
		$code = preg_replace($pattern, "\$this->_parseArray('$0')", $code);
		
		$pattern = '/((\$[\w\-\>\x7f-\xff]+)(\[[\w\-\>\.\"\'\[\]\$\x7f-\xff]+\])+?)/se';
		$code = preg_replace($pattern, "\$this->_addQuote('$0')", $code);
		
		$pattern = '/\$([\w\x7f-\xff]+)/se';
		$code = preg_replace($pattern, "\$this->_addVar('$1')", $code);
		return stripslashes($code);
	}
	
	
	private function _parseTags($var) {
		if (isset($this->_tags[$var])) {
			if (is_array($this->_tags[$var])) {
				return "load_model('".$this->_tags[$var][0]."')->".$this->_tags[$var][1]."(";
			} else {
				return $this->_tags[$var] . '(';
			}	
		} else {
			return $var . '(';	
		}
	}
	
	
	
	private function _parseArray($var) {
		$pattern = '/\.([\w\-\x7f-\xff]+)/s';
		return preg_replace($pattern, "['$1']", $var);	
	}
	
	
	private function _addQuote($var) {
		$pattern = '/\[([\w\-\.\x7f-\xff]+)\]/s';
		return preg_replace($pattern, "['$1']", $var);
	}

	
	private function _addVar($var) {
		if ('this' == $var) {
			return "$".$var;
		} else {
			return '$this->data[\''.$var.'\']';
		}
	}
}
