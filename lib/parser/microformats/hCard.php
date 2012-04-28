<?php
/**
 * Implements most of hCard format properties.
 * SOUND missing.
 * See http://microformats.org/wiki/hcard for more info.
*/
class hCard {

	/// confidentiality/access classification of the entire hCard
	var $class = false;
	var $name = false;
	var $givenName = false;
	var $familyName = false;
	var $additionalName = false;
	var $honorificPrefix = false;
	var $honorificSuffix = false;
	var $nickname = false;
	var $sortString = false;
	var $url = false;
	var $photo = false;
	var $organization = false;
	var $title = false;
	var $role = false;
	var $fullAddress = false;
	var $streetAddress = false;
	var $locality = false;
	var $region = false;
	var $postalCode = false;
	var $poBox = false;
	var $extendedAddress = false;
	var $fullGeo = false;
	var $latitude = false;
	var $longitude = false;
	var $timezone = false;
	var $birthday = false;
	var $countryName = false;
	var $telephone = false;
	var $email = false;
	var $category = false;
	var $note = false;
	
	
	function hCard ($rootNode=false) {
		if ($rootNode) $this->populateFromDomNode($rootNode);
	}
	
	function populateFromDomNode ($rootNode) {
		$children = new xArray($rootNode->get_elements_by_tagname('*'));
		
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
				return ("class" == $value->name && preg_match("/\bfn\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->name = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bgiven-name\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->givenName = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bfamily-name\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->familyName = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\badditional-name\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->additionalName = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bhonorific-prefix\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->honorificPrefix = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bhonorific-suffix\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->honorificSuffix = $res->get_content();
		
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
				return ("class" == $value->name && preg_match("/\bsort-string\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->sortString = $res->get_content();
		
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
				return ("class" == $value->name && preg_match("/\bphoto\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) {
			$atts = new xArray($res->attributes());
			$src = $atts->detect('return ("src" == $value->name);'); // IMG tag
			$data = $atts->detect('return ("data" == $value->name);'); // OBJECT tag
			$this->photo = ($src) ? $src->value : (($data) ? $data->value : false);
		}
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bemail\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) {
			$atts = new xArray($res->attributes());
			$url = $atts->detect('return ("href" == $value->name);');
			$this->email = ($url) ? $url->value : $res->get_content();
		}
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\btel\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->telephone = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\badr\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->fullAddress = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bpost-office-box\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->poBox = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bextended-address\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->extendedAddress = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bstreet-address\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->streetAddress = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\blocality\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->locality = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bregion\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->region = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bpostal-code\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->postalCode = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bcountry-name\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->countryName = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bgeo\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->fullGeo = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\blatitude\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->latitude = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\blongitude\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->longitude = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\btz\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->timezone = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\bbday\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->birthday = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\btitle\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->title = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\brole\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->role = $res->get_content();
		
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("class" == $value->name && preg_match("/\borg\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->organization = $res->get_content();
		
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
				return ("class" == $value->name && preg_match("/\bnote\b/", $value->value)) 
					? true : false;
			\');
		');
		if ($res) $this->note = $res->get_content();
	}
}
?>