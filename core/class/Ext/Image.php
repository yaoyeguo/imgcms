<?php
/**
 * 图片处理扩展
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-12-27 17:17
 * @version 1.0
 */
class Ext_Image {
	
	public static $doGif = true;

	
	public static $cutZoom = false;


	
	private static $_imageExts = array(1 => 'gif', 2 => 'jpg', 3 => 'png');
	
	
	public static function vcode($text = 'TEST', $width = 60, $height = 20) {
		$num = strlen ($text);
	   	$im = imagecreatetruecolor($width, $height);
    	$back_color  = imagecolorallocate($im, 250, 250, 250);
    	$boder_color = imagecolorallocate($im, 200, 200, 200);	
		imagefilledrectangle($im, 0, 0, $width, $height, $back_color);
    	imagerectangle($im, 0, 0, $width - 1, $height - 1, $boder_color);
		for ($i = 0; $i < strlen($text); $i++) {
			$x  = floor(($width - 5) / $num) * $i + 5;
			$y  = mt_rand(0, $height - 15);
			$text_color = imagecolorallocate($im, mt_rand(0,255), mt_rand(0,128), mt_rand(0,255));
			imagechar($im, 5, $x, $y, $text{$i}, $text_color);
		}
		for ($i = 0; $i < $width; $i++) {
			$dis_color = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
			imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $dis_color);
		}
		@ob_end_clean();
		header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
		header("Content-type: image/png");
		imagepng($im);
		imagedestroy($im);		
	}
	
	
	public static function cut($oldFile, $newFile, $newW, $newH, $cutType = 0, $pct = 80) {
		$info 	= self::getInfo($oldFile);
        $oldW  = $info['width'];
        $oldH  = $info['height'];
        $type   = $info['type'];
        $ext    = $info['ext'];  
        unset($info);
        $justCopy = false;
        if ('gif' == $ext && false == self::$doGif) {
        	$justCopy = true;	
        }
        if ($oldW < $newW && $oldH < $newH && false == self::$cutZoom) {
        	$justCopy = true;
        }
        if (true == $justCopy) {
            if (!is_dir(dirname($newFile))){
        		Ext_Dir::mkDirs(dirname($newFile), 0777);
        	}
        	$flag = @copy($oldFile, $newFile);
        	return $flag;		
        }
        
        if (0 == $cutType) { 
        	$scale = min($newW / $oldW, $newH / $oldH); // 计算缩放比例
        	$width  = (int)($oldW * $scale); // 缩略图尺寸
        	$height = (int)($oldH * $scale);
        	$startW = $startH = 0;
        	$endW = $oldW;
        	$endH = $oldH;
        }
        
        elseif (1 == $cutType) { 
    		$scale1 = round($newW / $newH, 2);
    		$scale2 = round($oldW / $oldH, 2);
    		if ($scale1 > $scale2) {
    			$endH = round($oldW / $scale1,2);
    			$startH = ($oldH - $endH)/2;
    			$startW  = 0;
    			$endW    = $oldW;
    		} 
    		else {
    			$endW  = round($oldH * $scale1, 2);
    			$startW = ($oldW - $endW) / 2;
    			$startH = 0;
    			$endH   = $oldH;
    		}
    		$width = $newW;
        	$height= $newH; 
    	} elseif (2 == $cutType) { // left top 裁剪	
    		$scale1 = round($newW / $newH,2);
        	$scale2 = round($oldW / $oldH,2);
        	if ($scale1 > $scale2) {
        		$endH = round($oldW / $scale1, 2);
        		$endW = $oldW;
        	} 
        	else {
        		$endW = round($oldH * $scale1, 2);
        		$endH = $oldH;
        	}
        	$startW = 0;
        	$startH = 0;
        	$width = $newW;
        	$height= $newH; 
    	}
    	else {
    		self::showError($cutType.' :裁剪类型错误');	
    	}
        $oldIm = self::createImFrom($oldFile, $type);
        $newIm = self::createIm($width, $height, $type);
        if ($type == 'jpeg') imageinterlace($newIm, 1); 
        self::copyIm($newIm, $oldIm, $startW, $startH, $endW, $endH, $width, $height); 
        if ('' == pathinfo($newFile, PATHINFO_EXTENSION)) {
        	$newFile .= '.'.$ext;	
        }
        $flag = self::saveIm($newIm, $newFile, $type, $pct);
        imagedestroy($oldIm);
        imagedestroy($newIm);
        return $flag;
	}
	
	
	public static function text($oldFile, $newFile, $text, $bgColor = '000000', $textColor = 'ffffff', $pct = 80, $bgHeight = 20, $textSize = 10) {
		$oldInfo 	= self::getInfo($oldFile);
		$oldImg 	= self::createImFrom($oldFile, $oldInfo['type']);
		$width 		= $oldInfo['width'];
		$height 	= $oldInfo['height'];
		$ext   		= $oldInfo['ext'];		
        if ('gif' == $oldInfo['ext'] && false == self::$doGif){
        	return true;	
        }
        $newImg = imagecreatetruecolor($width, $height + $bgHeight);
        $bgColor = self::rgbColor($bgColor);
        $bgColor =  imagecolorallocate($newImg, $bgColor['r'], $bgColor['g'], $bgColor['b']);
        $textColor = self::rgbColor($textColor);
        $textColor = imagecolorallocate($newImg, $textColor['r'], $textColor['g'], $textColor['b']);
        imagecopymerge($newImg, $oldImg, 0, 0, 0, 0, $width, $height, 100);
        imagefilledrectangle($newImg, 0, $height, $width, $height + $bgHeight, $bgColor);
        $font = CORE_PATH . 'misc/font/simsun.ttc';
        imagettftext($newImg, $textSize, 0, 5, $height + $bgHeight - 5, $textColor, $font, $text);
		$flag = self::saveIm($newImg, $newFile, $oldInfo['type'], $pct);
		imagedestroy($oldImg);
  		imagedestroy($newImg);
  		return $flag;		
	}
	
	
	public static function water($oldFile, $newFile, $waterFile, $waterPos = 1, $waterPct = 80, $pct = 80) {
		$oldInfo 	= self::getInfo($oldFile);
		$oldW 		= $oldInfo['width'];
		$oldH 		= $oldInfo['height'];
		$oldImg 	= self::createImFrom($oldFile, $oldInfo['type']);
		$waterInfo  = self::getInfo($waterFile);
		$waterW 	= $waterInfo['width'];
		$waterH 	= $waterInfo['height'];
		$waterImg 	= self::createImFrom($waterFile, $waterInfo['type']);
		$ext   		= $oldInfo['ext'];		
        if ('gif' == $oldInfo['ext'] && false == self::$doGif){
        	return true;	
        }		
		
		$waterW > $oldW && $waterW = $oldW;
		$waterH > $oldH && $waterH = $oldH;
		
					
		switch($waterPos) {
			case 0: 
            	$pos_x = rand(0, ($oldW - $waterW)); 
            	$pos_y = rand(0, ($oldH - $waterH)); 
            	break; 
        	case 1: 
            	$pos_x = 0; 
            	$pos_y = 0; 
            	break; 
        	case 2: 
            	$pos_x = ($oldW - $waterW) / 2; 
            	$pos_y = 0; 
            	break; 
        	case 3: 
            	$pos_x = $oldW - $waterW; 
            	$pos_y = 0; 
            	break; 
        	case 4: 
            	$pos_x = 0; 
            	$pos_y = ($oldH - $waterH) / 2; 
            	break; 
        	case 5: 
            	$pos_x = ($oldW - $waterW) / 2; 
            	$pos_y = ($oldH - $waterH) / 2; 
            	break; 
        	case 6: 
            	$pos_x = $oldW - $waterW; 
            	$pos_y = ($oldH - $waterH) / 2; 
            	break; 
        	case 7: 
            	$pos_x = 0; 
            	$pos_y = $oldH - $waterH; 
            	break; 
        	case 8: 
            	$pos_x = ($oldW - $waterW) / 2; 
            	$pos_y = $oldH - $waterH; 
            	break; 
        	case 9: 
            	$pos_x = $oldW - $waterW; 
            	$pos_y = $oldH - $waterH; 
            	break; 
        	default:  
            	$pos_x = rand(0,($oldW - $waterW)); 
            	$pos_y = rand(0,($oldH - $waterH)); 
            	break;   
		}
	    
		imagealphablending($oldImg, true); 
		
		imagecopymerge($oldImg, $waterImg, $pos_x, $pos_y, 0, 0, $waterW, $waterH, $waterPct);
        $flag = self::saveIm($oldImg, $newFile, $oldInfo['type'], $pct);
  		imagedestroy($oldImg);
  		imagedestroy($waterImg);
  		return $flag;	
	} 
	
	
	public static function saveIm($im, $file, $type = 'jpeg', $pct = 80) {
		if (!is_dir(dirname($file))) {
			Ext_Dir::mkDirs(dirname($file), 0777);	
		}
        $fun = 'image'.$type;
        if ('jpeg' == $type) {
        	$flag = @$fun($im, $file, $pct);
        } 
        else {
        	$flag = @$fun($im, $file);
        }
        return $flag;		
	}
	
	
	public static function createImFrom($file, $type='jpeg') {
        $fun  = 'imagecreatefrom'.$type;
        $im   = $fun($file);
        return $im; 
	}
	
	
	public static function createIm($width, $height, $type = 'jpeg') {
        if ('gif' != $type && function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor($width, $height); 
        } 
        else {
            $im = imagecreate($width, $height); 
        }	
        return $im;
	}
	
	
	public static function copyIm($newIm, $oldIm, $startW, $startH, $endW, $endH, $width, $height) {
		if (function_exists("imagecopyresampled")) {
        	$fun = "imagecopyresampled";
        } 
        else {
        	$fun = "imagecopyresized";
        }
        $flag = $fun($newIm, $oldIm, 0, 0, $startW, $startH, $width, $height, $endW, $endH);
        return $flag; 		
	}
	
	
	public static function getInfo($file) {
		$info = @getimagesize($file);
		if (empty($info)) self::showError($file.' :这不是一张可用的图片');
		$info['type']	= substr($info['mime'], 6);
		if (isset(self::$_imageExts[$info[2]])) {
			$info['ext'] 	= self::$_imageExts[$info[2]];
		}
		else {
			self::showError($file.' :不支持 '.$info['mime'].' 类型文件');	
		}
		$info['width'] 	= $info[0];
		$info['height'] = $info[1];	
		return $info;		
	}
	
	
	public static function crop($oldFile, $newFile, $x, $y, $w, $h, $targW = 100, $targH = 100, $quality = 90) {
		$oldIm = self::createImFrom($oldFile);
		$newIm = self::createIm($targW, $targH);
		$rs = self::copyIm($newIm, $oldIm, $x, $y, $w, $h, $targW, $targH);
		self::saveIm($newIm, $newFile, 'jpeg', $quality);
	}
	
	
	public static function showError($errorMsg) {
		show_error($errorMsg);
	}
	
	
	public static function rgbColor($color) {
		if ('#' == $color{0}) {
			$color = substr($color, 1);
		} 
		$color = unpack('Cr/Cg/Cb', pack('H*', $color));
		return $color;	
	}
}