<?php
/**
 * Copyright (c) 2011 ScientiaMobile, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the COPYING file distributed with this package.
 *
 * @package    WURFL_XMLParser
 * @copyright  ScientiaMobile, Inc.
 * @author     Steve Kamerman <steve AT scientiamobile.com>
 * @license    GNU Affero General Public License
 * @version    $id$
 */
/**
 * Abstract class to provide a skeleton for the wurfl.xml parsers.
 * @abstract
 * @package TeraWurflXMLParser
 */
abstract class TeraWurflXMLParser {

	public static $TYPE_WURFL = 'wurfl';
	public static $TYPE_PATCH = 'patch';
	
	public $wurflVersion;
	public $wurflLastUpdated;
	public $devices = array();
	public $errors = array();
	
	protected static $PARSER_SIMPLEXML = 'simplexml';
	protected static $PARSER_XMLREADER = 'xmlreader';
	
	protected $parser_type;
	protected $file_type;
	protected $xml;
		
	abstract public function open($filename,$file_type);
	abstract public function process(Array &$destination);
	protected function cleanValue($value){
		if($value === 'true') return true;
		if($value === 'false')return false;
		// Clean Numeric values by loosely comparing the (float) to the (string)
		$numval = (float)$value;
		if(strcmp($value,$numval)==0)$value=$numval;
		return $value;
	}
	protected function enabled($cap_or_group){
		return in_array($cap_or_group,TeraWurflConfig::$CAPABILITY_FILTER);
	}
	
	final public static function getInstance(){
		if(class_exists('XMLReader',false)){
			require_once realpath(dirname(__FILE__).'/TeraWurflXMLParser_XMLReader.php');
			return new TeraWurflXMLParser_XMLReader();
		}elseif(function_exists('simplexml_load_file')){
			require_once realpath(dirname(__FILE__).'/TeraWurflXMLParser_SimpleXML.php');
			return new TeraWurflXMLParser_SimpleXML();
		}else{
			throw new Exception("No suitable XML Parser was found.  Please enable XMLReader or SimpleXML");
		}
	}
}








