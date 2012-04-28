<?php

/* OpenGraphNode.php
 * =================
 *
 *
 * INSTALLATION
 * ------------
 *
 *
 *     Copy OpenGraphNode.php and the "arc" folder to your web server.
 *
 *     Requires PHP 5+. (Version 20100427 supports PHP 4.x.)
 *
 *
 * BASIC USAGE
 * -----------
 *
 *
 *     include "OpenGraphNode.php";
 *
 *     # Fetch and parse a URL
 *     #
 *     $page = "http://www.rottentomatoes.com/m/oceans_eleven/";
 *     $node = new OpenGraphNode($page);
 *
 *     # Retrieve the title
 *     #
 *     print $node->title . "\n";    # like this
 *     print $node->title() . "\n";  # or with parentheses
 *
 *     # And obviously the above works for other Open Graph Protocol
 *     # properties like "image", "description", etc. For properties
 *     # that contain a hyphen, you'll need to use underscore instead:
 *     #
 *     print $node->street_address . "\n";
 *
 *     # OpenGraphNode uses PHP5's Iterator feature, so you can
 *     # loop through it like an array.
 *     #
 *     foreach ($node as $key => $value) {
 *       print "$key => $value\n";
 *     }
 *
 *
 * ADVANCED USAGE
 * --------------
 *
 *
 *     # What if a page has multiple <meta property="og:title"> elements?
 *     #
 *     $titles = $node->title(TRUE);  # return an array
 *     print_r($titles);
 *
 *     # This gets a key=>value array of all properties.
 *     #
 *     $all = $node->All();
 *     print_r($all);
 *
 *     # And this gets a key=>array_of_values array of all properties.
 *     #
 *     $all = $node->All(TRUE);
 *     print_r($all);
 *
 *     # The Open Graph Protocol is based on RDFa, and OpenGraphNode.php
 *     # is powered by a full-fledged RDFa parser. You can access all
 *     # the page's RDFa data if you like!
 *     #
 *     $data = $node->RDFa();
 *
 *     # Return the URL of the page (after following any redirections)
 *     #
 *     $url = $node->Base();
 *
 * AUTHOR
 * ------
 *
 *
 *     Toby Inkster <http://tobyinkster.co.uk/>.
 *
 *
 * LICENCE
 * -------
 *
 *
 *     Choose your favourite of:
 *     <http://www.gnu.org/licenses/gpl-3.0.html>
 *     <http://www.gnu.org/licenses/gpl-2.0.html>
 *     <http://www.w3.org/Consortium/Legal/2002/copyright-software-20021231>
 *
 *
 * SUPPORT / WARRANTY
 * ------------------
 *
 *
 *     This program is distributed in the hope that it
 *     will be useful, but WITHOUT ANY WARRANTY; without
 *     even the implied warranty of MERCHANTABILITY or
 *     FITNESS FOR A PARTICULAR PURPOSE.
 *
 *     For support, try the OGP developers' mailing list
 *     <http://groups.google.com/group/open-graph-protocol>.
 *
 */

include 'arc/ARC2.php';

class OpenGraphNode implements Iterator
{
	private $_rdfa_parser;
	private $_data = array();
	
	public function __construct ($u)
	{
		$this->_rdfa_parser = ARC2::getSemHTMLParser();
		$this->_rdfa_parser->parse($u);
		$this->_rdfa_parser->extractRDF('rdfa');
		
		$index = $this->_rdfa_parser->getSimpleIndex(0);
		foreach ($index[ $this->Base() ] as $prop => $values)
		{
			$matches = array();
			if (preg_match('!^http://opengraphprotocol.org/schema/(.+)$!i', $prop, $matches))
				$p = strtolower($matches[1]);
			elseif (preg_match('!^http://ogp.me/ns#(.+)$!i', $prop, $matches))
				$p = strtolower($matches[1]);
			else
				$p = $prop;
			
			foreach ($values as $value)
			{
				if ($value['type'] == 'bnode') continue;
				$this->_data[$p][] = $value['value'];
			}
		}
		
		return $this;
	}
	
	# Magic method calls
	public function __call($name, $args)
	{
		$name  = str_replace('_', '-', $name);
		$array = isset($args[0]) ? $args[0] : FALSE;
		
		if (!isset($this->_data[$name]))
			return $array ? array() : NULL;
		
		return $array ? $this->_data[$name] : $this->_data[$name][0];
	}
	
	# Magic attributes
	public function __get ($name)
	{
		return $this->__call($name, FALSE);
	}

	public function __isset ($name) {
		return $this->__get($name)!==NULL;
	}
	
	public function RDFa ($flatten=TRUE)
	{
		return $this->_rdfa_parser->getSimpleIndex($flatten);
	}
	
	public function All ($arrays=FALSE)
	{
		if ($arrays)
			return $this->_data;
		
		$rv = array();
		foreach ($this->_data as $prop => $values)
			$rv[$prop] = $values[0];
		
		return $rv;
	}
	
	public function Base ()
	{
		return $this->_rdfa_parser->base;
	}
	
	# Iterator <http://php.net/manual/en/language.oop5.iterations.php>
	public function rewind ()
		{ reset($this->_data); }
	public function current ()
		{ $c = current($this->_data); return $c[0]; }
	public function key ()
		{ return key($this->_data); }
	public function next ()
		{ next($this->_data); }
	public function valid ()
		{ $k = key($this->_data); return !($k===NULL||$k===FALSE); }
}
