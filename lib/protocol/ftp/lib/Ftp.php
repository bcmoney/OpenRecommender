<?php
class Ftp
{
    const MODE_AUTO = 4;
    /**
     * Ftp mode 
     * 
     * @var int
     */
    protected $_mode = self::MODE_AUTO;

    /**
     * Ftp resource when conection established
     * @var resource
     */
	protected $_resource;					

    /**
     * ASCII file extensions
     * 
     * @var array
     */
    protected $_ascii = array(
        'htm', 'html', 'shtml', 'php', 'txt', 'py', 'cgi', 'js', 'cnf', 
        'css', 'forward', 'htaccess', 'map', 'pwd', 'pl', 'grp', 'ctl'
    );

    /**
     * __construct 
     * 
     * @param string $ftpServer 
     * @param string $username 
     * @param string $password 
     * @param int $port 
     * @return void
     */
    public function __construct($ftpServer, $username, $password, $port=21)
    {
        if (!extension_loaded('ftp')) {
            throw new Exception('Ftp extension is not enabled for PHP.');
        }
        try{
            $this->_connect($ftpServer, $port)
                 ->_login($username, $password);
        } catch(Exception $e) {
            throw $e;
        }
        $this->_init();
    }

    /**
     * Initialization
     */
    protected function _init(){}

    /**
     * _getRemoteFileExt 
     * 
     * @param string $remoteFile 
     * @return string|boolean
     */
    protected function _getRemoteFileExt($remoteFile)
    {
        for($i=strlen($remoteFile)-2; $i >= 0; $i--){
            if(substr($remoteFile, $i, 1) == '.'){
                 $ext = substr($remoteFile, ++$i);
                 return $ext;
            }
        }
        return false;
    }

    /**
     * Authenticates to the server
     * 
     * @access protected
     * @param string $username 
     * @param string $password 
     * @return boolean
     */
    protected function _login($username, $password)
    {
        $login = ftp_login($this->_resource, $username, $password);

        if(!$login){
            throw new Exception("Could not login as $username");
        }
        return true;
    }

    /**
     * Connects to ftp server
     *
     * @access protected
     * @param  int ftp port
     * @return Ftp 
     */
	protected function _connect($ftpServer, $port = 21)
    {
		if(is_null($this->_resource)){
			$ftp = ftp_connect($ftpServer, $port);//connect to ftp server
			if(!$ftp){
				throw new Exception("Unable to connect to ftp server");
			}else{
				$this->_resource = $ftp;
                //$this->setPassive(true);
			}	
            return $this;	
		}
	}

    /**
     * setMode 
     * 
     * @return Ftp
     */
    public function setMode($mode)
    {
        if ($mode != FTP_BINARY && $mode != FTP_ASCII && $mode != self::MODE_AUTO) {
            throw new InvalidArgumentException(
                        'Only FTP_ASCII, FTP_BINARY and Ftp::MODE_AUTO is valid'
                    );
        }
        $this->_mode = $mode;
        return $this;
    }

    /**
     * Turn passive mode on/off
     *
     * @access public
     * @param  boolean
     * @return Ftp
     */
	public function setPassive($value)
    {
		$result = ftp_pasv($this->_resource, $value);
		if(!$result){
			throw new Exception("Passive mode can not be enabled");
		}
		return $this;
	}
	
	/**
     * Gets present working directory
     *
     * @access public
     * @return string current directory
     */
	public function pwd()
    {
		return ftp_pwd($this->_resource);
	}

    /**
     * Gets file modified time on the server 
     * 
     * @param mixed $remoteFile 
     * @return void
     */
    public function mtime($remoteFile)
    {
        return ftp_mdtm($this->_resource, $remoteFile);
    }
	
	/**
     * Changes current directory
     *
     * @access public
     * @return boolean
     */
	public function chdir($dirname)
    {
		return @ftp_chdir($this->_resource, $dirname);
	}
	
	/**
     * Closes FTP stream
     *
     * @access public
     * @return boolean
     */
	public function close()
    {
		return ftp_close($this->_resource);
	}

    /**
     * Uploads a file to the server
     * 
     * @param string $remoteFile 
     * @param string $localFile 
     * @param int $mode 
     * @return boolean
     */
    public function put($remoteFile, $localFile)
    {
        if ($this->_mode === self::MODE_AUTO) {
            $info = pathinfo($localFile);
            if (in_array($info['extension'], $this->_ascii)) {
                $mode = FTP_ASCII;
            }else{
                $mode = FTP_BINARY;
            }
        }else{
            $mode = $this->_mode;
        }
        return @ftp_put($this->_resource, $remoteFile, $localFile, $mode);
    }

    /**
     * Downloads a file from the server 
     * 
     * @param string $localFile 
     * @param string $remoteFile 
     * @param int $mode 
     * @return boolean
     */
    public function get($localFile, $remoteFile, $mode=FTP_ASCII)
    {
        if ($this->_mode === self::MODE_AUTO) {
            $remExt = $this->_getRemoteFileExt($remoteFile);
            if (in_array($remExt, $this->_ascii)) {
                $mode = FTP_ASCII;
            }else{
                $mode = FTP_BINARY;
            }
        }else{
            $mode = $this->_mode;
        }
        return @ftp_get($this->_resource, $localFile, $remoteFile, $mode);
    }

    /**
     * Gets the remote file's size 
     * 
     * @param string $remoteFile 
     * @return int
     */
    public function size($remoteFile)
    {
        return ftp_size($this->_resource, $remoteFile);
    }

    /**
     * Mkdir 
     * 
     * @param string $dirname 
     * @return string
     */
    public function mkdir($dirname)
    {
        return @ftp_mkdir($this->_resource, $dirname);
    }

    /**
     * Removes directory from the server 
     * 
     * @param string $dirname 
     * @return boolean
     */
    public function rmdir($dirname)
    {
        return ftp_rmdir($this->_resource, $dirname);
    }

    /**
     * Removes a file from the server 
     * 
     * @param string $remoteFile 
     * @return boolean
     */
    public function delete($remoteFile)
    {
        return ftp_delete($this->_resource, $remoteFile);
    }

    /**
     * Returns the system type identifier of the FTP server
     * 
     * @return string
     */
    public function systype()
    {
        return ftp_systype($this->_resource);
    }

	/**
     * Gets all remote files
     *
     * @access public
     * @param  string directory to get its child
     * @return array list of remote files and directories
     */
	public function getRemoteFiles($dir=".")
    {
		 return ftp_nlist($this->_resource, $dir);
	}

    /**
     * Checks whether given path is file or directory 
     * 
     * @param string $dir 
     * @return boolean
     */
    protected function _isDir($dir) 
    {
        $pwd = $this->pwd();
        //check if something is a dir by trying to open it
        if ($this->chdir($dir)) {
            //if it does open we want to go back to our own dir, 
            //else we'll get an error on checking the next thing
            $this->chdir($pwd);
            return true;
        }else{
            return false;
        }       
    }
}
