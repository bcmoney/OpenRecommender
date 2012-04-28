<?php

/**
 * XMLTV
 *   XML Parser, following Object-Oriented getter/setter style
 *   NOTES for rolling back jQuery to Plain-Old-JavaScript:  
 *   1. replace .find() with xmlDoc.getElementsByTagName("...")    NOTE: getElementsByTagName will not work well with XML namespaces
 *   2. replace .children() with [n] (i.e. xmlDoc.getElementsByTagName("item")[0] for the first item in list of XML elements)
 *   3. replace .attr() with .getAttribute() for non-jquery javascript parsing
 *   4. replace .text() with .childNodes[0].nodeValue
 * @author bcmoney
 * @param xml snippet of XML or XML Object
 * @return data String 
 */
class XMLTV {

	var $epg;
	
	function __construct($xml) {
		$schedule = ( file_exists($xml) || preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i',$xml) ) ? simplexml_load_file($xml) : simplexml_load_string($xml);
		$this->epg = $schedule;
	}
	
  /* Performs the SOAP request to get the data */
  function getEPG() {
  }
  
	//<xmltv> (root)
	function getXMLTV() {
		return $this->epg;
    }	
	//<tv>
	function getTV($xmltv) {
		return $xmltv->tv;
	}
	// @generator-info-name
	function getGeneratorInfoName($tv) {
		return $tv["generator-info-name"];
	}
	//	<channel>
	function getChannel($tv) {
		return $tv->channel;
	}
	//	@id
	function getChannelID($channel) {
		return $channel["id"];
	}
	//	  <icon>
	function getChannelIcon($channel) {
		return $channel->icon["src"];
	}
	//	  <url>
	function getChannelURL($channel) {
		return $channel->url;
	}
	//	  <display-name>
	function getChannelDisplayName($channel, $language) {
		$i18n = (!empty($language)) ? $language : "en";
		return $channel["display-name"][$i18n];
	}
	//  <programme>
	function getProgramme($tv) {
		return $tv->programme;
	}
	//  @start
	function getProgrammeStart($programme) {
		return $programme["start"];
	}
	//  @stop
	function getProgrammeStop($programme) {
		return $programme["stop"];
	}
	//  @channel
	function getProgrammeChannel($programme) {
		return $programme["channel"];
	}
	//    <title>
	function getProgrammeTitle($programme) {
		return $programme->title;
	}
	//    <sub-title>
	function getProgrammeSubTitle($programme) {
		return $programme["sub-title"];
	}
	//    <desc>
	function getProgrammeDesc($programme) {
		return $programme->desc;
	}
	//    <date>
	function getProgrammeDate($programme) {
		return $programme->date;
	}
	//    <category>
	function getProgrammeCategory($programme) {
		return $programme->category;
	}
	//    <episode-num  system="dd_progid"
	function getProgrammeEpisodeID($programme) {
		$epID = $programme["episode-num"]["system"];
		$episodeID = '';
		if ($epID=='dd_progid') { $episodeID = $programme["episode-num"]["system"]; }
		return $episodeID;
	}
	//    <episode-num  system="onscreen"
	function getProgrammeEpisodeNum($programme) {
		$epNum = $programme["episode-num"]["system"];
		$episodeNum = '';
		if ($epNum=='onscreen') { $episodeID = $programme["episode-num"]["system"]; }
		return $episodeNum;
	}
	//    <audio><stereo>
	function getProgrammeAudioStereo($programme) {
		return $programme->audio["stereo"];
	}
	//    <previously-shown>
	function getProgrammePreviouslyShownStart($programme) {
		return $programme["previously-shown"]["start"];
	}
	//    <subtitles> (format for alternate languages)
	function getProgrammeSubtitlesType($programme) {
		return $programme->subtitles["type"];
	}

}

?>