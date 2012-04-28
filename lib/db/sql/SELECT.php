<?php

require_once "../../../config.php";
include_once "{$db_type}.php";

$t = $_REQUEST['type'];
$table_type = (!empty($t)) ? $t : 'tv';

try {
    /*** set the error reporting attribute ***/
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /*** some variables ***/
    $item_id = 6;
    $item_title = 'Transformers';

    /*** prepare the SQL statement ***/
    $stmt = $dbh->prepare("SELECT * FROM {$table_type} WHERE {$table_type}_id = :item_id AND {$table_type}_title = :item_title");

    /*** bind the paramaters ***/
    $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
    $stmt->bindParam(':item_title', $item_title, PDO::PARAM_STR, 5);

    /*** execute the prepared statement ***/
    $stmt->execute();

    /*** fetch the results ***/
    $result = $stmt->fetchAll();

    /*** loop of the results ***/
    foreach($result as $row) {
      echo $row['item_id'].'<br />';
      echo $row['item_title'].'<br />';        
      echo $row['item_image'];
      echo $row['item_link'];   
    }
    
    include_once "CLOSE.php"; //CLOSE connection to the DB
}
catch(PDOException $e) {
    echo $e->getMessage();
}

?>