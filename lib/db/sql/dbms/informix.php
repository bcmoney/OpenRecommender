<?php

require_once "../../config.php";

try {
  $dbh = new PDO("informix:host=$db_host; service=$db_port; database=$db_name; server=ids_server; protocol=onsoctcp; EnableScrollableCursors=1", $db_username, $db_password);
}
catch (PDOException $e) {
  echo $e->getMessage();
}

?>