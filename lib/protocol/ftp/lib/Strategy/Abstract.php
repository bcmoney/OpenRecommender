<?php
abstract class Strategy_Abstract
{
    /**
     * _ftpSync 
     * 
     * @var FtpSync
     */
    protected $_ftpSync;

    public function __construct(FtpSync $ftpSync)
    {
        $this->_ftpSync = $ftpSync;
    }

    /**
     * Returns the FtpSync object 
     * 
     * @return FtpSync
     */
    public function getFtpSync()
    {
        return $this->_ftpSync;
    }
    /**
     * remote 
     * Sync based on remote host
     * 
     * @param string $remoteDir 
     * @param string $localDir 
     * @param array $options 
     * @return void
     */
    abstract public function remote($remoteDir, $localDir, $options=null);

    /**
     * local 
     * Sync based on local host
     * 
     * @param string $localDir 
     * @param string $remoteDir 
     * @param array $options 
     * @return void
     */
    abstract public function local($localDir, $remoteDir, $options=null);
}
