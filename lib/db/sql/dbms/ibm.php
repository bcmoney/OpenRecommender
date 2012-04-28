<?php

require_once "../../config.php";

try {
    $db = new PDO("ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$db_name; HOSTNAME=$db_host,4;PORT=$db_port;PROTOCOL=TCPIP;", $db_username, $db_password);
}
catch (PDOException $e) {
    echo $e->getMessage();
}
?>