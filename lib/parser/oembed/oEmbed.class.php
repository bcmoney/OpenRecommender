<?php

class oEmbed {

  public $resource_url;
  public $file_format;
  public $oembed_string;
  public $oembed_object;

  public $oembedUrls = array (
    'vids.myspace.com' => 'http://vids.myspace.com/index.cfm?fuseaction=oembed&url=~~PARAMETER~~',
    'www.5min.com' => 'http://www.oohembed.com/oohembed/?url=~~PARAMETER~~',    
    'www.amazon.com' => 'http://www.oohembed.com/oohembed/?url=~~PARAMETER~~',
    'www.blip.tv' => 'http://blip.tv/oembed/?url=~~PARAMETER~~&format=json',
    'www.collegehumor.com' => 'http://www.collegehumor.com/oembed.xml?url=~~PARAMETER~~',
    'www.dailymotion.com' => 'http://www.dailymotion.com/api/oembed?url=~~PARAMETER~~&format=json',    
    'www.flickr.com' => 'http://www.flickr.com/services/oembed/?url=~~PARAMETER~~',
    'www.funnyordie.com' => 'http://www.funnyordie.com/oembed?url=~~PARAMETER~~&format=json',
    'www.embed.ly' => 'http://api.embed.ly/1/oembed?key=8d08cf06ff6c11e0810a4040d3dc5c07&url=~~PARAMETER~~&format=json',
    'www.hulu.com' => 'http://www.hulu.com/api/oembed.json?url=~~PARAMETER~~',
    'www.ifixit.com' => ' http://www.ifixit.com/Embed?url=~~PARAMETER~~&format=json',
    'www.imdb.com' => 'http://imdb.com/oembed/?url=~~PARAMETER~~&format=json',
    'www.kinomap.com' => 'http://www.kinomap.com/oembed?url=~~PARAMETER~~&format=json',
    'www.livejournal.com' => 'http://www.oohembed.com/oohembed/?url=~~PARAMETER~~',
    'www.metacafe.com' => 'http://www.oohembed.com/oohembed/?url=~~PARAMETER~~',
    'my.opera.com' => 'http://my.opera.com/service/oembed/?url=~~PARAMETER~~',
    'www.qik.com' => 'http://qik.com/api/oembed?url=~~PARAMETER~~&format=json',
    'www.revision3.com' => 'http://revision3.com/api/oembed/?url=~~PARAMETER~~&format=json',
    'www.scribd.com' => 'http://www.scribd.com/services/oembed?url=~~PARAMETER~~&format=json',
    'www.screenr.com' => 'http://screenr.com/api/oembed.json?url=~~PARAMETER~~',
    'www.slideshare.net' => 'http://slideshare.net/oembed/?url=~~PARAMETER~~&format=json',
    'www.smugmug.com' => 'http://api.smugmug.com/services/oembed/?url=~~PARAMETER~~&format=json',
    'www.thedailyshow.com' => 'http://www.oohembed.com/oohembed/?url=~~PARAMETER~~',    
    'www.twitpic.com' => 'http://twitpic.com/oembed/?url=~~PARAMETER~~&format=json',
    'www.wikipedia.org' => 'http://wikipedia.org/oembed/?url=~~PARAMETER~~&format=json',
    'www.viddler.com' => 'http://lab.viddler.com/services/oembed/?url=~~PARAMETER~~&format=json',
    'www.vimeo.com' => 'http://vimeo.com/api/oembed.xml?url=~~PARAMETER~~&format=json',
    'www.wikipedia.org' => 'http://wikipedia.org/oembed/?url=~~PARAMETER~~&format=json',
    'www.wordpress.tv' => 'http://wordpress.tv/oembed/?url=~~PARAMETER~~&format=json',
    'www.xkcd.com' => 'http://www.oohembed.com/oohembed/?url=~~PARAMETER~~',
    'www.yfrog.com' => 'http://www.oohembed.com/oohembed/?url=~~PARAMETER~~',
    'www.youtube.com' => 'http://www.youtube.com/oembed?url=~~PARAMETER~~&format=json'
  );
  
  
  /* 
   * oEmbed
   *   constructor
   *
   * @param url String  representing the URL of the resource to load
   * @param format String  representing the Format [XML,JSON] of the resource to load (optional)
   */  
  public function __construct($url, $format="html") {    
    $this->resource_url = $url;    
    $this->file_format = $format;    
  }
  
  /* Make a request  */
  public function getOembed($url) {    
    //parse contents for display
    switch ($format) {
      case "json": //JSON
          $this->oembed_string = makeRequest($this->resource_url);
          $this->oembed_object = json_decode($this->oembed_string);          
        break;
      case "xml": //XML
        $this->oembed_string = makeRequest($this->resource_url);
        $this->oembed_object = new SimpleXMLElement($this->oembed_string);
        break;
      default: //HTML
        $oembed_array = $this->getOembedUrlFromMetadata($this->resource_url);
        $this->oembed_string = file_get_contents($oembed_array['oembed']);
        $this->oembed_object = new SimpleXMLElement($this->oembed_string);	
        break;
    }  
  }  
  
  /* oEmbed - XML/JSON Parser */
  public function getOembedType() {
    return $this->oembed_object->type;
  }  
  public function getOembedVersion() {
    return $this->oembed_object->version;
  }
  public function getOembedTitle() {
    return $this->oembed_object->title;
  }
  public function getOembedUrl() {
    return $this->oembed_object->url;
  }
  public function getOembedHtml() {
    return $this->oembed_object->html;
  }
  public function getOembedHtml5() {
    return $this->oembed_object->html5;
  }  
  public function getOembedWidth() {
    return $this->oembed_object->width;
  }
  public function getOembedHeight() {
    return $this->oembed_object->height;
  }
  public function getOembedAuthorName() {
    return $this->oembed_object->author_name;
  }
  public function getOembedAuthorURL() {
    return $this->oembed_object->author_url;
  }
  public function getOembedProviderName() {
    return $this->oembed_object->provider_name;
  }  
  public function getOembedProviderURL() {
    return $this->oembed_object->provider_url;
  }  
  
