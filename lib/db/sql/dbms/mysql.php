<?php

require "../../config.php";

try {
    $dbh = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);    
      if($DEBUG) { echo 'Connected to MySQL database via PDO'; }
}
catch(PDOException $e) {
    echo $e->getMessage();
}

?>