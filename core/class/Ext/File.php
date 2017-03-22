<?php 
/**
 * 文件管理扩展
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-12-27 17:17
 * @version 1.0
 */
class Ext_File {
	
	public static function read ($file) {
		$data = @file_get_contents ($file);
		return $data;
	}

	
	public static function write ($fileName, $data, $flags = 0) {
		$dirName = dirname($fileName);
		if (!is_dir($dirName)) {
			Ext_Dir::mkDirs($dirName);
		}
		$rs = file_put_contents($fileName, $data, $flags);
		return $rs;
	}
	
	public static function writeArray($file, $array) {
		$content = "<?php\nif (!defined('APP_PATH')) die('error');\nreturn " 
						. var_export($array, true) . ";";
		$rs = Ext_File::write($file, $content);	
		return $rs;
	}
	
	
	public static function formatSize ($sizeInput) {
		$sizeInput = doubleval ($sizeInput);
		if ($sizeInput >= 1024 * 1024 * 1024) {
			$sizeOutput = sprintf ("%01.2f", $sizeInput / (1024 * 1024 * 1024)) . " GB";
		}
		elseif ($sizeInput >= 1024 * 1024) {
			$sizeOutput = sprintf ("%01.2f", $sizeInput / (1024 * 1024)) . " MB";
		}
		elseif ($sizeInput >= 1024) {
			$sizeOutput = sprintf ("%01.2f", $sizeInput / 1024) . " KB";
		}
		else {
			$sizeOutput = $sizeInput . " Bytes";
		}
		return ($sizeOutput);
	}

	
	public static function getDir($file) {
		return pathinfo($file, PATHINFO_DIRNAME);
	}

	
	public static function getName ($file) {
		return pathinfo($file, PATHINFO_BASENAME);
	}

	
	public static function getExt ($file) {
		return strtolower(pathinfo($file, PATHINFO_EXTENSION));	
	}
}
