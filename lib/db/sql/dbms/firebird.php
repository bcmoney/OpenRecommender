<?php

require_once "../../config.php";

try {
    $dbh = new PDO("firebird:dbname=$db_name:$db_path", $db_username, $db_password);
}   
catch (PDOException $e) {
    echo $e->getMessage();
}

?>