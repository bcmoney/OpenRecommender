<?php

  require_once "DataSource.php";
  
  $url = (!empty($_REQUEST['url'])) ? $_REQUEST['url'] : 'tests/data/names.csv';
  $DEBUG = false;
  
  $csv = new File_CSV_DataSource($url);
  
  //Headers
  $headers = $csv->getHeaders(); // array('name', 'age');  
  $table = "<table border=1><tr>";
  foreach($headers as $h) {
	$table .= '<th style="background:#ccc">'.$h."</th>";
  }
  $table .= "</tr>";
  
  //Columns
  $csv_array = $csv->connect();
	if($DEBUG) { print_r($csv_array); } 
  foreach($csv_array as $csv) {
	$table .= "<tr><td>".$csv['name']."</td><td>".$csv['age']."</td></tr>"; 
  }
  $table .= "</table>";
  
  echo $table;
?>