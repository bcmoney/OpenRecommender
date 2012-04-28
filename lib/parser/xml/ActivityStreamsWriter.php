<?php

date_default_timezone_set("America/Halifax");

$feeds = array(           
    "audio"       =>    "http://ws.audioscrobbler.com/1.0/user/bcmoney/recenttracks.rss",
	"book"        =>    "http://www.goodreads.com/user/updates_rss/1711344?key=9ec7708446cc20b18659d4a782924bfbc9ef6046",
	//"event"       =>    "http://www.eventful.com/bcmoney/atom/",
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

function getDomain ($url) {
  $raw_url = parse_url($url);
  $hostname = $raw_url['host']; //	preg_match ("/\.([^\/]+)/", $raw_url['host'], $domain_only);
  return $hostname;
}

$day = "";
$stream = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$stream .= "<feed xmlns=\"http://www.w3.org/2005/Atom\" xmlns:activity=\"http://activitystrea.ms/spec/1.0/\">\n";
$stream .= "  <id>http://skipsearch.net/stream</id>\n";
$stream .= "  <title>SkipSearch - Bryan Copeland's Activity Stream</title>\n";
$stream .= "  <subtitle>Why Google it when you can SkipSearch?</subtitle>\n";
$stream .= "  <updated>2010-09-09T09:00:57+02:00</updated>\n";
$stream .= "  <author>\n";
$stream .= "    <name>Bryan Copeland</name>\n";
$stream .= "    <uri>http://bryancopeland.com/</uri>\n";
$stream .= "  </author>\n";
$stream .= "  <link rel=\"self\" type=\"application/atom+xml\" href=\"http://skipsearch.net/stream/ActivityStreamsWriter.php\" />\n";
$stream .= "  <link rel=\"alternate\" href=\"http://skipsearch.net/stream/\" />\n";

foreach ($list as $timestamp => $item) {
    $this_day = date("F jS",$timestamp);
    if ($day != $this_day) {
        $day = $this_day;
    }	
	$service_type = $item["name"];
	$service_url = $account[$service_type];
	$service_domain = getDomain($service_url);	
	$link = str_replace('&','&amp;',$item["link"]);
	$title = trim(htmlspecialchars($item["title"]));
	$published_year = date("Y",$timestamp);
	$published_time = date("g:ia",$timestamp);
	$published_timestamp = date("c",$timestamp);
	
    $stream .= "<entry>\n";
	$stream .= "  <id>tag:{$service_domain},{$published_year}:{$link}</id>\n";
	$stream	.= "  <title>{$title}</title>\n";
    $stream .= "  <published>{$published_timestamp}</published>\n";
	$stream .= "  <updated>{$published_timestamp}</updated>\n";
	$stream .= "  <author><name>Bryan Copeland</name><uri>http://bryancopeland.com</uri></author>\n";
	$stream .= "  <link type=\"text/html\" rel=\"alternate\" href=\"{$link}\"/>\n";
	$stream .= "  <link type=\"image/gif\" rel=\"image\" href=\"http://skipsearch.net/images/icons/{$service_type}.gif\"/>\n";
	$stream .= "  <activity:verb>http://activitystrea.ms/schema/1.0/{$service_type}</activity:verb>\n";
	$stream .= "  <activity:object>\n";
	$stream .= "    <id>tag:{$service_domain},{$published_year}:{$link}</id>\n";
	$stream	.= "    <title>{$title}</title>\n";
    $stream .= "    <published>{$published_timestamp}</published>\n";
	$stream .= "    <updated>{$published_time}</updated>\n";
	$stream .= "    <author><name>Bryan Copeland</name><uri>http://bryancopeland.com</uri></author>\n";
	$stream .= "    <link type=\"text/html\" rel=\"alternate\" href=\"{$link}\"/>\n";	
	$stream .= "    <link type=\"image/gif\" rel=\"image\" href=\"http://skipsearch.net/images/icons/{$service_type}.gif\"/>\n";
	$stream .= "  </activity:object>\n";
	$stream .= "</entry>\n";
}
$stream .= "</feed>";

header('Content-Type: text/xml; charset=utf-8');
print $stream; 

?>