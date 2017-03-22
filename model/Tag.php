<?php
/**
 * 标签管理模型
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class Tag_Model extends Model {	
	
	public function __construct() {
		parent::__construct();	
	}
	
	
	public function image($src, $width = 0, $height = 0, $type = 1) {
		if (Wee::$config['upload_dispatch']) {		
			$args = "$src,$width,$height,$type";
			$crc = substr(md5(Wee::$config['encrypt_key'].$args), 10, 6);
			$url = Wee::$config['web_url'] . 'attach.php?r=' . 
				Ext_String::base64UrlEncode("$args,$crc");
		} else {	
			$url = load_model('Attach')->makeImage($src, $width, $height, $type);
		}
		return $url;
	}
	
	
	public function article($cid, $star, $num = 10, $order = 'id') {
		$cacheKey = "article_{$cid}_{$star}_{$num}_{$order}";
		if ($cacheData = $this->cache->getFromBox($cacheKey)) {
			return $cacheData;
		} 
		if ($cid) {
			if (false !== strpos($cid, ',')) {
				$where['cid'] = explode(',', $cid);
			} else {
				$modCate = load_model('Cate');
				$cate = $modCate->getPlace($cid);
				if ($cate['sonId']) {
					$where['cid'] = $cate['sonId'];
					array_unshift($where['cid'], $cid);
				} else {
					$where['cid'] = $cid;
				}	
			}
		}
		if ($star) {
			if (false !== strpos($star, ',')) {
				$where['star'] = explode(',', $star);
			} else {
				$where['star'] = $star;	
			}
		}
		if ('week' == $order) {
			$where[] = "addtime > " . time() - 7 * 24 * 3600;
			$order = 'hits';	
		}
		$articleMod = load_model('Article');	
		$articleList = $articleMod->search($where, $num, $order, 'DESC');
		$this->cache->setToBox($cacheKey, $articleList);
		return $articleList;		
	}
	
	
	public function relevant($id, $limit = 5) {
		$modArticle = load_model('Article');
		$articleInfo = $modArticle->get($id);
		$list = array();
		if ($articleInfo['tagArr']) {
			$res = $this->db->table('#@_tags')
					->where(array('tag' => $articleInfo['tagArr']))
					->limit($limit)
					->getAll();
			$ids = Ext_Array::cols($res, 'article_id');
			$where = array('id' => $ids);
			$list = $modArticle->search($where, $limit);
		}
		return $list;
	}
	
	
	public function tags($num = 20) {
		$cacheKey = "tags_$num";
		if ($cacheData = $this->cache->getFromFile($cacheKey)) {
			return $cacheData;	
		}
		$modArticle = load_model('Article');
		$tags = $modArticle->getTags($num);
		$this->cache->setToFile($cacheKey, $tags);
		return $tags;
	}
	
	
	public function searchurl($keyword) {
		return url('Search', '', array('keyword' => $keyword));	
	}
	
	
	public function tagsurl($tag) {
		return url('Tags', '', array('tag' => $tag));	
	}
	
	
	public function rssurl() {
		if (Wee::$config['url_html_maps']) {
			return Wee::$config['web_url'] . Wee::$config['url_dir_maps'] . '/rss.xml';
		} else {
			return url('Maps', 'rss');	
		}
	}
	
	
	public function sitemapurl() {
		if (Wee::$config['url_html_maps']) {
			return Wee::$config['web_url'] . Wee::$config['url_dir_maps'] . '/sitemap.xml';
		} else {
			return url('Maps');	
		}
	}
	
	
	public function links($type = 0, $num = 100) {
		$cacheKey = "links_{$type}_{$num}";
		if ($cacheData = $this->cache->getFromFile($cacheKey)) {
			return $cacheData;	
		}
		$where = array();
		if ($type) {
			$where['type'] = $type; 	
		}
		$linkList = load_model('Link')->getList($where, $num, 'oid ASC');
		$this->cache->setToFile($cacheKey, $linkList);
		return $linkList;
	}
	
	
	public function adsense($title) {
	    $cKey = "Adsense_$title";
		$cData = $this->cache->getFromFile($cKey);
		
		 
	    if($cKey=='Adsense_banner' && Wee::$config['led_ok']==1){
		$ccc = Wee::$config['led_values'];
		$pp = Wee::$config['led_link'];
		$a=<<<ABC
		<table borderColor=#006600 height=90 cellSpacing=0 width=98% align=center background=./images/default/leddd.png border=0 style="margin: 0px 0px 0px 0px">
<tr><TD vAlign="middle" noWrap align="center" style="padding-top:5px;"><div align="center">
<marquee scrollamount=5 FONT style="FONT-SIZE: 32pt; FILTER: glow(color=red); WIDTH: 100%; COLOR: #FFFF00; FONT-FAMILY: 黑体" onmouseover=stop() onmouseout=start()>
<B><a  style='color:#FFFF00' href="$pp" target="_blank">$ccc</a></B>
</marquee></div>
</td></tr>
</table>

ABC;
		return $a;

		  
		}
		
		
		if ($cData) {
			return $cData;	
		}
		$info = load_model('Adsense')->getByTitle($title);
		if ($info) {
			$this->cache->setToFile($cKey, $info['content']);
			return $info['content'];
		} else {
			return "<!-- $title:广告标识不存在 -->";	
		}
	
	}
}


