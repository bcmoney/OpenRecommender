<?php


class TCP {
  var $host;
  var $port;
  
  function __construct($host='127.0.0.1',$port=1234) {
    if(!preg_match("/^d{1,3}.d{1,3}.d{1,3}.d{1,3}$/",$host)) {
        trigger_error('Invalid IP address format.',E_USER_ERROR);
    }
    if(!is_int($port)||$port<1||$port>65535) {
        trigger_error('Invalid TCP port number.',E_USER_ERROR);
    }
    $this->host=$host;
    $this->port=$port;    
  }
  
/*****************************************************************************/  
/*
 * TCP Receive
 *   Create a basic TCP server for point-to-point communications
 * @author copelandb
 */
  function Receive($host='127.0.0.1',$port=1234) {
    if(!preg_match("/^d{1,3}.d{1,3}.d{1,3}.d{1,3}$/",$host)) {
        trigger_error('Invalid IP address format.',E_USER_ERROR);
    }
    if(!is_int($port)||$port<1||$port>65535) {
        trigger_error('Invalid TCP port number.',E_USER_ERROR);
    }
    $this->host=$host;
    $this->port=$port;
    $this->connect();
  }
  
  function connect() {
        set_time_limit(0);		
		fscanf(STDIN, "%d\n", $close); //listens for the exit command as a boolean but represented as an integer "1"
		while ($close != 1) {
			// create low level socket
			if(!$socket=socket_create(AF_INET,SOCK_STREAM,0)) {
				trigger_error('Error creating new socket.',E_USER_ERROR);
			}
			// bind socket to TCP port
			if(!socket_bind($socket,$this->host,$this->port)) {
				trigger_error('Error binding socket to TCP port.',E_USER_ERROR);
			}
			// begin listening connections
			if(!socket_listen($socket)) {
				trigger_error('Error listening socket connections.',E_USER_ERROR);
			}
			// create communication socket
			if(!$comSocket=socket_accept($socket)) {
				trigger_error('Error creating communication socket.',E_USER_ERROR);
			}
			// read socket input
			$socketInput=socket_read($comSocket,1024);
			// convert to uppercase socket input 
			$socketOutput=strtoupper(trim($socketInput))."n";
			// write data back to socket server
			if(!socket_write($comSocket,$socketOutput,strlen($socketOutput))) {
				trigger_error('Error writing socket output',E_USER_ERROR);
			}	
		}
		close($socket, $comSocket);
  }
  
	function close($socket, $comSocket) {
	    // close sockets
        socket_close($comSocket);
        socket_close($socket);
	}
  

/*****************************************************************************/
/*
 * TCP Send
 *   Connect to an endpoint directly via TCP and send a message, then disconnect
 * @author copelandb
 */  
  function Send($host='127.0.0.1',$port=1234,$msg='test') {
      if(!preg_match("/^d{1,3}.d{1,3}.d{1,3}.d{1,3}$/",$host)) {
          trigger_error('Invalid IP address format.',E_USER_ERROR);
      }
      if(!is_int($port)||$port<1||$port>65535) {
          trigger_error('Invalid TCP port number.',E_USER_ERROR);
      }
      $this->host=$host;
      $this->port=$port;
      $this->socket_get($msg);
  }
  
	function socket_get($data, $host=$this->host, $port=$this->port) {
	  $output = "";

	  // Create a TCP Stream Socket
	  $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	  if ($socket === false)
		throw new Exception("Socket Creation Failed");

	  // Connect to the server.
	  $result = socket_connect($socket, $host, $port);
	  if ($result === false)
		throw new Exception("Connection Failed");

	  // Write to socket!
	  socket_write($socket, $data, strlen($data));

	  // Read from socket!
	  do {
      $line = socket_read($socket, 1024, PHP_NORMAL_READ);
      $output .= $line;
	  } while ($line != "");

	  // Close and return.
	  socket_close($socket);
	  return $output;
	}
  
}

?>
