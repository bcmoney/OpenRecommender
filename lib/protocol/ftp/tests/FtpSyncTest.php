<?php
require_once dirname(__DIR__).'/lib/FtpSync.php';

class FtpSyncTest extends PHPUnit_Framework_TestCase
{
    protected $_ftp;

    public function setUp()
    {
        $this->_ftp = new FtpSync('127.0.0.1', 'Amin', '123');
    }

    public function testLocalFiles()
    {
        $this->markTestSkipped();
        $result = $this->_ftp->ignore('.wma')
                       ->getLocalFilesRecursive('Music/Classical');
        var_dump($result);
    }

    public function testRemoteFiles()
    {
        $this->markTestSkipped();
        $result = $this->_ftp
                       ->getRemoteFilesRecursive('Music/Classical');
    }
}
