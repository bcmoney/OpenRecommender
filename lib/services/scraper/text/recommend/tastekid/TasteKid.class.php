<?php

$url = (!empty($_REQUEST['url'])) ? $_REQUEST['url'] : 'http://www.tastekid.com/ask/ws?q=radiohead%2C+fight+club%2F%2Fbooks&verbose=1';

$tastekid = new TasteKid($url);
$resources = $tastekid->getResources();

foreach($resources as $resource) {
  name
  type
  wTeaser
  wUrl
  yUrl
  yID
  yTitle
  
}

?>