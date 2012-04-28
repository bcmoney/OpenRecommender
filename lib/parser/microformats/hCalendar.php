<?php
/**
 * hCalendar implementation.
 * For more info, visit:
 * http://microformats.org/wiki/hcalendar
 * http://microformats.org/wiki/hcalendar-cheatsheet
*/
class hCalendar {

	var $category = false;
    var $class = false;
    var $description = false;
    var $dateEnd = false;
    var $dateStart = false;
    var $duration = false;
    var $location = false;
    var $status = false;
    var $summary = false;
    var $uid = false;
    var $url = false;
    var $lastModified = false;
	
	
	function hCalendar ($rootNode=false) {
		if ($rootNode) $this->populateFromDomNode($rootNode);
	}
	
	function populateFromDomNode ($rootNode) {
		$children = new xArray($rootNode->get_elements_by_tagname('*'));
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bcategory\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->category = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bclass\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->class = $res->get_content();
		
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
				return ("class" == $value->name && preg_match("/\bdtend\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->dateEnd = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bdtstart\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->dateStart = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bduration\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->duration = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bnickname\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->nickname = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\blocation\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->location = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bstatus\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->status = $res->get_content();
		
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
				return ("class" == $value->name && preg_match("/\buid\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->uid = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\burl\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) {
			$atts = new xArray($res->attributes());
			$url = $atts->detect('return ("href" == $value->name);');
			$this->url = ($url) ? $url->value : $res->get_content();
		}
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\blast-modified\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->lastModified = $res->get_content();
	}
}
?>