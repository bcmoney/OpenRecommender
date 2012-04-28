<?php

/**
 * HTTP client/server
 */ 

class HTTP {

    var $dir;
    var $host;
    var $port;
    var $socket;
    var $comSocket;

    //URL to grab (again, see NOTE on security above)
    $urlArg = $argv[1]; //look for any command-line URL arguments
    $urlParam = $_REQUEST['url']; //look for any URL parameters containing the 'url' to GET
    $url = (!empty($urlArg)) ? $urlArg : ((!empty($urlParam)) ? $urlParam : 'Receive.php'); 
  
  /**
   * HttpConsumer
   *   Provides a basic Web Server implemented in PHP. 
   *   NOTE: It waits for the text 'quit' to be typed in at the console, or,
   *		   passed via STDIN then closes upon receiving it
   * @param host
   * @param port
   * @param dir
   */    
    function Receive($host='127.0.0.1',$port='12345',$dir='defaultDir') {
        $this->dir=$dir;
        $this->host=$host;
        $this->port=$port;
        $this->createSocket();
        $this->readUserInput();
    }
    
    function createSocket() {
        set_time_limit(0);
        // create low level socket
        if(!$this->socket=socket_create(AF_INET,SOCK_STREAM,0)){
            trigger_error('Error creating new socket',E_USER_ERROR);
        }
        // tie up socket to TCP port
        if(!socket_bind($this->socket,$this->host,$this->port)){
            trigger_error('Error binding socket to TCP port',E_USER_ERROR);
        }
        // begin listening connections
        if(!socket_listen($this->socket)){
            trigger_error('Error listening socket connections',E_USER_ERROR);
        }
        // create communication socket
        if(!$this->comSocket=socket_accept($this->socket)){
            trigger_error('Error creating communication socket',E_USER_ERROR);
        }
        // display welcome message
        $message='Please type the name of the file you want to fetch: '."\r\n";
        socket_write($this->comSocket,$message,strlen($message));
    }
    
    // read user input
    function readUserInput() {
        // start a loop and continue reading user input
        do {
            // delay loop execution
            sleep(10);
            // read socket input
            $socketInput=socket_read($this->comSocket,1024);
            if(trim($socketInput)!='') { 
                // if user did not entered the 'quit' command continue reading data
                if(trim($socketInput)!='quit') {
                    // convert to uppercase socket input 
                    $socketOutput=$this->fetchFile($socketInput)."\r\n";
                    // write data back to socket server
                    socket_write($this->comSocket,$socketOutput,strlen($socketOutput));
                }
                else {
                    // if 'quit' command was entered close communication socket & terminate all the connections
                    socket_close($this->comSocket);
                    break;
                }
            }
        }
        while(true);
        // close global socket
        socket_close($this->socket);
    }
    // fetch file
    function fetchFile($socketInput) {
        $path='C:\\php\cli\\'.$this->docDir.'\\'.trim($socketInput);
        if(!file_exists($path)) {
            return 'File not found on this server. Please try again';
        }
        return file_get_contents($path);
    }
    
    

/**
 * HttpProducer.php
 *   Acts as a server-side requestor for data on behalf of the client-side
 *   (NOTE: there could be a small security risk by doing a naiive REQUEST 
 *      to pass the proxy URL without POST + SSL and more thorough validation.
 *  	  If an attacker knew the location of this script, would there be a chance 
 *	  they can use it as a proxy for attacks to other servers, or this server. 
 *	  For our purposes, it probably is negligible, but for more on how to solve
 *	  potential issues, see: http://php.net/manual/en/function.fopen.php  
 *	  or:  http://www.virtualforge.de/vmovie/xss_selling_platform_v1.0.php)
 * @author copelandb
 */     
	function Send($msg='test',$url=this->$url) {
	
		//force fresh request (no caching)
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		
		//parse the passed in URL using PHP convenience function (could add validation here, see: http://www.scriptol.com/how-to/parsing-url.php )
		$arr = parse_url($url);
		$parameters = $arr["query"];
		parse_str($parameters, $param);

		$format = $param['type']; //look for user-specified parameter 'type' specifying the desired return format

		// Set your return content type, based on the specified response type
		switch ($format) {
			case "html":
				header('Content-type: text/html');
				break;				
			case "xml":
			case "kml":
				header('Content-type: application/xml');
				break;
			case "json":
			case "geojson":
				header('Content-type: application/json');
				break;
			case "atom": 
			case "georss":			
				header('Content-type: application/atom+xml');	
				break;
			case "rdf":
			case "rss":
				header('Content-type: application/rdf+xml');
			case "text":
			case "plain":
			case "plaintext":
				header('Content-type: text/plain');
				break;			
			default:
				header('Content-type: text/html');
				break;
		}
    $response = "";
    
    try {
      $response = $this->sendMessage($msg);
    } catch(Exception $e) {
      $response = $this->sendMessageFGC($msg);
    } catch(Exception $ex) {
      $response = $this->sendMessageCURL($msg);
    }
    
    return $response;
	}
  
	/**
	 * sendMessage
	 *   Sends an HTTP message via GET to remote content/data  
	 *   (NOTE: some servers may not allow or be configured to support "fopen" or "fgets"... 
	 *    if not you should be able to use either "file_get_contents" or "CURL" lib method instead)
	 * @param msg the message to send 
	 * @return buffer response message 
	 */
	function sendMessage($msg) {	
		$path = trim(this->$url . $msg);
		$handle = fopen($path, "r");
		// some content/data was received, then read & return
		if ($handle) {
			while (!feof($handle)) {
				$buffer = fgets($handle, 4096);
			}
			fclose($handle);
		}
		return $buffer;
	}

	/**
	 * sendMessageFGC
	 *   Sends an HTTP message via GET retrieval of the passed URL
	 * @param msg the message to send 
	 * @return the file at the given path (i.e. HTTP GET response)
	 */
	function sendMessageFGC($msg) { 
		$path = trim(this->$url . $msg);
			if(!file_exists($path)) { 
			  return 'File not found on this server. Please try again'; 
			}
		return file_get_contents($path);    
	}

	/**
	 * sendMessageCURL
	 * Sends an HTTP message via GET using the CURL library
	 * NOTE: CURL can be a nice middle-ground when a hosting provider 
	 *       does not allow fopen, file_get_conents or other in-memory system calls 
	 *       as the library is easily to install, has its own error-handling and 
	 *       a small footprint.
	 *    More options for CURL available here:
	 *	  http://www.php.net/curl_setopt
	 * @param msg the message to send
	 * @return output of the CURL request (i.e. HTTP GET response)
	 */
	function sendMessageCURL($msg) { 
		$path = trim(this->$url . $msg);
		
		// is curl installed?
		if (!function_exists('curl_init')) { die('CURL is not installed!');	}
	 
		// create a new curl resource
		$ch = curl_init();
		
		// request configuration options
		curl_setopt($ch, CURLOPT_URL, $path); // set URL to download
		curl_setopt($ch, CURLOPT_REFERER, "http://www.hsvo.ca/"); // set referer (site sending/passing this request on)    
		curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0"); // user agent     
		curl_setopt($ch, CURLOPT_HEADER, 0); // remove header? 0 = yes, 1 = no
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // should curl return or print the data? true = return, false = print
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); // timeout in seconds
	 
		// download the given URL, and return output
		$output = curl_exec($ch);
	 
		// close the curl resource, and free system resources
		curl_close($ch);
	 
		// print output
		return $output;
	}
  
}

?>
