<?php

include_once "Crawler.class.php";

$LEVELS = 1; //how many levels deep do you want to Crawl?


//CRAWL the page specified by url and look for all images or links
if (empty($_REQUEST['url']) || $_REQUEST['url'] == " ") $url = "http://bcmoney-mobiletv.com"; else $url = $_REQUEST['url'];

$crawl = new Crawler($url, $LEVELS);
$images = $crawl->get('images');
$links = $crawl->get('links');
$embeds = $crawl->get('embeds');

//DEBUG (print parsed values)
echo "<a href=\"{$url}\" target=\"_blank\">{$url}</a>";
echo "<br/><br/>IMAGES: <pre>"; 
print_r($images);
echo "</pre><br/>LINKS: <pre>"; 
print_r($links);
echo "</pre><br/>EMBEDS: <pre>";
print_r($embeds);
echo "</pre>";

/*
foreach ($images as $image) { ; }
foreach ($links as $link) { ; }
foreach ($embeds as $embed) { ; }
*/

?>