  /*
   * getOembedUrlFromMetadata
   *   Gets <META> tag content, along with <TITLE> and <LINK> tags
   *
   * @param url  String (optional)
   * @return meta  Associative Array
   */
  public function getOembedUrlFromMetadata($url) {    
	$meta = get_meta_tags($url); //get meta tags	

    $TITLE_TAG_LENGTH = 7; //length of '<title>' tag    		
      $titleStart = strpos($this->oembed_string,'<title>')+$TITLE_TAG_LENGTH; //find where the title CONTENT begins	
      $titleLength = strpos($this->oembed_string,'</title>')-$titleStart; //find how long the title is		
    $meta['title'] = trim(substr($this->oembed_string,$titleStart,$titleLength)); //extract title from page HTML
	
	$links = $this->getLinks();
	foreach ($links as $link) {
	  if (strlen(strstr($link,"xml"))>0) {
	    $meta['oembed'] = $link;
	  }
	}
	
    return $meta; // return array of data
  }
  
  /* 
   * getLinks
   *   Search through the HTML, save all <link> tags and store each link's attributes
   *
   * @param html  String
   * @return url_list  Associative Array
   */
  public function getLinks() {
	$html = $this->oembed_string;
    preg_match_all('/<link\s+(.*?)\s*\/?>/si', $html, $matches);
    $links = $matches[1];

    $final_links = array();
    $link_count = count($links);

    for($n = 0; $n < $link_count; $n++) {
      $attributes = preg_split('/\s+/s', $links[$n]);
      foreach($attributes as $attribute) {
        $att = preg_split('/\s*=\s*/s', $attribute, 2);
        if (isset($att[1])) {
		  $att[1] = preg_replace('/([\'"]?)(.*)\1/', '$2', $att[1]);
		  $final_link[strtolower($att[0])] = $att[1];
		}
      }
      $final_links[$n] = $final_link;
    }
	
    // now figure out which ones point to the OEMBED (but also hold onto RSS/Atom/OPML for possible usage)
    for($n = 0; $n < $link_count; $n++) {
      $href = '';
      if (strtolower($final_links[$n]['rel']) == 'alternate' || strtolower($final_links[$n]['rel']) == 'outline') {
        if (in_array(strtolower($final_links[$n]['type']), array('text/xml+oembed', 'application/json+oembed', 'oembed'))) {
          $href = $final_links[$n]['href']; // Find OEMBED		  
		}
		else if (strtolower($final_links[$n]['type']) == 'application/rss+xml') {
          $href = $final_links[$n]['href']; // Find RSS feeds
		}
		else if (!$href && strtolower($final_links[$n]['type']) == 'application/atom+xml') {
          $href = $final_links[$n]['href']; // Find ATOM feeds
		}
		else if (!$href && in_array(strtolower($final_links[$n]['type']), array('text/x-opml', 'application/xml', 'text/xml')) &&	preg_match("/\.opml$/", $final_links[$n]['href'])) {
          $href = $final_links[$n]['href']; // Find OPML outlines
		}
		else if (!$href && strtolower($final_links[$n]['type']) == 'text/xml') {
          $href = $final_links[$n]['href']; // catch-all to make this still work for any XML feed (i.e. APML, ActivityStrea.ms, etc)
		}		
		if ($href) {
          if (strstr($href, "http://") !== false) {
            $full_url = $href; // if it's absolute
          }
          else {
            $url_parts = parse_url($location); // otherwise, 'absolutize' it			  
            $full_url = "http://$url_parts[host]"; // only made it work for "http://" links. Any problem with this?
            if (isset($url_parts['port'])) {
              $full_url .= ":$url_parts[port]";
            }
			if ($href{0} != '/') {
              $full_url .= dirname($url_parts['path']); // it's a relative link on the domain
			  if (substr($full_url, -1) != '/') {
			    $full_url .= '/'; // if the last character isn't a '/', add it
			  }
			}
			$full_url .= $href;
		  }		
		  if(!in_array($full_url, $url_list)) {
            $url_list[] = $full_url;// Only add the resource URL if not already on the list
          }
        }
      }
    }
    return $url_list;      
  }	 
  
  
  /***************************************************************************/
  /* UTILITIES                                                               */
  /***************************************************************************/
  /* 
   * makeRequest
   *   utility function to load a file from URL
   */
  public function makeRequest($url) {
    try {
      return file_get_contents($url); //request page
    }
    catch(Exception $ex) {
      return file_get_contents_curl($url); //request page using CURL	
    }
  }  
  
  /* 
   * file_get_contents_curl
   *   Helper utility for making an HTTP Request
   *
   *   Requires CURL for PHP: 
   *   http://curl.haxx.se/libcurl/php/
   *
   * @param url  String representing the URL of the resource to load
   * @return file  String containing the entire contents of the resource loaded
   */
  private function file_get_contents_curl($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    $file = curl_exec($ch);
    curl_close($ch);

    return $file;
  }
  
  /*
   * display
   *   Echoes the contents of the file string we loaded in the constructor
   *   (useful for debugging)
   */
  public function display() {
    echo $this->oembed_string;
  }

  /*
   * output
   *   Outputs the contents of the file object (as an array var dump)
   *   (useful for debugging)
   */
  public function output() {
    print_r($this->oembed_object);
  }  
  
}

?>