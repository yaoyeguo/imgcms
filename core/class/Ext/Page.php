<?php
/**
 * 数据分类管理
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-10-25 15:42
 * @version 1.0
 */
class Ext_Page {
	
	private $_url;
	
	
	private $_totalNum;
	
	
	private $_curPage = 1;
	
	
	private $_sizeNum = 10;
	
	
	private $_lineNum = 9;
	
	
	public static $prevText = '上一页';
	
	
	public static $nextText = '下一页';
	
	
	private $_start;
	
	
	private $_end;
	
	
	private $_totalPage;
	
	
	private $_limit;
	
	
	private $_html;
	
	
	private $_makeUrlFun = null;
	
	
	private $_callArgs = array();
	
	
	
	public function __construct($url, $totalNum, $curPage = 1, $sizeNum = 10, $lineNum = 9) {
		$this->_url = $url;
		if (is_array($url)) {
			$this->_makeUrlFun = array_splice($url, 0, 2);
			$this->_callArgs = $url;
		} else {
			$this->_url = str_replace('%40', '@', $this->_url);	
		}
		$this->_totalNum = $totalNum;
		$this->_curPage = $curPage;
		$this->_sizeNum = $sizeNum;
		$this->_lineNum = $lineNum;
		if ($this->_totalNum < 1) {
			$this->_totalNum = 1;
		}
		if ($this->_sizeNum < 1) {
			$this->_sizeNum = 1;	
		}
		if ($this->_curPage < 1) {
			$this->_curPage = 1;	
		}
		$this->make();
	}
	
	
	public function make() {	
		$this->_totalPage = ceil($this->_totalNum / $this->_sizeNum);	
		if ($this->_curPage > $this->_totalPage) {
			$this->_curPage = $this->_totalPage;
		}				
		$this->_start = ($this->_curPage - 1) * $this->_sizeNum;	
		$tmpArr = array();	
		$prevPage = $this->_curPage - 1;
		if ($prevPage < 1) {
			$tmpArr[] = "<span>".self::$prevText."</span>";
		} else {
			$tmpArr[] = $this->_makeUrl($prevPage, self::$prevText);
		}
		$k = floor( $this->_lineNum / 2 );
		$i = $this->_curPage - $k;
		$j = $this->_curPage + $k;
		if ($i < 1) {
			$i = 1;
			if ($i + $this->_lineNum > $this->_totalPage) {
				$j = $this->_totalPage;
			} else {
				$j = $i + $this->_lineNum;	
			}
		} 
		if ($j > $this->_totalPage) {
			$j = $this->_totalPage;
			if ($j - $this->_lineNum < 1) {
				$i = 1;
			} else {
				$i = $j - $this->_lineNum;
			}
		}
		if ($i > 1) {
			$tmpArr[] = $this->_makeUrl(1, 1) . ' ... ';
		}
		for (; $i <= $j; $i++) {
			if ($i == $this->_curPage) {
				$tmpArr[] = "<strong>$i</strong>\n";
			} else {
				$tmpArr[] = $this->_makeUrl($i, $i);
			}
		}	
		if ($j < $this->_totalPage) {
			$tmpArr[] =  ' ... ' . $this->_makeUrl($this->_totalPage, $this->_totalPage);	
		}
		$nextPage = $this->_curPage + 1;
		if ($nextPage > $this->_totalPage) {
			$tmpArr[] = "<span>".self::$nextText."</span>";	
		} else {
			$tmpArr[] = $this->_makeUrl($nextPage, self::$nextText);	
		}
		$this->_limit = $this->_start . ', ' . $this->_sizeNum;
		$this->_end = $this->_start + $this->_sizeNum;
		if ($this->_end > $this->_totalNum) {
			$this->_end = $this->_totalNum;	
		}
		$this->_html = implode("\n", $tmpArr);
	}
	
	
	private function _makeUrl($p, $text) {
		if ($this->_makeUrlFun) {
			$args = $this->_callArgs;
			$args[] = $p;
			$url = call_user_func_array($this->_makeUrlFun, $args);
		} else {
			$url = str_replace('@', $p, $this->_url);
		}
		$str = "<a href=\"$url\">$text</a>";
		return $str;
	}
	
	
	public function totalPage() {
		return $this->_totalPage;	
	}
	
	
	public function html() {
		return $this->_html;	
	}
	
	
	public function limit() {
		return $this->_limit;	
	}
	
	public function start() {
		return $this->_start;	
	}
	
	public function end() {
		return $this->_end;	
	}
}