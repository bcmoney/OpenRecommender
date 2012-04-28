<?php

require_once "../../config.php";

try {  
  $dbh = new PDO("sqlite:$db_path"); //connect to SQLite database
}
catch(PDOException $e) {
  echo $e->getMessage();
}


?>