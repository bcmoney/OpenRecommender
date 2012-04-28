<?php
/**
 * Simplified:
 * Missing item info (because the spec sucks).
 * See http://microformats.org/wiki/hreview for more info
 */
class hReview {

	///  version. optional. text.
	var $version = false;
    /// summary. optional. text.
	var $summary = false;
    /// item type. optional. product | business | event | person | place | website | url.
	var $type = false;
    /// item info. required. fn (url || photo ) | hCard (for person or business) | hCalendar (for event)
	//var $info = false;
    /// reviewer. optional. hCard.
	var $reviewer = false;
    /// dtreviewed. optional. ISO8601 absolute date time.
	var $dateReviewed = false;
    /// rating. optional. fixed point integer [1.0-5.0], with optional alternate worst (default:1.0) and/or best (default:5.0), also fixed point integers, and explicit value.
	var $rating = false;
    /// description. optional. text with optional valid XHTML markup.
	var $description = false;
    /// tags. optional. keywords or phrases, using rel-tag, each with optional rating.
	var $tags = false;
    /// permalink. optional, using rel-bookmark and rel-self.
	var $permalink = false;
    /// license. optional, using rel-license. 
	var $license = false;
	
	function hReview ($rootNode=false) {
		if ($rootNode) $this->populateFromDomNode($rootNode);
	}
	
	function populateFromDomNode ($rootNode) {
		$children = new xArray($rootNode->get_elements_by_tagname('*'));
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bversion\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->version = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bsummary\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->summary = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\btype\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->type = $res->get_content();
		
		//REWIEVER
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\breviewer\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->reviewer = new hCard($res);
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bdtreviewed\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->dateReviewed = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\brating\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->rating = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bdescription\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->description = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\btags\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) {
			$this->tags = new xArray ($this->tags);
			$m = new MicroFormatParser();
			foreach ($res->child_nodes() as $rcn) {
				$e = $m->parseRel ($rcn);
				$this->tags->append ($e->toArray());
			}
		}
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bpermalink\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) {
			$this->permalink = new relElement($res);
		}
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\blicense\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) {
			$this->license = new relElement($res);
		}
	}
}
?>