<?php

require_once("XMLCreator.php");
require_once("PHPParser.php");

/**
 * Project:     PHP WSDL generator
 * File:        WSDLCreator.php
 * Purpose:     Create a WSDL
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
 * @copyright 2009 Dragos Protung
 * @author Dragos Protung <dragos@protung.ro>
 * @package PHP WSDL generator
 * @version 1.3.1
 */ 

class WSDLCreator {
	
	/**
	 * Object for PHPParser
	 *
	 * @var PHPParser
	 */
	private $PHPParser;
	
	/**
	 * Object for XMLCreator
	 *
	 * @var XMLCreator
	 */
	private $XMLCreator;
	
	/**
	 * Array with internal variable types
	 *
	 * @var array
	 */
	private $xsd = array("string"=>"string", "bool"=>"boolean", "boolean"=>"boolean",
						 "int"=>"integer", "integer"=>"integer", "double"=>"double", "float"=>"float", "number"=>"float",
						 "datetime"=>"datetime",
						 "resource"=>"anyType", "mixed"=>"anyType", "unknown"=>"anyType", "unknown_type"=>"anyType", "anytype"=>"anyType"
						 );
	
	/**
	 * Array with soapenc variable types
	 *
	 * @var array
	 */
	private $soapenc  = array("array"=>"soapenc:Array");
	
	/**
	 * Array of typens defined by classes that are parsed
	 *
	 * @var array
	 */
	private $typensDefined = array();
	
	/**
	 * Array of typens
	 *
	 * @var array
	 */
	private $typens = array();
	private $typeTypens = array();
	private $complexTypens = array();
	
	/**
	 * Array of URLs for undefined typens
	 *
	 * @var array
	 */
	private $typensURLS = array();
	
	/**
	 * General URL
	 *
	 * @var string
	 */
	public $classesGeneralURL;
	
	/**
	 * The WSDL
	 *
	 * @var string
	 */
	private $WSDL;
	
	/**
	 * The WSDL in XMLCreator object
	 *
	 * @var XMLCreator
	 */
	private $WSDLXML;
	
	/**
	 * Array of classes
	 *
	 * @var array
	 */
	private $classes = array();
	
	/**
	 * Array of URLs of classes
	 *
	 * @var array
	 */
	private $classesURLS = array();
	
	/**
	 * The name of the WSDL
	 *
	 * @var stirng
	 */
	private $name;
	
	/**
	 * The URL of the WSDL
	 *
	 * @var stirng
	 */
	private $url;
	
	/**
	 * Array of messages
	 *
	 * @var array
	 */
	private $messages = array();
	
	/**
	 * Array of portTypes
	 *
	 * @var array
	 */
	private $portTypes = array();
	
	/**
	 * Array of bindings
	 *
	 * @var array
	 */
	private $bindings = array();
	
	/**
	 * Array of services
	 *
	 * @var array
	 */
	private $services = array();
	
	/**
	 * Array of parameters of a class
	 *
	 * @var array
	 */
	private $paramsNames = array();
	
	/**
	 * Flag to include the documentation of the methods in the WSDL
	 *
	 * @var bool
	 */
	private $includeMethodsDocumentation = true;
	
	
	/**
	 * Constructor
	 *
	 * @param string $name
	 * @param string $url
	 */
	public function __construct($name, $url) {
		
		$name = str_replace(" ", "_", $name);
		
		$this->PHPParser = new PHPParser();
		
		$this->WSDLXML = new XMLCreator("definitions");
		$this->WSDLXML->setAttribute("name", $name);
		$this->WSDLXML->setAttribute("targetNamespace", "urn:".$name);
		$this->WSDLXML->setAttribute("xmlns:typens", "urn:".$name);
		$this->WSDLXML->setAttribute("xmlns:xsd", "http://www.w3.org/2001/XMLSchema");
		$this->WSDLXML->setAttribute("xmlns:soap", "http://schemas.xmlsoap.org/wsdl/soap/");
		$this->WSDLXML->setAttribute("xmlns:soapenc", "http://schemas.xmlsoap.org/soap/encoding/");
		$this->WSDLXML->setAttribute("xmlns:wsdl", "http://schemas.xmlsoap.org/wsdl/");
		$this->WSDLXML->setAttribute("xmlns", "http://schemas.xmlsoap.org/wsdl/");
		$this->name = $name;
		$this->url = $url;
	}
	
