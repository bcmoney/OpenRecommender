<?php

$msg = !empty($_REQUEST['msg']) ? $_REQUEST['msg'] : "hello";

//////////////////////////////////////////////
//SEND
$http = HTTP('http://localhost/openrecommender/lib/protocol/http/Receive.php'); //send message to this HTTP endpoint
$http_client = $http->Send($msg);
print $http_client;
//print $http->sendMessageFGC($msg);
//print $http->sendMessageCURL($msg);
//////////////////////////////////////////////

?>
