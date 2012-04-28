<?php
require 'lib/FTPSync.php';
set_time_limit(0);

$sync = new FtpSync('127.0.0.1', 'Amin', '123');
$sync->enableLog()
     ->reportOnly(true)
     ->ignore('.DS_Store')
     ->ignore('.settings')
     ->ignore('.buildpath')
     ->ignore('.project')
     ->useStrategy(FtpSync::SYNC_BY_SIZE)
     ->remote('/Library/WebServer/Documents/SN', '/Users/Amin/Downloads/Test');
