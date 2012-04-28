<?php

require "Shoutcast.class.php";

$API_KEY = 'bc1hhZmG9Y9qTlnb';

$shoutcast = new Shoucast($API_KEY);

  $top500 = $shoutcast->top500();
  //DEBUG:
  echo "<pre";
    print_r($top500);
  echo "</pre>";

$s = $_REQUEST['station']; //Station name
$g = $_REQUEST['genre']; //Genre selection
  $a = $_REQUEST['artist']; //Artist 
  $s= $_REQUEST['song']; //Song

$station = $shoutcast->getStations()

  echo "<pre>";
    print_r($station);
  echo "</pre>";
?>