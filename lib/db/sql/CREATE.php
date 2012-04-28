<?php

require_once "../../../config.php";
include_once "{$db_type}.php";

$t = $_REQUEST['type'];
$table_type = (!empty($t)) ? $t : 'tv';

try {
    /*** set the PDO error mode to exception ***/
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    /*** begin the transaction ***/
    $dbh->beginTransaction();

    /*** CREATE table statements ***/
    $table = "CREATE TABLE {$table_type} ( {$table_type}_id INT(12) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  {$table_type}_title VARCHAR(25) NOT NULL,
                  {$table_type}_image VARCHAR(25),
                  {$table_type}_link VARCHAR(25),
              )";
    $dbh->exec($table);
    /***  INSERT statements ***/
    $dbh->exec("INSERT INTO {$table_type} ({$table_type}_title, {$table_type}_image, {$table_type}_link) VALUES ('emu', 'http://example.com/image1.jpg', 'http://example.com/1')");
    $dbh->exec("INSERT INTO {$table_type} ({$table_type}_title, {$table_type}_image, {$table_type}_link) VALUES ('funnel web', 'http://example.com/image2.jpg', 'http://example.com/2')");
    $dbh->exec("INSERT INTO {$table_type} ({$table_type}_title, {$table_type}_image, {$table_type}_link) VALUES ('lizard', 'http://example.com/image3.jpg', 'http://example.com/3')");          
    $dbh->exec("INSERT INTO {$table_type} ({$table_type}_title, {$table_type}_image, {$table_type}_link) VALUES ('dingo', 'http://example.com/image4.jpg', 'http://example.com/4')");
    echo $dbh->lastInsertId(); //display the id of the last INSERT

    /*** commit the transaction ***/
    $dbh->commit();

    /*** echo a message to say the database was created ***/
    echo 'Data entered successfully<br />';
}
catch(PDOException $e) {
    /*** roll back the transaction if we fail ***/
    $dbh->rollback();

    /*** echo the sql statement and error message ***/
    echo $sql . '<br />' . $e->getMessage();
}

include_once "CLOSE.php";

?>