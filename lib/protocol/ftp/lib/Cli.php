<?php
require 'FTPSync.php';
class Cli
{
    /**
     * Checks whether local host OS is Microsoft Windows or not
     * 
     * @return boolean
     */
    public static function isLocalHostWin()
    {
        return preg_match('/^win/i', PHP_OS);
    }

    /**
     * Check if you are runing the script in cli
     * 
     * @return boolean
     */
    public static function isCli()
    {
        return defined('STDIN');
    }

    public function __construct()
    {
        if (!self::isCli()) {
            throw new Exception('You have to run this in cli');
        }
    }

    public function __invoke()
    {
        $this->getCli();
    }

    /**
     * Gets password invisibly
     *
     * @link http://www.sitepoint.com/blogs/2009/05/01/interactive-cli-password-prompt-in-php/
     * @access public
     * @return string password
     */
	protected function _hiddenPrompt($prompt) 
    {
		if (self::isLocalHostWin()) {
			$vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
			file_put_contents(
                $vbscript, 
                'wscript.echo(InputBox("'. addslashes($prompt). 
                                             '", "", "password here"))'
            );
			$command  = "cscript //nologo " . escapeshellarg($vbscript);
			$password = rtrim(shell_exec($command));
			unlink($vbscript);
			return $password;
		} else {
			$command = "/usr/bin/env bash -c 'echo OK'";
			if (rtrim(shell_exec($command)) !== 'OK') {
			      throw new Exception("Can't invoke bash");
			}
			$command = "/usr/bin/env bash -c 'read -s -p \"".
                addslashes($prompt). "\" mypassword && echo \$mypassword'";
			$password = rtrim(shell_exec($command));
			echo "\n";
			return $password;
		}
	}

