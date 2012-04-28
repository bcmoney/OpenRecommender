<?php

include "AutoEmbed.class.php";

error_reporting(0);
ini_set('user_agent', 'OpenRecommender');

/* clean, validate and set URL */
$u = filter_var($_REQUEST['url'], FILTER_SANITIZE_URL);
$url = (!empty($u) && isset($u) && filter_var($u, FILTER_VALIDATE_URL)) ? $u : 'http://www.youtube.com/watch?v=ikTxfIDYx6Q';

$AE = new AutoEmbed();

// load the embed source from a remote url
if ($AE->parseUrl($url)) {
	if($AE->getEmbedCode()) {
		$AE->setParam('wmode','transparent');
		$AE->setParam('autoplay','true');
		echo $AE->getEmbedCode();
	}
	else if ($AE->getImageURL()) {
		echo '<img src="'.$AE->getImageURL().'/" />';
	}
	else {
		//iframe it
		echo '<iframe src="'.$url.'" width="600" height="400" frameborder="0" scrolling="auto"></iframe>';
	}
}
// No embeddable video found (or supported)
else {
 //try oEmbed
}

?>