	public function includeMethodsDocumentation ($include = true) {
		$this->includeMethodsDocumentation = (bool)$include;
	}
	
	/**
	 * Add a file to parse
	 *
	 * @param string $file
	 */
	public function addFile ($file) {$this->PHPParser->addFile($file);}
	
	
	/**
	 * Unignore all.
	 * All ignored items will be removed (including method types)
	 *
	 */
	public function ignoreNone () {$this->PHPParser->ignoreNone();}
	
	/**
	 * Ignore or not all public methods
	 *
	 * @param boolean $ignore
	 */
	public function ignorePublic ($ignore = false) {$this->PHPParser->ignorePublic($ignore);}
	
	/**
	 * Ignore or not all protected methods
	 *
	 * @param boolean $ignore
	 */
	public function ignoreProtected ($ignore = false) {$this->PHPParser->ignoreProtected($ignore);}
	
	/**
	 * Ignore or not all private methods
	 *
	 * @param boolean $ignore
	 */
	public function ignorePrivate ($ignore = false) {$this->PHPParser->ignorePrivate($ignore);}
	
	/**
	 * Ignore or not all static methods
	 *
	 * @param boolean $ignore
	 */
	public function ignoreStatic ($ignore = false) {$this->PHPParser->ignoreStatic($ignore);}
	
	/**
	 * Add a class name to ignore on parsing
	 *
	 * @param string $class
	 */
	public function ignoreClass ($class) {$this->PHPParser->ignoreClass($class);}
	
	/**
	 *  Add classes to ignor on parsing
	 *
	 * @param array $classes
	 */
	public function ignoreClasses ($classes) {$this->PHPParser->ignoreClasses($classes);}
	
	/**
	 * Add a method of a class to ignore on parsing
	 *
	 * @param array $method
	 */
	public function ignoreMethod ($method) {$this->PHPParser->ignoreMethod($method);}

	/**
	 * Add methods of classes to ignore on parsing
	 *
	 * @param array $methods
	 */
	public function ignoreMethods ($methods) {$this->PHPParser->ignoreMethods($methods);}
	
	/**
	 * Set an URL for all the classes that do not have an explicit URL set
	 *
	 * @param string $url
	 */
	public function setClassesGeneralURL ($url) {
		$this->classesGeneralURL = $url;
	}
	
	/**
	 * Set an URL for a class
	 *
	 * @param string $className
	 * @param string $url
	 */
	public function addURLToClass ($className, $url) {
		$this->classesURLS[$className] = $url;
	}
	
	/**
	 * Set an URL for a variable type (when a variable is an object)
	 *
	 * @param string $type
	 * @param string $url
	 */
	public function addURLToTypens ($type, $url) {
		$this->typensURLS[$type] = $url;
	}
	
