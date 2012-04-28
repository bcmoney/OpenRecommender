<?php

require_once "../../../config.php";

/**
 * Global instance of the Database
 */

class CONNECT {

  /*** Declare instance ***/
  private static $instance = NULL;

  /**
  *
  * the constructor is set to private so
  * so nobody can create a new instance using new
  *
  */
  private function __construct() {
    /*** maybe set the db name here later ***/
  }

  /**
  *
  * Return DB instance or create intitial connection
  *
  * @return object (PDO)
  *
  * @access public
  *
  */
  public static function getInstance() {
    if (!self::$instance) {
        self::$instance = include_once("{$db_type}.php"); //new PDO(driver, db_settings);
        self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return self::$instance;
  }

  /**
  *
  * Like the constructor, we make __clone private
  * so nobody can clone the instance
  *
  */
  private function __clone() {
  
  }

} /*** end of class ***/





/***************************************************************/
/*           USE THE DB class     */
/***************************************************************/
try {
    /*** query the database ***/
    $result = CONNECT::getInstance()->query("SELECT * FROM {$table_type}");

    /*** loop over the results ***/
    foreach($result as $row) {
        print $row["{$table_type}_title"] .' - '. $row["{$table_type}_image"] . '<br />';
    }
}
catch(PDOException $e) {
    echo $e->getMessage();
}

?>