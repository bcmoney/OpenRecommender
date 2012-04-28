<?php

//////////////////////////////////////////////
//RECEIVE message via TCP
$theHost = (!empty($argv[1])) ? $argv[1] : 'localhost';
$thePort= (!empty($argv[2])) ? $argv[2] : '80';
$tcp = TCP($theHost,$thePort);
$tcp_server = Receive($theHost,$thePort);
//////////////////////////////////////////////

?>
