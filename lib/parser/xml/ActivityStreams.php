<?php

date_default_timezone_set("America/Halifax");

$feeds = array(           
    "audio"       =>    "http://ws.audioscrobbler.com/1.0/user/bcmoney/recenttracks.rss",
	"book"        =>    "http://www.goodreads.com/user/updates_rss/1711344?key=9ec7708446cc20b18659d4a782924bfbc9ef6046",
	"image"       =>    "http://www.flickr.com/services/feeds/photos_public.gne?id=97951665@N00&format=rss_200",
	"news"        =>    "http://feeds.delicious.com/v2/rss/bcmoney?count=50",
	"text"        =>    "http://bcmoney-mobiletv.com/blog/?feed=rss2",
	//"text"        =>    "http://twitter.com/statuses/user_timeline/7425822.rss",
	"video"       =>    "http://gdata.youtube.com/feeds/base/users/BCmoney/uploads?alt=rss&v=2&orderby=published&client=ytapi-youtube-profile"
);
$account = array("audio"=>"http://last.fm","book"=>"http://goodreads.com","image"=>"http://flickr.com","news"=>"http://delicious.com","text"=>"http://bcmoney-mobiletv.com/blog","video"=>"http://youtube.com"); //TEMPORARY FIX... until JSON parsing works right for complete data array

$details = array("title","link");
$list = array();
$rss = new DOMDocument();

foreach ($feeds as $name => $feed) {
    $rss -> load($feed);
    $items = $rss -> getElementsByTagName("item");

    foreach ($items as $item) {
        if ($item -> getElementsByTagName("pubDate") -> item(0)) {
            $date = $item -> getElementsByTagName("pubDate") -> item(0) -> nodeValue;
        } 
		else {
            $date = $item -> getElementsByTagName("date") -> item(0) -> nodeValue;
        }
        $date = strtotime(substr($date,0,25));
        $list[$date]["name"] = $name;

        foreach ($details as $detail) {
            $list[$date][$detail] = $item -> getElementsByTagName($detail) -> item(0) -> nodeValue;
        }
    }
}

krsort($list);

$day = "";
$i=0; //TEMPORARY FIX
foreach ($list as $timestamp => $item) {
    $this_day = date("F jS",$timestamp);
    if ($day != $this_day) {
        $stream .= "</tbody>\n<thead class=\"alive\">\n<tr>\n<th colspan=\"3\">{$this_day}</th>\n</tr>\n</thead>\n<tbody class=\"alive\">\n";
        $day = $this_day;
    }	
    $stream .= "<tr class=\"vevent " . $item["name"] . "\">\n";
    $stream .= "<th><abbr class=\"dtstart\" title=\"" . date("c",$timestamp) . "\">" . date("g:ia",$timestamp) . "</abbr></th>\n";
    $stream .= "<td><a class=\"url summary\" href=\"" . $item["link"] . "\">" . trim($item["title"]) . "</a></td>\n";
    $stream .= "<td><a href=\"".$account[$item["name"]]."\" target=\"_blank\"><img src=\"../images/icons/" . $item["name"] . ".gif\" alt=\"" . $item["name"] . "\" /></a></td>\n";
    $stream .= "</tr>\n";
	$i++;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>SkipSearch - Activity Stream</title>
<link rel="stylesheet" type="text/css" media="all" href="activitystream.css" />
</head>
<body class="hfeed">
<table class="hcalendar">
<caption><a href="ActivityStreamsWriter.php" title="Download ActivityStreams in XML format (right-click, save as: 'ActivityStreams.xml')"><img src="../openrecommender/images/activitystreams.png" alt="ActivityStrea.ms"/></a> &nbsp;&nbsp; <a href="http://activitystrea.ms" target="_blank" title="ActivityStrea.ms schema"><img src="../images/external.png" alt="&#187;"/></a></caption>
<tbody>
<?php echo $stream; ?>
</tbody>
</table>
</body>
</html>