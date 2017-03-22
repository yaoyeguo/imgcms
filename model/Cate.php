<?php
/**
 * Cate栏目管理模型
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class Cate_Model extends Model {
	
	public function __construct() {
		parent::__construct();
	}	
	
	
	public function getList() {
		$cacheKey = 'cate_list';
		if ($cacheData = $this->cache->getFromBox($cacheKey)) {
			return $cacheData;	
		}
		if ($cacheData = $this->cache->getFromFile($cacheKey)) {
			return $cacheData;	
		}
		$res = $this->db->table('#@_cate')->order('oid, cid ASC')->getAll('cid');
		foreach ($res as &$value) {
			$value = $this->getVo($value);
		}
		unset($value);
		$this->cache->setToFile($cacheKey, $res);
		$this->cache->setToBox($cacheKey, $res);
		return $res;	
	}
	
	public function getTree() {
		$cacheKey = 'cate_tree';
		if ($cacheData = $this->cache->getFromBox($cacheKey)) {
			return $cacheData;	
		}
		if ($cacheData = $this->cache->getFromFile($cacheKey)) {
			return $cacheData;	
		}
		$rs = $this->getList();
		$tree = array();
		foreach ($rs as $cid => $value) {
			if (0 == $value['pid']) {
				$value['son'] = array();
				$tree[$cid] = $value;	
			}
		}
		foreach ($rs as $cid => $value) {
			if (0 != $value['pid']) {
				$tree[$value['pid']]['son'][$cid] = $value; 
			}	
		}
		$this->cache->setToFile($cacheKey, $tree);
		$this->cache->setToBox($cacheKey, $tree);
		return $tree;
	}
	
	
	public function getVo($value) {
		$value['url'] = $this->getUrl($value);
		return $value;
	}
	
	
	public function get($cid) {
		$cateList = $this->getList();
		return isset($cateList[$cid]) ? $cateList[$cid] : null;	
	}
	
	
	public function del($cid) {
		$modArticle = load_model('Article');
		$where = array(
			'cid' => $cid,
			'status' => -1
		);
		$list = $modArticle->search($where);
		foreach ($list as $value) {
			$modArticle->del($value['id']);	
		}
		$this->db->table('#@_cate')->where("cid = $cid")->delete();
	}
	
	
	public function getArticelNum($cid = 0) {
		if ($cid) {
			$this->db->where(array('cid' => $cid, 'status' => 1));	
		}
		$res = $this->db->table('#@_article')->field("COUNT(*) AS num")->getOne();
		return $res['num'];
	}
	
	
	
	
	public function getUrl($cateInfo, $page = 0) {
		$cid = $cateInfo['cid'];
		if (Wee::$config['url_html_cate']) {
			$url = Wee::$config['web_url'] . $this->_getName($cateInfo, $page);
		} else {
			if ($page > 1) {
				$url = url('Cate', '', array('cid' => $cid, 'p' => $page));
			} else {
				$url = url('Cate', '', array('cid' => $cid));	
			}
		}
		return $url;
	}
	
	public function getPath($cateInfo, $page = 0) {
		return APP_PATH . $this->_getName($cateInfo, $page);
	}
	
	private function _getName($cateInfo, $page = 0) {
		$engName = $cateInfo['eng_name'];
		if (!$engName) {
			$engName = 'c' . $cateInfo['cid'];	
		}
		$name = "$engName/index";
		if (Wee::$config['url_dir_cate']) {
			$name = Wee::$config['url_dir_cate'] . '/' . $name;
		} 
		if ($page > 1) {
			$name .= "_$page";	
		}
		return $name . Wee::$config['url_suffix'];	
	}
	

	
	
	
	
	public function printTree($name = 'cid', $selectCid = 0, $onlyParent = false, $showDefId = true) {
		$tree = $this->getTree();
		$str = "<select name='$name' id='$name' class='select'>\n";
		if ($showDefId) {
			$str .= "<option value='0'>所有分类</option>"; 	
		}
		if ($onlyParent) { 
			foreach ($tree as $value) {
				$str .= "<option value='{$value['cid']}'";
				if ($selectCid == $value['cid']) {
					$str .= " selected";	
				}
				$str .= ">{$value['name']}</option>\n";			
			}
		} else { 
			foreach ($tree as $value) {
				$str .= "<option value='{$value['cid']}'";
				if ($selectCid == $value['cid']) {
					$str .= " selected";	
				}
				$str .= ">{$value['name']}</option>\n";
				if (!empty($value['son'])) {
					foreach ($value['son'] as $val) {
						$str .= "<option value='{$val['cid']}'";
						if ($selectCid == $val['cid']) {
							$str .= " selected";	
						}
						$str .= ">├{$val['name']}</option>\n";
					}
				}			
			}
		}
		$str .= '</select>';
		return $str;
	}
	
	public function getOne($cid) {
		$rs = $this->db->table('#@_cate')
					->where("cid = $cid")
					->getOne();
		return $rs;	
	}
	
	public function set($cid, $data) {
		$this->db->table('#@_cate')->where("cid = $cid")->update($data);
	}
	
	
	public function getPlace($cid) {
		$cateList = $this->getList();
		$cateTree = $this->getTree();
		if (!isset($cateList[$cid])) {
			return false;	
		}
		$cateInfo = $cateList[$cid];
		$cateInfo['sonId'] = array();
		$cateInfo['son'] = array();
		$cateInfo['parent'] = array();
		if (isset($cateTree[$cid]) && !empty($cateTree[$cid]['son'])) {
			$cateInfo['son'] = & $cateTree[$cid]['son'];
			$cateInfo['sonId'] = array_keys($cateInfo['son']);
		}
		if ($cateInfo['pid']) {
			$cateInfo['parent'] = & $cateTree[$cateInfo['pid']];	
		}
		return $cateInfo;
	}
}



