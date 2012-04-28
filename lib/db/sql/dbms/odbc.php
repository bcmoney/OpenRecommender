<?php

require "../../config.php";

try {
  $dbh = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq=$db_path;Uid=$db_username");
}
catch (PDOException $e) {
  echo $e->getMessage();
} 
?>