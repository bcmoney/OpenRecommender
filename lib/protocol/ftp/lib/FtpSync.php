<?php
require_once 'Ftp.php';
require_once 'Logger.php';
require_once 'myReflection.php';

//Strategies
require_once 'Strategy/CleanUp.php';
require_once 'Strategy/Size.php';
require_once 'Strategy/MTime.php';

/**
 * FtpSync 
 * 
 * @version 3.1 
 * @copyright Copyright (C) 2009-2011.
 * @author Amin Saeedi <amin.w3dev@gmail.com> 
 * @license GPL License
 */
class FtpSync extends Ftp
{
    const SYNC_BY_SIZE = 'Strategy_Size';
    const SYNC_BY_MTIME = 'Strategy_MTime';
    const SYNC_BY_CLEANUP = 'Strategy_CleanUp';

    /**
     * Just reports the differences in two hosts 
     * 
     * @var boolean
     */
    protected $_reportOnly = false;

    /**
     * Logging status
     * 
     * @var mixed
     */
    protected $_logEnabled = false;

    /**
     * Sync strategies 
     * 
     * @var array
     */
    protected $_syncStrategies = array();

    /**
     * Blacklist 
     * 
     * @var array
     */
    protected $_blackList = array();

    /**
     * Remote files 
     * 
     * @var array
     */
    protected $_remoteFiles = array();

    /**
     * Local files 
     * 
     * @var array
     */
    protected $_localFiles = array();

    /**
     * Logger object 
     * 
     * @var Logger
     */
    protected $_logger;

    protected function _init()
    {
        $this->_syncStrategies = array(
           self::SYNC_BY_MTIME   => new Strategy_MTime($this),
           self::SYNC_BY_SIZE    => new Strategy_Size($this),
           self::SYNC_BY_CLEANUP => new Strategy_CleanUp($this),
        );
    }

    /**
     * Checks whether a file is in blacklist or not 
     * 
     * @param string $filepath 
     * @return boolean
     */
    protected function _isInBlackList($filepath)
    {
        foreach ($this->_blackList as $item) {
            if (preg_match('/'.$item.'/', $filepath)) {
                return true;
            }
        }
        return false;
    }

    /**
     * getSyncStrategies 
     * 
     * @return Strategy_Abstract
     */
    public function getSyncStrategies()
    {
        return $this->_syncStrategies;
    }

    /**
     * Sets current strategy and returns strategy object 
     * 
     * @param string $strategy e.g. Strategy_Size
     * @return Strategy_Abstract
     */
    public function useStrategy($strategy)
    {
        if (!array_key_exists($strategy, $this->_syncStrategies)) {
            throw new InvalidArgumentException(
                'Invalid strategy: '.$strategy
            );
        }
        return $this->_syncStrategies[$strategy];
    }

    /**
     * Enables logging 
     * 
     * @return FtpSync
     */
    public function enableLog()
    {
        $this->_logEnabled = true;
        return $this;
    }

    /**
     * Returns the logger instance
     * 
     * @return Logger
     */
    public function getLogger()
    {
        if (is_null($this->_logger)) {
            $this->_logger = new Logger($this->_logEnabled);
        }
        return $this->_logger;
    }

    /**
     * Adds file to the blacklist 
     * 
     * @param mixed $path 
     * @return void
     */
    public function ignore($path)
    {
        $this->_blackList[] = $path;
        return $this;
    }

    /**
     * Sets the report mode 
     * 
     * @param boolean 
     * @return FtpSync
     */
	public function reportOnly($value=true)
    {
		$this->_reportOnly = $value;
        return $this;
	}

    /**
     * isReportOnly 
     * 
     * @return boolean
     */
    public function isReportOnly()
    {
        return $this->_reportOnly;
    }

    /**
     * Traverses remote directory recursively
     *
     * @access public
     * @param string remote directory that should be traversed
     */
    public function getRemoteFilesRecursive($dir)
    {
        $remoteFiles = $this->getRemoteFiles($dir);
        if (is_array($remoteFiles)) {
            foreach($remoteFiles as $f) {
                if(!$this->_isInBlackList($f)) {
                    if($this->_isDir($f)) {
                        $this->getRemoteFilesRecursive($f);
                    }else {
                        $this->_remoteFiles[] = $f;
                    }
                }
            }
        }
        return $this->_remoteFiles; 
    }

	/**
     * Traverses local directory recursively
     *
     * @access public
     * @param string local directory that should be traversed
     */
	public function getLocalFilesRecursive($dir)
    {
        //$handle = new RecursiveIteratorIterator(
                //new RecursiveDirectoryIterator($dir)
                //);
        $handle = new DirectoryIterator($dir);
		foreach($handle as $f) {
            if(!$f->isDot() && !$this->_isInBlackList($f->getPathname())) {
				if($f->isDir()) {
					$this->getLocalFilesRecursive($f->getPathname());
				}else {
					$this->_localFiles[] = $f->getPathname();
				}
            }
		}
        return $this->_localFiles;
	}
}
