<?php

/*
 * get_url_content
 *   this is used to get url content
 */
function get_url_content($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

/*
 * get_inbetween
 *   this function used to retrieve value inbetween specific delimiter
 */
function get_inbetween($tag1,$tag2,$string) {
  if (eregi("$tag1(.*)$tag2?", $string, $out)) {
    $outdata = $out[1];
  }
  return $outdata;
}

/*
 * 
 *   this function used to download music from veoh.com
 *   URL: http://www.veoh.com/videos/v1734061jbnYFjPj
 */
function get_veoh($url) {
  // get the music_id
  $ari = explode("/",$url);
  $v_id = array_pop($ari);

  // retrieve xml files
  $data = get_url_content("http://www.veoh.com/rest/video/".$v_id."/details");

  // retrieve path into music files
  $hasil = get_inbetween("fullPreviewHashPath","fullPreviewToken=",$data);
  $hasil = str_replace(array('"','='),"",$hasil);

  // yell it loud
  return trim($hasil);
}

echo get_veoh("http://www.veoh.com/videos/v1734061jbnYFjPj");

?>