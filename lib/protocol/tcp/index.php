<?php

 require_once "TCP.class.php"; //TCP send/receive library
 
 
 $message = !empty($_REQUEST['msg']) ? $_REQUEST['msg'] : "hello";

/* TCP Receive (start listening for message) */ 
 include_once "Receive.php";
 
/* TCP Send (sends a test, or, user-specified message) */
 include "Send.php?msg=".$message;
 
?>