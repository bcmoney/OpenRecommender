<?php
/**
* You NEED xArray library for this to work.
* You can get it here: http://www.phpclasses.org/browse/package/3565.html
*/
require_once ('xArray.php');

require_once ('hCard.php');
require_once ('hCalendar.php');
require_once ('hReview.php');
require_once ('relElement.php');
if (PHP_VERSION>='5') {
	require_once('domxml-php4-to-php5.php');
}


class MicroFormatParser {

	/// Dom object.
	var $_dom = false;

	/// Parser switches
	var $_parseHCard = true;
	var $_parseHCalendar = true;
	var $_parseHReview = true;
	var $_parseRelTags = true;

   /**
	* Constructor.
	* Will die if no DOM XML extension found.
	*/
	function MicroFormatParser () {
		if (!function_exists('domxml_open_mem')) 
			die ('You need DOM XML extension for this to work. '.
				'See http://www.php.net/dom+xml for more info');
	}

   /**
	* Sets up parser switches.
	* Accepts a hash of values to set up.
	* Returns true on success, false on failure.
	* @param $args Array of setup values.
	* @return True on success, false on failure.
	*/
	function parserSetup ($args) {

		if (!is_array($args)) return false;

		if (isset($args['hcard'])) {

			$this->_parseHCard = $args['hcard'];

		}

		if (isset($args['hcalendar'])) {

			$this->_parseHCalendar = $args['hcalendar'];

		}

		if (isset($args['hreview'])) {

			$this->_parseHReview = $args['hreview'];

		}

		if (isset($args['reltag'])) {

			$this->_parseRelTags = $args['reltag'];

		}

		return true;

	}

   /**
	* Main parser dispatcher method.
	* This is where the first-level parsing gets dispatched to specific parser methods.
	* Accepts (requires) a single string, containing HTML to be parsed for uFormats.
	* Returns an xArray of parsed uFormats' objects.
	* @param $str HTML to be parsed.
	* @return xArray of uFormat objects.
	*/
	function parseSource ($str) {

		@$this->_dom =& domxml_open_mem($str);

		if (!$this->_dom) return false;

		$root = $this->_dom->get_elements_by_tagname('body');

		$root = $root[0];

		$children = $root->child_nodes();

		$elems = new xArray();

		foreach ($children as $child) {

			if ($this->_parseHCard) {

				$e = $this->parseCard($child);

				$elems->append($e->toArray());

			}

			

			if ($this->_parseHCalendar) {

				$e = $this->parseCalendar($child);

				$elems->append($e->toArray());

			}

			

			if ($this->_parseHReview) {

				$e = $this->parseReview($child);

				$elems->append($e->toArray());

			}

			

			if ($this->_parseRelTags) {

				$e = $this->parseRel($child);

				$elems->append($e->toArray());

			}

		}

		return $elems->compact();

	}

   /**
	* HCard-specific first level parser.
	* This is where all the hcard container elements are detected.
	* Accepts a DOM XML node, and returns an xArray of found hcards.
	* 
	* Normaly, there should be no need to call this yourself. 
	* Use parseSource method with appropriate parser setup instead.
	*/
	function parseCard ($child) {

		$elems = array();

		$atts = new xArray($child->attributes());

		$class = $atts->detect (

			//'return ("class" == $value->name && "vcard" == $value->value) ? true : false;'

			'return ("class" == $value->name && preg_match("/\bvcard\b/", $value->value)) ? true : false;'

		);

		if ($class) $elems[] = new hCard($child);

		else {

			foreach ($child->child_nodes() as $n) {

				$res = $this->parseCard($n);

				if ($res) $elems = array_merge($elems, $res->toArray());

			}

		}

		return new xArray(@$elems);

	}

   /**
	* HCalendar-specific first level parser.
	* This is where all the hcalendar container elements are detected.
	* Accepts a DOM XML node, and returns an xArray of found hcalendars.
	* 
	* Normaly, there should be no need to call this yourself. 
	* Use parseSource method with appropriate parser setup instead.
	*/	
	function parseCalendar ($child) {

		$elems = array();

		$atts = new xArray($child->attributes());

		$class = $atts->detect (

			//'return ("class" == $value->name && "vevent" == $value->value) ? true : false;'

			'return ("class" == $value->name && preg_match("/\bvevent\b/", $value->value)) ? true : false;'

		);

		if ($class) $elems[] = new hCalendar($child);

		else {

			foreach ($child->child_nodes() as $n) {

				$res = $this->parseCalendar($n);

				if ($res) $elems = array_merge($elems, $res->toArray());

			}

		}

		return new xArray(@$elems);

	}

   /**
	* HReview-specific first level parser.
	* This is where all the hreview container elements are detected.
	* Accepts a DOM XML node, and returns an xArray of found hreviews.
	* 
	* Normaly, there should be no need to call this yourself. 
	* Use parseSource method with appropriate parser setup instead.
	*/
	function parseReview ($child) {

		$elems = array();

		$atts = new xArray($child->attributes());

		$class = $atts->detect (

			//'return ("class" == $value->name && "hreview" == $value->value) ? true : false;'

			'return ("class" == $value->name && preg_match("/\bhreview\b/", $value->value)) ? true : false;'

		);

		if ($class) $elems[] = new hReview($child);

		else {

			foreach ($child->child_nodes() as $n) {

				$res = $this->parseReview($n);

				if ($res) $elems = array_merge($elems, $res->toArray());

			}

		}

		return new xArray(@$elems);

	}



   /**
	* RelTag-specific first level parser.
	* This is where all the relTags container elements are detected.
	* Accepts a DOM XML node, and returns an xArray of found relTags.
	* 
	* Normaly, there should be no need to call this yourself. 
	* Use parseSource method with appropriate parser setup instead.
	*/
	function parseRel ($child) {

		$elems = array();

		$atts = new xArray($child->attributes());

		$class = $atts->detect ('

			$valid = array ("tag", "bookmark", "license");

			return ("rel" == $value->name && in_array($value->value, $valid)) ? true : false;

		');

		if ($class) $elems[] = new relElement($child);

		else {

			foreach ($child->child_nodes() as $n) {

				$res = $this->parseRel($n);

				if ($res) $elems = array_merge($elems, $res->toArray());

			}

		}

		return new xArray(@$elems);

	}

}

?>