    /**
     * Gets Command Line Interface.(only in shell environment)
     *
     * @static
     * @access public
     * @return FTPSync instance of this class 
     */
    public function getCli()
    {
        //STDIN constant is not defined in non-CLI environment
        if(!defined("STDIN")){			
            echo "This method should be run in CLI";
            return false;
        }
        echo "Host Name: ";
        $host =  $this->_read();
        echo "FTP Username: ";
        $user =  $this->_read();
        try{
            $pass = $this->_hiddenPrompt("FTP Password: ");
        }catch(Exception $e){
            echo $e;
            return false;
        }
        echo "FTP Port[21]: ";
        $port = $this->_read();
        $port = (!$port) ? 21 : (int)$port;

        echo "Connecting to $user@$host...\n";
        // create an instance of FTPSync with given parameters
        try{
            $ftp = new FtpSync($host, $user, $pass, $port);	
        }catch(Exception $e) {
            die($e->getMessage());
        }
        echo "Connected.\n";
        echo "Set passive? yes[no] ";
        if ($this->_isYes()) {
            $ftp->setPassive(true);
        }
        echo "Would you like to configure the options? yes[no]: ";
        if($this->_isYes()){
            echo "Please select the mode:\n".
                 "[1] - Auto \n".
                 "2 - ASCII \n".
                 "3 - Binary \n";
            $answer = $this->_read();
            if ($answer == 2) {
                $ftp->setMode(FTP_ASCII);
            }elseif ($answer == 3) {
                $ftp->setMode(FTP_BINARY);
            }
            echo "Would you like newer files in remote host to be ".
                "downloaded into local host or vice versa? yes[no] ".
                "(Note: your local file might be replaced ".
                "with newer one in remote host or vice versa)\n";
            if ($this->_isYes()) {
                $ftp->reportOnly(false);
            }else{
                $ftp->reportOnly(true);
            }
            echo "Would you like logs to be written in external file? ".
                 "yes[no] \n";
            if ($this->_isYes()) {
                $ftp->enableLog();
enter_logpath:
                echo "Please enter your log file path:".
                    "[".$ftp->getLogger()->getLogFile()."] \n";
                $logPath = trim(fgets(STDIN));
                if ($logPath) {
                    if (!is_writable($logPath)) {
                        echo "'".$logPath . "' is not writable, ".
                            "Please enter a valid path:";
                        goto enter_logpath;
                    }else{
                        $ftp->getLogger()->setLogFile($logPath);
                    }
                }

            }

            echo "if you have files or directories to exclude, ".
                "please insert(serperate their paths with comma):\n";
            //get comma seperated list of directories that must be excluded
            $excludes =  trim(fgets(STDIN));
            if($excludes){
                $aExcludes = split(",", $excludes);
                foreach($aExcludes as $ex){
                    $ftp->ignore(trim($ex));
                }
            }
        }
        echo "Starting...\n";
        $this->_showOptions($ftp);
	}
	
	
	/**
     * Shows options that can be used for sync
     *
     * @access protected
     * @param resource
     */
	protected function _showOptions($ftp)
    {
show:
        echo "Please Select one of these strategies for synchronization:\n";
        $syncStrategies = $ftp->getSyncStrategies();
        $strategies = array_flip(array_keys($syncStrategies));
        $i = 0;
        $operations = array();
        foreach ($syncStrategies as $strategyClass=>$strategyObj) {
            $className = new ReflectionClass($strategyClass);
            foreach ($className->getMethods() as $method){
                if ($method->name != 'remote' && $method->name != 'local') {
                    continue;
                }
                $i++;
                try{
                    $reflectionMethod = new myReflection(
                            $strategyClass, 
                            $method->name
                        );
                    $operations[$i] = array(
                            'class'      => $strategyClass, 
                            'method'     => $method->name,
                            'reflection' => $reflectionMethod,
                            );
                    //print operations with their descriptions
                    //$i = $strategies[$strategyClass]+1;
                    echo '['.$i.'] - '.$strategyClass.'::'.$method->name.
                         ' *** '.$reflectionMethod->getDescription()."\n";
                }catch(Exception $e) {
                    die($e->getMessage());
                }
            }
        }
        echo "[".++$i."] Exit \n";
        echo "What is your choice [1-$i]: ";
        $selectedNum =  intval(trim(fgets(STDIN)));
        if ($selectedNum === 0) {
            goto show;
        }
        if ($selectedNum === $i) {
            $ftp->close();
            die("Bye.\n");
        }
        //$selectedNum--;	//items are started at 1 in the list. first items is in 0 index of array
        //get information about selected operation
        $method = $operations[$selectedNum]['reflection'];
        $reflectionParams = $method->getParameters();	//get number of parameters
        $paramCount = $method->getNumberOfParameters();	//get parameter quantity
        $aParamDocs = $method->getParamDesc(); //get description of parameters
        echo "You have selected `".$operations[$selectedNum]['class']."::".
             $operations[$selectedNum]['method']."`.".
             "You must pass $paramCount parameter(s) to this method.\n";
        $i = 0;
        $params = array();
        while($i<=$paramCount && isset($aParamDocs[1][$i])){
            echo "Param ".($i+1)." ->".$aParamDocs[1][$i].
                ".Please write it down:\n";
            $input = trim(fgets(STDIN));
            if( $reflectionParams[$i]->isOptional() && ($input==false) ){
                $i++;
                continue;
            }
            if($input == "true"){
                $input = true;
            }elseif($input == "false"){
                $input = false;
            }elseif(intval($input) > 0){
                $input = intval($input);
            }
            $params[] = $input;
            $i++;
        }
        //call selected method with given parameters
        call_user_func_array(
                array(
                    $ftp->useStrategy($operations[$selectedNum]['class']),
                    $operations[$selectedNum]['method']
                    )
                , $params
                );	
        echo "Would you like to do new operation? yes[no] ";
        $answer = trim(fgets(STDIN));
        if (strtolower($answer)=="yes") {
            goto show;
        }else{
            $ftp->close();	//close ftp connection
        }
    }

  /**
   * Reads user input from command line 
   * 
   * @return string
   */
  public function _read()
  {
      return trim(fgets(STDIN));
  }

  /**
   * Determines whether the user's input is "Yes" or not 
   * 
   * @return boolean
   */
  public function _isYes()
  {
      $answer = $this->_read();
      return substr(strtolower($answer), 0, 1) === 'y';
  }
}
$cli = new Cli();
$cli();
