<?php
/**
 * 文章统计插件
 * @author 许仙 <QQ:1216560669 >
 * @time 2012-2-7 14:18
 * @version 1.0
 */
 
//    @articleNum()
Wee::$output->registerTag('articleNum', 'tag_article_num');
function tag_article_num($cid = 0) {
	$where = array();
	if ($cid) {
		$where['cid'] = $cid;	
	}
	$totalNum = load_model('Article')->getTotal($where);
	return $totalNum;
}

//  @todayNum()
Wee::$output->registerTag('todayNum', 'tag_today_num');
function tag_today_num($cid = 0) {
	$today = Ext_Date::today();
	$where = array("addtime > $today");
	if ($cid) {
		$where['cid'] = $cid;	
	}
	$todayNum = load_model('Article')->getTotal($where);
	return $todayNum;	
}