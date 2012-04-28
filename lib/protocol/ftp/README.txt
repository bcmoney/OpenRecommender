Description
===================
This package can be used to synchronize two hosts via FTP protocol.
It connects to a given FTP server and compares the list of files of a remote directory with the files of a given local directory.
It transfers the files to the remote host that are outdated and downloads the files from the remote host that were updated more recently.
The directories are traversed recursively but the class may exclude given files or directories.
Size comparison and cleaning up files can be done in version 2.0 and later.
You can also synchronize files from the command line using a provided script.  
NOTE: USE THIS PACKAGE AT YOUR OWN RISK.

Features
-------------------
- Sync based on local or remote host with different strategies
- Interactive synchronization (CLI)
- Logging activities
- Traverse remote/local directories recursively
- Exclude files/directories from synchronization
- Get the list of files to be synchronized (Without transfering files)

Getting Started
-------------------

    Run lib/Cli.php from terminal: 
    $ php lib/Cli.php
    
    Or use the FtpSync like the provided example(index.php):
    require 'lib/FTPSync.php';
    $sync = new FtpSync('127.0.0.1', 'Amin', '123');
    $sync->enableLog()
        ->ignore('.DS_Store')
        ->useStrategy(FtpSync::SYNC_BY_MTIME)
        ->remote('Remote path', 'Local path');
