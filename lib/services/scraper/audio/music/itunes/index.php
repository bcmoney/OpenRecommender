<?php

require "Itunes.class.php";

$AFFILIATE_KEY = '';
$API_KEY = '';

$itunes = new Itunes();

$term = $_REQUEST['q']; //generic search field (broken apart into Artist/Album/Song if they are not specified

$artist = $_REQUEST['artist'];
$song = $_REQUEST['song'];
$album = $_REQUEST['album'];

echo "<pre>";
print_r($itunes->getSong());
echo "</pre";

?>