<?php

require_once "../../config.php";

try {
    $db = new PDO("pgsql:dbname=$db_name;host=$db_host", $db_username, $db_password);
      if ($DEBUG) { echo "PDO connection object created for PostgreSQL"; }      
}
catch(PDOException $e) {
    echo $e->getMessage();
}

?>