<?php

/**
 * Project:     PHP WSDL generator
 * File:        XMLCreator.php
 * Purpose		Create XML Documents
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * For questions, help, comments, discussion, etc., please send
 * e-mail to dragos@protung.ro
 *
 * @link http://www.protung.ro/
 * @copyright 2006 Dragos Protung
 * @author Dragos Protung <dragos@protung.ro>
 * @package PHP WSDL generator
 * @version 1
 */ 

class XMLCreator {

	/**
	 * Element name
	 *
	 * @var string
	 */
	private $tag;
	/**
	 * Value of the element
	 *
	 * @var string
	 */
	private $data;
	/**
	 * Starting string of CDATA
	 *
	 * @var string
	 */
	private $startCDATA = "";
	/**
	 * Ending string of CDATA
	 *
	 * @var string
	 */
	private $endCDATA = "";
	/**
	 * Attributes of the element
	 *
	 * @var array
	 */
	private $attributs = array();
	/**
	 * Sub elements of the element
	 *
	 * @var array
	 */
	private $children = array();


	/**
	 * Create first level
	 *
	 * @param string $tag
	 * @param string $cdata
	 */
	public function __construct($tag, $cdata = false) {
		$cdata ? $this->setCDATA() : null;
		$this->tag = $tag;
	}

	/**
	 * Set data in CDATA
	 *
	 */
	public function setCDATA() {
		$this->startCDATA = "<![CDATA[";
		$this->endCDATA = "]]>";
	}

	/**
	 * Add an attribute
	 *
	 * @param string $attrName
	 * @param string $attrValue
	 */
	public function setAttribute($attrName, $attrValue) {

		$newAttribute = array($attrName => $attrValue);
		$this->attributs = array_merge($this->attributs, $newAttribute);
	}

	/**
	 * Set the value of the element
	 *
	 * @param string $data
	 */
	public function setData($data) {
		$this->data = $data;
	}

	/**
	 * Add a child element
	 *
	 * @param XMLCreator $element
	 */
	public function addChild($element) {
		if($element && $element instanceof XMLCreator) {
			array_push($this->children, $element);
		}
	}

	/**
	 * Get all attributes in a string
	 *
	 * @return string
	 */
	protected function getAttributs() {
		$attributs = "";
		if (is_array($this->attributs)){
			foreach($this->attributs as $key=>$val) {
				$attributs .= " " . $key. "=\"" . $val . "\"";
			}
		}
		return $attributs;
	}

	/**
	 * Get all the children's XML
	 *
	 * @return string
	 */
	protected function getChildren() {
		$children = "";
		foreach($this->children as $key=>$val) {
			$children .= $val->getXML();
		}
		return $children;

	}

	/**
	 * Get the XML
	 *
	 * @return string
	 */
	public function getXML() {
		if (!$this->tag) {
			return "";
		}
		$xml  = "<" . $this->tag . $this->getAttributs() . ">";
		$xml .= $this->startCDATA;
		$xml .= $this->data;
		$xml .= $this->endCDATA;
		$xml .= $this->getChildren();
		$xml .= "</" . $this->tag . ">";
		return $xml;
	}

	/**
	 * Delete this element
	 *
	 */
	public function __destruct() {
		unset($this->tag);
	}
}

?>