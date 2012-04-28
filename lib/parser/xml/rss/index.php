<?php

require_once "magpierss/rss_fetch.inc";


$url = (!empty($_REQUEST['url'])) ? filter_var($_REQUEST['url'], FILTER_VALIDATE_URL) : 'http://bcmoney-mobiletv.com/rss/new/';

$rss = fetch_rss($url);
$channel_title = $rss->channel['title'];
$channel_description = $rss->channel['description'];
$channel_link = $rss->channel['link'];
$channel_logo = $rss->image['url'];
echo "<h1><a href=\"{$channel_link}\" title=\"{$channel_description}\" target=\"_blank\"><img src=\"{$channel_logo}\" style=\"border:0;vertical-align:middle\">{$channel_title}</a></h1>";
echo "<ul>";
foreach ($rss->items as $item ) {
	$title = $item['title'];
	$url   = $item['link'];
  $description = htmlspecialchars_decode($item['description']);
	echo "<li><a href='{$url}' target='_blank'>{$title}</a><br/>{$description}</li>";
}
echo "</ul>";

?>