<?php

$format = (isset($_REQUEST['format']) && !empty($_REQUEST['format'])) ? filter_var($_REQUEST['format'], FILTER_SANITIZE_STRING) : 'html';

if (isset($_REQUEST['wsdl'])) {
  header("Location: ./RockPaperScissors.wsdl"); //redirect to WSDL
} else if (strtolower($format) == "soap") {
  header("Location: ./client.php"); //redirect to client (assume a SOAP client is trying to implement this Web Service)
} else {
  header("Location: ./server.php"); //redirect to server (assume a user is trying to interact directly with the game server)
}

exit; //make sure that code below does not get executed when we redirect

?>