	/**
	 * Add a typens that is undefined in internal typens
	 *
	 * @param string $type
	 */
	private function addtypens ($type) {
		static $t=0;
		if (isset($this->typensURLS[$type])) {
			$this->typens["typens".$t] = $this->typensURLS[$type];
			$this->typeTypens[$type] = "typens".$t;
			$t++;
		} elseif (array_key_exists($type, $this->classes)) {
			$this->typensDefined[$type] = $type;
		} else {
			$foundClasses = $this->PHPParser->getFoundClasses();
			if (in_array($type, $foundClasses)) {
				trigger_error("There are no methods defined for <b>".$type."</b>", E_USER_ERROR);
			} else {
				trigger_error("URL for type <b>".$type."</b> or method for class <b>".$type."</b> not defined", E_USER_ERROR);
			}
		}
	}
	
	
	private function addComplexTypes($type) {
		
		if (substr($type, -2) == "[]") { // array
			$complexType = trim(substr($type,0,-2));
			
			$xsdComplexType = new XMLCreator("xsd:complexType");
			$xsdComplexType->setAttribute("name", "ArrayOf".$complexType);
				$xsdComplexTypeContent = new XMLCreator("xsd:complexContent");
					$xsdComplexTypeContentRestriction = new XMLCreator("xsd:restriction");
					$xsdComplexTypeContentRestriction->setAttribute("base", "soapenc:Array");
						$xsdComplexTypeContentRestrictionAttr = new XMLCreator("xsd:attribute");
						$xsdComplexTypeContentRestrictionAttr->setAttribute("ref", "soapenc:arrayType");
						
						if(isset($this->xsd[strtolower($complexType)])) {
							$arrayType = "xsd:".$complexType;
						} elseif (isset($this->soapenc[$complexType])) {
							$arrayType = $this->soapenc[$complexType];
						} else {
							$arrayType = "urn:".$complexType."[]";
						}
						
						$xsdComplexTypeContentRestrictionAttr->setAttribute("arrayType", $arrayType);
					$xsdComplexTypeContentRestriction->addChild($xsdComplexTypeContentRestrictionAttr);
				$xsdComplexTypeContent->addChild($xsdComplexTypeContentRestriction);
			$xsdComplexType->addChild($xsdComplexTypeContent);
			
			$this->complexTypens[$type] = $xsdComplexType;
		}
	}
	
	/**
	 * Create a message for the WSDL
	 *
	 * @param string $name
	 * @param string $returnType
	 * @param array $params
	 */
	private function createMessage ($name, $returnType = false, $params = array()) {
		
		$message = new XMLCreator("message");
		$message->setAttribute("name", $name);
		if (is_array($params)) {
			foreach ($params as $pname=>$param) {
				
				if (isset($this->paramsNames[$pname])) {
					$pname = $pname.($this->paramsNames[$pname]+1);
				} else {
					$this->paramsNames[$pname] = 0;
				}
				
				$part = new XMLCreator("part");
				$part->setAttribute("name", $pname);
				$type = isset($param["varType"]) ? $param["varType"]:"anyType";
				if (isset($this->xsd[strtolower($type)])) {
					$type = "xsd:".$this->xsd[strtolower($type)];
				} elseif (isset($this->soapenc[$type])) {
					$type = $this->soapenc[$type];
				} elseif (substr($type, -2) == "[]") {
					$this->addComplexTypes($type);
					$type = "urn:ArrayOf".trim(substr($type,0,-2));
				} else {
					if (isset($this->typeTypens[$type])) {
						$type = $this->typeTypens[$type].":".$type;
					} else {
						$this->addtypens($type);
						$typens = isset($this->typensDefined[$type]) ? "typens" : $this->typeTypens[$type];
						$type = $typens.":".$type;
					}
				}
				$part->setAttribute("type", $type);
				$message->addChild($part);
			}
		}
		$this->messages[] = $message;
		
		if ($returnType) {
			$message = new XMLCreator("message");
			$message->setAttribute("name", $name."Response");
			$part = new XMLCreator("part");
			$part->setAttribute("name", $name."Return");
			$type = isset($returnType) ? $returnType:"anyType";
			if (isset($this->xsd[strtolower($type)])) {
				$type = "xsd:".$this->xsd[strtolower($type)];
			} else {
				if (isset($this->typeTypens[$type])) {
					$type = $this->typeTypens[$type].":".$type;
				} elseif (isset($this->soapenc[$type])) {
					$type = $this->soapenc[$type];
				} elseif (substr($type, -2) == "[]") {
					$this->addComplexTypes($type);
					$type = "urn:ArrayOf".trim(substr($type,0,-2));
				} else {
					$this->addtypens($type);
					$typens = isset($this->typensDefined[$type]) ? "typens" : $this->typeTypens[$type];
					$type = $typens.":".$type;
				}
			}
			$part->setAttribute("type", $type);
			$message->addChild($part);
			$this->messages[] = $message;
		} else {
			$message = new XMLCreator("message");
			$message->setAttribute("name", $name."Response");
			$this->messages[] = $message;
		}
	}
	
