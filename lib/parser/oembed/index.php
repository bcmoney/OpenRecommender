<?php

include_once "oEmbed.class.php";

ini_set('user_agent', 'OpenRecommender');
error_reporting(0);

$url = (!empty($_REQUEST['url']) && isset($_REQUEST['url'])) ? $_REQUEST['url'] : "http://www.youtube.com/watch?v=uZsDliXzyAY";
$host = "";
if(!filter_var($url, FILTER_VALIDATE_URL)) {
	$parts = parse_url($url);	
	$host = $parts['host'];
}


if (!empty($host)) {
  $oembed = new oEmbed($url, 'json'); //oEmbed url directly (using XML or JSON)
  if ( isset($oembed) && array_key_exists($host, $oembed->oembedUrls) ) {
    $oembedDirectUrl = str_replace("~~PARAMETER~~", $url, $oembed->oembedUrls[$host]);
    $oembed->oembed_string = $oembed->makeRequest($oembedDirectUrl);
    
    $type = $oembed->getOembedType();
    if($type == 'photo') {
      echo '<img src="'.$oembed->getOembedHtml().'" alt="'.$oembed->getOembedTitle().'"/><br/><a href="'.$url.'" target="_blank">'.$oembed->getOembedTitle().'</a>'; //for 'photo' providers (Flickr, SmugMug, etc)
    }
    else if($type == 'link') {
      echo '<iframe src="'.$url.'" width="100%" height="600" frameborder="0" scrolling="no"></iframe><br/><a href="'.$url.'" target="_blank">'.$oembed->getOembedTitle().'</a>'; //for 'link' providers (try to create an iFrame around the content)
    }
    else {
      echo $oembed->getOembedHtml()."<br/><a href='".$url."' target='_blank'>".$oembed->getOembedTitle()."</a>"; //everything else (video, rich) embed HTML directly
    }
  }
  else {
    echo $oembed->getOembedHtml()."<br/><a href='".$url."' target='_blank'>".$oembed->getOembedTitle()."</a>";
  }
}
else {
  $oembed = new oEmbed($url); //oEmbed via META or LINK discovery
  echo $oembed->getOembedHtml()."<br/><a href='".$url."' target='_blank'>".$oembed->getOembedTitle()."</a>";
}

?>