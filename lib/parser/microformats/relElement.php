<?php

class relElement {
	
	/// relElement contents
	var $name = false;
	/// relElement url (HREF attribute value)
	var $url = false;
	/// relElement type (tag, bookmark, permalink ... - REL attribute value)
	var $type = false;
	
	
	function relElement ($rootNode=false) {
		if ($rootNode) $this->populateFromDomNode($rootNode);
	}
	
	function populateFromDomNode ($rootNode) {
		$children = new xArray($rootNode->get_elements_by_tagname('*'));
		// Tags may occur out of a container.
		if ($children->length() == 0) $children = new xArray (array($rootNode));
		$res = $children->detect ('
			$atts = new xArray($value->attributes());
			return $atts->detect(\'
				return ("rel" == $value->name && preg_match("/\btag\b|\blicense\b|\bbookmark\b/", $value->value)) 
					? true : false;
			\');
		');
		
		if ($res) {
			$this->name = $res->get_content();
			$atts = new xArray($res->attributes());
			$url = $atts->detect('return ("href" == $value->name);');
			$this->url = ($url) ? $url->value : $res->get_content();
			$type = $atts->detect('return ("rel" == $value->name);');
			$this->type = $type->value;
		}
	}
}
?>