	/**
	 * Create a portType for the WSDL
	 *
	 * @param array $portTypes
	 */
	private function createPortType ($portTypes) {
		if (is_array($portTypes)) {
			foreach ($portTypes as $class=>$methods) {
				$pt = new XMLCreator("portType");
				$pt->setAttribute("name", $class."PortType");
				foreach ($methods as $method=>$components) {
					$op = new XMLCreator("operation");
					$op->setAttribute("name", $method);
					if ($this->includeMethodsDocumentation && $components["documentation"]) {
						$doc = new XMLCreator("documentation");
						$doc->setData(trim($components["documentation"]));
						$op->addChild($doc);
					}
					$input = new XMLCreator("input");
					$input->setAttribute("message", "typens:".$method);
					$op->addChild($input);
					
					$output = new XMLCreator("output");
					$output->setAttribute("message", "typens:".$method."Response");
					$op->addChild($output);
					
					$pt->addChild($op);
				}
				$this->portTypes[] = $pt;
			}
		}
	}
	
	/**
	 * Create a binding for the WSDL
	 *
	 * @param array $bindings
	 */
	private function createBinding ($bindings) {
		if (is_array($bindings)) {
			$b = new XMLCreator("binding");
			foreach ($bindings as $class=>$methods) {
				$b->setAttribute("name", $class."Binding");
				$b->setAttribute("type", "typens:".$class."PortType");
				$s = new XMLCreator("soap:binding");
				$s->setAttribute("style", "rpc");
				$s->setAttribute("transport", "http://schemas.xmlsoap.org/soap/http");
				$b->addChild($s);
				foreach ($methods as $method=>$components) {
					$op = new XMLCreator("operation");
					$op->setAttribute("name", $method);
					$s = new XMLCreator("soap:operation");
					$s->setAttribute("soapAction", "urn:".$class."Action");
					$op->addChild($s);

					$input = new XMLCreator("input");
					$s = new XMLCreator("soap:body");
					$s->setAttribute("namespace", "urn:".$this->name);
					$s->setAttribute("use", "encoded");
					$s->setAttribute("encodingStyle", "http://schemas.xmlsoap.org/soap/encoding/");
					$input->addChild($s);
					$op->addChild($input);
					
					$output = new XMLCreator("output");
					$output->addChild($s);
					$op->addChild($output);
					$b->addChild($op);
				}
				$this->bindings[] = $b;
			}
		}
	}
	
	/**
	 * Create a service for the WSDL
	 *
	 * @param array $services
	 */
	private function createService ($services) {
		if (is_array($services)) {
			foreach ($services as $class=>$methods) {
				if (isset($this->classesURLS[$class]) || $this->classesGeneralURL) {
					$url = isset($this->classesURLS[$class]) ? $this->classesURLS[$class] : $this->classesGeneralURL;
					$port = new XMLCreator("port");
					$port->setAttribute("name", $class."Port");
					$port->setAttribute("binding", "typens:".$class."Binding");
					$soap = new XMLCreator("soap:address");
					isset($this->classesURLS[$class]) ? $soap->setAttribute("location", $this->classesURLS[$class]) : "";
					$port->addChild($soap);
				} else {
					trigger_error("URL for class <b>".$class."</b> was not defined", E_USER_ERROR);
				}
			}
			if (isset($port)) {
				$this->services[] = $port;
			}
		}
	}
	
