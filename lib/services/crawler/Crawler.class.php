<?php

/**
 * http://vision-media.ca/resources/php/create-a-php-web-crawler-or-scraper-5-minutes
 * CLASS Crawler
 *  can build requests to specific URIs in order to parse HTML webpages for content
 * (originally looks for images or links, but has been expanded to look for page title, &lt;object&gt; and &lt;embed&gt;)
 */
class Crawler {

  protected $markup = '';
  protected $levels;

  public function __construct($uri, $levels=0) {
    $this->levels = $levels;
    $this->markup = $this->getMarkup($uri);    
  }

  public function getMarkup($uri) {
    return file_get_contents($uri);
  }

 public function getMarkupTimeout($uri,$time=5) {
   $ch = curl_init();
   $timeout = $time;
   curl_setopt ($ch, CURLOPT_URL, $uri);
   curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
   $contents = curl_exec($ch);
   curl_close($ch);
   return $contents;
 }

  public function get($type) {
    $method = "_get_{$type}";
    if (method_exists($this, $method)){
      return call_user_method($method, $this);
    }
  }

  protected function _get_page_title() 
  {
    if (!empty($this->markup)) {
      preg_match_all('/(.*?)\<\/title\>/si', $this->markup, $pagetitles); // for multi-line
      return !empty($pagetitles[1]) ? $pagetitles[1] : FALSE;
    }
  }

  protected function _get_video_title() 
  {
    if (!empty($this->markup)){
      preg_match_all('/<meta name="title" content="([^>]+)"\>/i', $this->markup, $title); 
      if (empty($title[1][0])) { $vid_title = "Click to Play"; }
      else { $vid_title = $title[1][0]; } 
      return !empty($pagetitles[1]) ? $pagetitles[1] : FALSE;
    }
  }

  protected function _get_images() {
    if (!empty($this->markup)){
      preg_match_all('/<img([^>]+)\/>/i', $this->markup, $images);
      return !empty($images[1]) ? $images[1] : FALSE;
    }
  }

  protected function _get_links() {
    if (!empty($this->markup)){
      preg_match_all('/<a([^>]+)\>(.*?)\<\/a\>/i', $this->markup, $links);
      return !empty($links[1]) ? $links[1] : FALSE;
    }
  }

  protected function _get_unique_links() {
    if (!empty($this->markup)){
      preg_match_all('/markup', $links);
      return !empty($links[1]) ? array_flip(array_flip($links[1])) : FALSE;
    }
  }

  protected function _get_objects() {
    if (!empty($this->markup)){
      preg_match_all('/<object([^>]+)\>(.*?)\<\/object\>/i', $this->markup, $objects);
      return !empty($objects[1]) ? $objects[1] : FALSE;
    }
  }

  protected function _get_params() {
    if (!empty($this->markup)){
      preg_match_all('/<param([^>]+)\>(.*?)\<\/param\>/i', $this->markup, $params);
      return !empty($params[1]) ? $params[1] : FALSE;
    }
  }

  protected function _get_embeds() {
    if (!empty($this->markup)){
      preg_match_all('/<embed([^>]+)\>/i', $this->markup, $embeds);
      return !empty($embeds[1]) ? $embeds[1] : FALSE;
    }
  }

}

?>