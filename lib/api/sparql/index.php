<?php

require_once '../../parser/rdf/rap/test/config.php';

$database = ModelFactory::getDbStore(
   $GLOBALS['dbConf']['type'],
   $GLOBALS['dbConf']['host'],
   $GLOBALS['dbConf']['database'],
   $GLOBALS['dbConf']['user'],
   $GLOBALS['dbConf']['password']
);

$strModel = "http://xmlns.com/foaf/0.1/";
$dbModel = $database->getModel($strModel);

if ($dbModel === false) {
   die('Database does not have a model ' . $strModel . "\n");
} 

?>