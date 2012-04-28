<?php

require_once "Microdata.class.php";

$url = 'http://lin-clark.com/cool-hand-luke';
$md = new MicrodataPhp($url);
$data = $md->obj();
print $data->items[0]->properties['name'][0];   //prints 'Cool Hand Luke'
print $data->items[0]->properties['genre'][0];  //prints 'prison drama'

?>
