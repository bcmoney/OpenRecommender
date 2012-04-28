<?php

require_once "Message.class.php";


$file = (isset($_REQUEST["url"]) && !empty($_REQUEST["url"])) ? $_REQUEST["url"] : 'message.json';


$messageJSON = new MessageJSON($file);
$message = $messageJSON->getMessage(); //MESSAGE
  $messageID = $messageJSON->getMessageId($message);
  $messageAction = $messageJSON->getMessageAction($message);
  //DEBUG:  
    echo "<pre>";
      print_r($message);
    echo "</pre>";
  
echo "MESSAGE ID: {$messageID} | Action: {$messageAction}";
$service = $messageJSON->getService($message); 
foreach($service as $s) {               //SERVICE
  echo "<br/>&nbsp;&nbsp; SERVICE Name: ".$messageJSON->getServiceName($s)." | ".$messageJSON->getServiceEndpoint($s);
  $parameter = $messageJSON->getParameter($s);
  foreach($parameter as $p) {            //PARAMETER
    echo "<br/>&nbsp;&nbsp;&nbsp;&nbsp; PARAMETER Name: ".$messageJSON->getParameterName($p)." | ".$messageJSON->getParameterValue($p)." | ".$messageJSON->getParameterUnit($p)." | ".$messageJSON->getParameterUnitType($p);	
  }
}

?>