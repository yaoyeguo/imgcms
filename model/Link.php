<?php
/**
 * 友情链接
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-12-15 15:51
 * @version 1.0
 */
class Link_Model extends Model {
	
	public function __construct() {
		parent::__construct();	
		$this->setTable('#@_link', 'id');
	}
}