	/**
	 * Generate the WSDL
	 *
	 */
	public function createWSDL () {
		$this->classes = $this->PHPParser->getClasses();
		foreach ($this->classes as $class=>$methods) {
			$pbs = array();
			ksort($methods);
			foreach ($methods as $method=>$components) {
				
				if ($components["type"] == "public" || $components["type"] == "") {
					if (array_key_exists("params", $components)) {
						$this->createMessage($method, $components["returnType"], $components["params"]);
					} else {
						$this->createMessage($method, $components["returnType"]);
					}
					$pbs[$class][$method]["documentation"] = $components["description"];
					$pbs[$class][$method]["input"] = $method;
					$pbs[$class][$method]["output"] = $method;
				}
			}
			$this->createPortType($pbs);
			$this->createBinding($pbs);
			$this->createService($pbs);
		}
		
		// adding typens
		foreach ($this->typens as $typenNo=>$url) {
			$this->WSDLXML->setAttribute("xmlns:".$typenNo, $url);
		}
		
		// add types
		if (is_array($this->typensDefined) && count($this->typensDefined) > 0) {
			$types = new XMLCreator("types");
			$xsdSchema = new XMLCreator("xsd:schema");
			$xsdSchema->setAttribute("xmlns", "http://www.w3.org/2001/XMLSchema");
			$xsdSchema->setAttribute("targetNamespace", "urn:".$this->name);
			$vars = $this->PHPParser->getClassesVars();
			foreach ($this->typensDefined as $typensDefined) {
				$complexType = new XMLCreator("xsd:complexType");
				$complexType->setAttribute("name", $typensDefined);
				$all = new XMLCreator("xsd:all");
				if (isset($vars[$typensDefined]) && is_array($vars[$typensDefined])) {
					ksort($vars[$typensDefined]);
					foreach ($vars[$typensDefined] as $varName=>$varType) {
						$element = new XMLCreator("xsd:element");
						$element->setAttribute("name", $varName);
						$varType = isset($this->xsd[$varType]) ? "xsd:".$this->xsd[strtolower($varType)] : "anyType";
						$element->setAttribute("type", $varType);
						$all->addChild($element);
					}
				}
				$complexType->addChild($all);
				$xsdSchema->addChild($complexType);
				foreach ($this->complexTypens as $ct) {
					$xsdSchema->addChild($ct);
				}
			}
			$types->addChild($xsdSchema);
			$this->WSDLXML->addChild($types);
		}
		
		// adding messages
		foreach ($this->messages as $message) {
			$this->WSDLXML->addChild($message);
		}
		
		// adding port types
		foreach ($this->portTypes as $portType) {
			$this->WSDLXML->addChild($portType);
		}
		
		// adding bindings
		foreach ($this->bindings as $binding) {
			$this->WSDLXML->addChild($binding);
		}
		
		// adding services
		$s = new XMLCreator("service");
		$s->setAttribute("name", $this->name."Service");
		foreach ($this->services as $service) {
			$s->addChild($service);
		}
		$this->WSDLXML->addChild($s);
		
		$this->WSDL  = "<?xml version='1.0' encoding='UTF-8'?>\n";
		$this->WSDL .= "<!-- WSDL file generated by PHP WSDLCreator (http://www.protung.ro) -->\n";
		$this->WSDL .= $this->WSDLXML->getXML();
	}
	
	/**
	 * Get the WSDL
	 *
	 * @return string
	 */
	public function getWSDL () {
		return $this->WSDL;
	}
	
	/**
	 * Print the WSDL
	 *
	 * @param bool $headers
	 */
	public function printWSDL ($headers = false) {
		if ($headers === true) {
			header("Content-Type: application/xml");
			print $this->WSDL;
			exit;
		} else {
			print $this->WSDL;
		}
	}
	
	
	/**
	 * Save the WSDL to a file
	 *
	 * @param string $targetFile
	 * @param boolean $overwrite
	 */
	public function saveWSDL ($targetFile, $overwrite = true) {
		
		if (file_exists($targetFile) && $overwrite == false) {
			$this->downloadWSDL();
		} elseif ($targetFile) {
			$fh = fopen($targetFile, "w+");
			fwrite($fh, $this->getWSDL());
			fclose($fh);
		}
	}	
	
	/**
	 * Download the WSDL
	 *
	 */
	public function downloadWSDL () {
		session_cache_limiter();
		header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=".$this->name.".wsdl");
		header("Accept-Ranges: bytes");
		header("Content-Length: " . strlen($this->WSDL));
		$this->printWSDL();
		die();
	}
}


?>