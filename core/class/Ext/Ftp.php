<?php
/**
 * FTP管理
 * @author 许仙 <QQ:1216560669 >
 * @time 2011-12-27 17:17
 * @version 1.0
 */
class Ext_Ftp {
	private $_conn;
	private $_host;
	private $_port;
	private $_user;
	private $_pwd;
	public $timeout = 10;
	public $baseDir = '/';
	public $pasvMode = true;
	
	public function __construct($host, $port, $user, $pwd) {
		$this->_host = $host;
		$this->_port = $port;
		$this->_user = $user;
		$this->_pwd = $pwd;	
	}

	
	public function connect() {
		if (is_resource($this->_conn)) {
			return;	
		}
    	$this->_conn = @ftp_connect($this->_host, $this->_port, $this->timeout);
    	if (!$this->_conn) {
    		show_error('FTP服务器连接失败! 请检查服务器地址和端口!');
    	}
    	$rs =@ftp_login($this->_conn, $this->_user, $this->_pwd);
    	if (!$rs) {
    		show_error('FTP登录错误! 请检查用户名和密码!');
    	}
    	if ($this->pasvMode) {
    		$this->pasv(true);	
    	}
    	if (!$this->chdir($this->baseDir)) {
    		show_error($this->baseDir.': 切换到FTP当前目录失败! 请检查目录是否存在!');
    	}
    }
    
    
    public function chdir($dir) {
    	return @ftp_chdir($this->_conn, $dir);
    }
    
    
    public function size($file) {
    	return ftp_size($this->_conn, $file);	
    }
    
    
    public function isFile($file){
    	$buff = @ftp_mdtm($this->_conn, $file);
    	return (-1 != $buff);
    }
    
    
    public function pasv($mode=true) {
    	return ftp_pasv($this->_conn, true);
    }
    
    
    public function put($localFile, $remoteFile, $bMode = true) {
    	if ($bMode) {	
    		$mode = FTP_BINARY; 
    	} else {	
    		$mode = FTP_ASCII;  
    	}
    	if ($this->chdir(dirname($remoteFile))) {
    		$this->chdir($this->baseDir);	
    	} else {
    		$this->mkdirs(dirname($remoteFile));
    	}
    	$rs = ftp_put($this->_conn, $remoteFile, $localFile, $mode);
    	return $rs;
    }
    
    
    public function get($remoteFile, $localFile, $bMode = true) {
    	if ($bMode) {  
    		$mode = FTP_BINARY; 
    	} else {
    		$mode = FTP_ASCII;  
    	}
    	$rs = ftp_get($this->_conn, $localFile, $remoteFile, $mode);
		return $rs;
    }
    
    
    public function mkdirs($dir) {
    	$dir  = str_replace("\\", '/', $dir);
    	$dirs = explode('/', $dir);
    	$total = count($dirs); 
    	foreach ($dirs as $val) {
    		if ($val == '.') {
    			continue;	
    		}
    		if (!$this->chdir($val)) { 
    			if(!$this->mkdir($val)) {
    				show_error($val.': 创建失败!');
    				return false;
    			}
    			$this->chdir($val);
    		}	
    	}
    	$this->chdir($this->baseDir);
    }
    
    
    public function mkdir($dir) {
    	return ftp_mkdir($this->_conn, $dir);	
    }
    
    
    public function unlink($file) {
    	return @ftp_delete($this->_conn, $file);
    }
    
    
    public function rename($oldName, $newName) {
    	return ftp_rename($this->_conn, $oldName, $newName);
    }
    
    
	public function rmdir($dir) {
		return ftp_rmdir($this->_conn, $dir);	
	}
	
	
	public function rmdirs($dir, $delSelf = true) {
		if (!$this->rmdir($dir) && !$this->unlink($dir)) {
			$files = $this->nlist($dir);
			if (empty($files)) {
				return true;
			}
			foreach ($files as $file) {
				$file = basename($file);
				$this->rmdirs($dir . '/' . $file);
			}
			if($delSelf){
				$this->rmdirs($dir);
			}
		}
		return true;
	}
	
	
	public function nlist($dir) {
		return ftp_nlist($this->_conn, $dir);	
	}   
}