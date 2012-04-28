<?php

require_once "Send.class.php";

$sendit = new AttachmentEmail('bcopeld@gmail.com', 'Hello!', 'This is the message body', '/sample.jpg');
$sendit->mail();

?>