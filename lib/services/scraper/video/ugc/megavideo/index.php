<?php

require_once('Megavideo.class.php');

$url = !empty($_REQUEST['url']) ? $_REQUEST['url'] : 'http://www.megavideo.com/?v=6PTHEVUY';

$mv = new Megavideo($url); //Setup the URL
# Work to get the link
$mv->doScrape();
# You now have the link
echo $mv->getLink();
# The link can be used for download or stream
# To use for stream, you will need a flash player like JW Flash Player
# http://www.longtailvideo.com/players/jw-flv-player/


$old_mv = $mv->oldMegavideo($url);
print "-- Megavideo Downloader by luruke --\n";
print "URL download:..........{$old_mv->get(url)}\n";
print "Title:.................{$old_mv->get(title)}\n";
print "Duration:..............{$old_mv->get(duration)}m\n";
print "Size:..................{$old_mv->get(size)}Mb\n";  
   //system("firefox ".$obj->get(url));

?>