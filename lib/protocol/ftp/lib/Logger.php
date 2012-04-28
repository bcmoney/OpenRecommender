<?php
class Logger
{
    const DEFAULT_LOG_FILE = 'FtpSync.log';

    /**
     * Log file
     * 
     * @var string
     */
    protected $_logFile;

    /**
     * _logToFile 
     * 
     * @var boolean
     */
    protected $_logToFile;

    public function __construct($logToFile=false)
    {
        $this->_logToFile = $logToFile;
        $this->_logFile = self::DEFAULT_LOG_FILE;
        if (!touch($this->_logFile)) {
			trigger_error(
                    "Log file '".$this->_logFile."' is not writable. ".
                    "Please check permissions."
                    , 
                    E_USER_ERROR
                    );
		}
    }

    /**
     * Writes the given logs into the log file
     *
     * @access public
     * @param string
     */
	public function log($log)
    {
        if ($this->_logToFile && !empty($log)) {
            file_put_contents($this->_logFile, $log, FILE_APPEND);
        }
    }

    /**
     * endLog 
     * 
     * @return void
     */
    public function endLog()
    {
        $footer = str_repeat("-",100)."\n";
        $this->log($footer);
    }

    /**
     * Sets log file 
     * 
     * @param string $logFile 
     * @return void
     */
    public function setLogFile($logFile)
    {
        $this->_logFile = $logFile;
    }

    /**
     * Returns the log file 
     * 
     * @return string
     */
    public function getLogFile()
    {
        return $this->_logFile;
    }

    /**
     * getLogs 
     * 
     * @return string
     */
    public function getLogs()
    {
        if (file_exists($this->_logFile)) {
            return file_get_contents($this->_logFile);
        }
    }

    /**
     * Dumps the message
     * 
     * @param string $str 
     * @return Logger
     */
    public function dump($str)
    {
        if (!empty($str)) {
            if (Cli::isCli()) {
                echo $str."\n";
            }else{
                echo nl2br($str);
            }
            if ($this->_logToFile) {
                $this->log($str);
            }
            return $this;
        }
    }
}
