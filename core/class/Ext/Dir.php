<?php 
/**
 * 目录管理扩展
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-12-27 17:17
 * @version 1.0
 */
class Ext_Dir extends DirectoryIterator {
	const TYPE_DIR = 'DIR';
	const TYPE_FILE = 'FILE';
	const TYPE_ALL = 'ALL';
	
	
	public static function mkDirs($directory, $mode = 0777) {
		$rs = mkdir($directory, $mode, true);
		if ($rs) {
			$rs = @chmod($directory, $mode);	
		}
		return $rs;
	}
	
	
	public static function delDir($directory, $subdir = true) {
		if (is_dir($directory) == false) {
			//exit("The Directory Is Not Exist!");
			return false;
		}
		$handle = opendir($directory);
		while (($file = readdir($handle)) !== false) {
			if ($file != "." && $file != "..") {
				is_dir("$directory/$file") ? self::delDir("$directory/$file") : unlink("$directory/$file");
			}
		}
		if (readdir($handle) == false) {
			closedir($handle);
			rmdir($directory);
		}
	}

	
	public static function del($directory, $subdir = true){
		if (is_dir($directory) == false) {
			//exit("The Directory Is Not Exist!");
			return false;
		}
		$handle = opendir($directory);
		while (($file = readdir($handle)) !== false) {
			if ($file != "." && $file != "..") {
				if (is_file("$directory/$file")) { 
					unlink("$directory/$file");
				} elseif (is_dir("$directory/$file") && true == $subdir) { 
					self::deldir("$directory/$file", $subdir);
				}
			}
		}
		closedir($handle);
	}

	
	public static function copyDir($source, $destination) {
		if (is_dir($source) == false) {
			exit("The Source Directory Is Not Exist!");
		}
		if (is_dir($destination) == false) {
			Ext_Dir::mkDirs($destination, 0700);
		}
		$handle = opendir($source);
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && $file != ".svn") {
				is_dir("$source/$file") ? 
					self::copyDir("$source/$file", "$destination/$file") : 
					copy("$source/$file", "$destination/$file");
			}
		}
		closedir($handle);
	}
	
	
	public static function getDirList($source, $type = 'ALL', $no = array(), $ext = array()) {
		if (is_dir($source) == false) {
			return array();
		}
		$handle = opendir($source);
		$dirlist = array();
		array_push($no, '.');
		array_push($no, '..');
		while (false !== ($file = readdir($handle))) {
			if (!in_array($file, $no)) {
				if ($type == 'DIR' && !is_dir($source . '/' . $file)) {
					continue;
				} if ($type == 'FILE' && !is_file($source . '/' . $file)) {
					continue;	
				}
				if (!empty($ext)) {
					if (is_array($ext)) {
						$rs = in_array(end(explode('.',$file)), $ext);
					} else {
						$rs = in_str(end(explode('.', $file)), $ext);	
					} if (!$rs) {
						continue;
					}	
				}
				$dirlist[] = $file;
			}
		}
		closedir($handle);
		return $dirlist;		
	}
	
	
	
	public static function getDirTree($source, $ext = array()) {
		$list = self::getDirList($source, $type = 'ALL', $no = array('.', '..', '.svn'), $ext);
		$tree = array();
		foreach ($list as $value) {
			if (is_dir($source . '/' . $value)) {
				$tree[$value] = self::getDirTree($source . '/' . $value);	
			} else {
				$tree[] = $value; 	
			}
		}
		return $tree;
	}
	
	
}
