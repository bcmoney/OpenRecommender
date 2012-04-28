<?php

require_once "../../config.php";

try {
    $dbh = new PDO("OCI:dbname=$db_name;charset=$db_charset", $db_username, $db_password);
}
catch (PDOException $e) {
  echo $e->getMessage();     
}

?>