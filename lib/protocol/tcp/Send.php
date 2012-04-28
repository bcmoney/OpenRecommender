<?php

$msg = !empty($_REQUEST['msg']) ? $_REQUEST['msg'] : "hello";
 
//////////////////////////////////////////////
//SEND
$tcp = TCP('localhost','80');
$tcp_client = $tcp->Send($msg);
print $tcp_client;
//////////////////////////////////////////////

?>
