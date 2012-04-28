<?php

/**
 * Project:     PHP WSDL generator
 * File:        PHPParser.php
 * Purpose		Parse PHP files to get an array of the classes with details
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
 * @version 1.2.2
 */ 

class PHPParser {
	
	
	/**
	 * Array with all the files to be parsed
	 *
	 * @var array
	 */
	private $files = array();
	
	/**
	 * Classes to be ignored on parsing
	 *
	 * @var array
	 */
	private $ignoredClasses = array();
	
	/**
	 * Methods to be ignored on parsing
	 *
	 * @var array
	 */
	private $ignoredMethods = array();
	
	/**
	 * Auto ignore all public methods
	 *
	 * @var boolean
	 */
	private $ignorePublic = false;
	
	/**
	 * Auto ignore all protected methods
	 *
	 * @var boolean
	 */
	private $ignoreProtected = true;
	/**
	 * Auto ignore all private methods
	 *
	 * @var boolean
	 */
	private $ignorePrivate = true;
	
	/**
	 * Auto ignore all static methods
	 *
	 * @var boolean
	 */
	private $ignoreStatic = false;
	
	/**
	 * Array holding all the classes
	 *
	 * @var array
	 */
	private $classes = array();
	
	/**
	 * Array holding found classes but with errors (only classes withoutmethods for now) 
	 *
	 * @var array
	 */
	private $foundClasses = array();
	
	/**
	 * Array holding all the classes variables
	 *
	 * @var array
	 */
	private $classesVars = array();
	
	/**
	 * Array holding all the data
	 *
	 * @var array
	 */
	private $allData = array();
	
	/**
	 * Current class that is parsed
	 *
	 * @var string
	 */
	private $currentClass;
	
	/**
	 * The latest comment found for a method
	 *
	 * @var string
	 */
	private $currentMethodComment;
	
	/**
	 * The latest type found for a method
	 *
	 * @var string
	 */
	private $currentMethodType;
	/**
	 * The latest method found for a class
	 *
	 * @var string
	 */
	private $currentMethod;
	/**
	 * Latest parameters found for a method
	 *
	 * @var array
	 */
	private $currentParams = array();
	
	/**
	 * The variable that holds the XML
	 *
	 * @var XMLCreator
	 */
	private $WSDL;
	
	/**
	 * Messages for the WSDL
	 *
	 * @var array
	 */
	private $WSDLMessages = array();
	
	/**
	 * Bindings for the WSDL
	 *
	 * @var array
	 */
	private $bindings = array();
	
	/**
	 * PortTypes for the WSDL
	 *
	 * @var array
	 */
	private $portTypes = array();
	
	/**
	 * Services for the WSDL
	 *
	 * @var array
	 */
	private $WSDLService = array();
	
	/**
	 * Constructor
	 *
	 */
	public function __construct () {

	}
	
	/**
	 * Unignore all.
	 * All ignored items will be removed (including method types)
	 *
	 */
	public function ignoreNone () {
		$this->ignoredClasses = array();
		$this->ignoredMethods = array();
		$this->ignorePrivate = array();
		$this->ignoreProtected = array();
		$this->ignorePublic = array();
		$this->ignoreStatic = array();
	}
	
	/**
	 * Ignore or not all public methods
	 *
	 * @param boolean $ignore
	 */
	public function ignorePublic ($ignore = false) {
		if ($ignore === true) {
			$this->ignorePublic = true;
		} elseif ($ignore === false) {
			$this->ignorePublic = false;
		}
	}
	
	/**
	 * Ignore or not all protected methods
	 *
	 * @param boolean $ignore
	 */
	public function ignoreProtected ($ignore = false) {
		if ($ignore === true) {
			$this->ignoreProtected = true;
		} elseif ($ignore === false) {
			$this->ignoreProtected = false;
		}
	}
	
	/**
	 * Ignore or not all private methods
	 *
	 * @param boolean $ignore
	 */
	public function ignorePrivate ($ignore = false) {
		if ($ignore === true) {
			$this->ignorePrivate = true;
		} elseif ($ignore === false) {
			$this->ignorePrivate = false;
		}
	}
	
	/**
	 * Ignore or not all static methods
	 *
	 * @param boolean $ignore
	 */
	public function ignoreStatic ($ignore = false) {
		if ($ignore === true) {
			$this->ignoreStatic = true;
		} elseif ($ignore === false) {
			$this->ignoreStatic = false;
		}
	}
	
	/**
	 * Add a class name to ignore on parsing
	 *
	 * @param string $class
	 */
	public function ignoreClass ($class) {
		$this->ignoredClasses[] = $class;
	}
	
	/**
	 * Add classes to ignor on parsing
	 *
	 * @param array $classes
	 */
	public function ignoreClasses ($classes) {
		if (is_array($classes)) {
			foreach ($classes as $class) {
				$this->ignoreClass($class);
			}
		}
	}
	
	/**
	 * Add a method of a class to ignore on parsing
	 *
	 * @param array $method
	 */
	public function ignoreMethod ($method) {
		if (is_array($method)) {
			$this->ignoredMethods[key($method)][] = $method[key($method)];
		}
	}
	
	/**
	 * Add methods of classes to ignore on parsing
	 *
	 * @param array $methods
	 */
	public function ignoreMethods ($methods) {
		if (is_array($methods)) {
			foreach ($methods as $class=>$method) {
				if ($class != "" && $method != "")
					$this->ignoredMethods[$class][] = $method;
			}
		}
	}
	
	/**
	 * Add a file to parse
	 *
	 * @param string $file
	 */
	public function addFile ($file) {
		if (file_exists($file)) {
			$this->files[] = $file;
		} else {
			trigger_error("File <b>".$file."</b> does not exist !!", E_USER_ERROR);
		}
	}
	
	/**
	 * Return the next token resulted alfter token_get_all()
	 *
	 * @return array
	 */
	private function getNextToken () {
		if (is_array($this->allData)) {
			while (($c = next($this->allData))) {
				if (!is_array($c) || $c[0] == T_WHITESPACE) { // 370
					continue;
				}
				break;
			}
			return current($this->allData);
		}
		return false;
	}
	
	/**
	 * Return the previous token exept white space
	 *
	 * @return array
	 */
	private function getPrevToken () {
		if (is_array($this->allData)) {
			while (($c = prev($this->allData))) {
				if (!is_array($c) || $c[0] == T_WHITESPACE) { // 370
					continue;
				}
				break;
			}
			return current($this->allData);
		}
		return false;
	}
	
	/**
	 * Get next token with a type
	 *
	 * @param integer $type
	 * @return array
	 */
	private function getNextTokenWithType ($type) {
		while (($current = $this->getNextToken())) {
			if($current[0] == $type) {
				return current($this->allData);
			}
		}
		return array();
	}
	
	/**
	 * Parse a file
	 * It gets the data from $this->all_data
	 *
	 */
	private function parseFile () {
		$lookForClassVariables = true; // When this will be set as false we will not look for class variables because a function was defined
		while (($token = $this->getNextToken())) {
//			print_r($this->allData);exit;
			
			if ($token[0] == T_CLASS) { // T_CLASS
				$className = $this->getNextTokenWithType(T_STRING);
				$this->currentClass = $className[1];
				$this->currentMethodComment = $this->currentMethodType = $this->currentMethod = $this->currentParams = null;
				continue;
			}

			if ($lookForClassVariables === true && $token[0] == T_VARIABLE && $this->currentClass != null) {
				$varName = substr($token[1], 1);
				$this->classesVars[$this->currentClass][$varName] = "";
				continue;
			}
			
			if ($token[0] == T_DOC_COMMENT) { // T_DOC_COMMENT
				$nt = $this->getNextToken();
				
				if ($nt[0] == T_FUNCTION || $nt[0] == T_STATIC || $nt[0] == T_ABSTRACT || $nt[0] == T_FINAL || 
					$nt[0] == T_PRIVATE || $nt[0] == T_PROTECTED || $nt[0] == T_PUBLIC) { // public | protected | private | final | abstract | static | function
					
					$nnt = $this->getNextToken();
					if ($nnt[0] == T_VARIABLE) {
						$varName = substr($nnt[1], 1);
						$this->getPrevToken();
						$varType = $this->getPrevToken();
						if ($varType[0] == T_DOC_COMMENT) {
							$varType = $this->parseComment($varType[1]);
							$varType = $varType['params']['type'];
							$this->classesVars[$this->currentClass][$varName] = $varType;
						}
						continue;
					} else {
						$this->getPrevToken();
					}
					$this->currentMethodComment = $token[1];
					$this->currentMethod = null;
					$this->currentParams = null;
					$this->getPrevToken();
					continue;
				}
			}
			
			if (isset($nt) && ($nt[0] == T_STATIC || $nt[0] == T_ABSTRACT || $nt[0] == T_FINAL || 
				$nt[0] == T_PRIVATE || $nt[0] == T_PROTECTED || $nt[0] == T_PUBLIC)) { // public | protected | private | final | abstract | static
				$this->currentMethodType = $token[1] ? $token[1] : "public";
				$this->currentMethod = $this->currentParams = null;
				$token = $this->getNextToken();
			} else {
				$this->currentMethodType = "public";
			}
			
			if ($token[0] == T_FUNCTION) { // T_FUNCTION
				$lookForClassVariables = false;
				$f = $this->getNextTokenWithType(T_STRING);
				$this->currentMethod = $f[1];
				$this->currentParams = null;
				if (next($this->allData) == "(") {
					while (($p = next($this->allData)) != ")") {
						if ($p[0] == T_VARIABLE) { // T_VARIABLE
							$this->currentParams[] = $p[1];
						}
					}
				}
			}
			
			if (!isset($this->classes[$this->currentClass])) {
				$this->foundClasses[$this->currentClass] = $this->currentClass;
			}
			
			if ($this->currentClass && $this->currentMethod) {
				$this->classes[$this->currentClass][$this->currentMethod]["comment"] = $this->currentMethodComment;
				if ($this->currentMethod == null) $this->currentMethod = "public";
				$this->classes[$this->currentClass][$this->currentMethod]["type"] = $this->currentMethodType;
				$this->classes[$this->currentClass][$this->currentMethod]["params"] = $this->currentParams;
				$this->currentMethodComment = $this->currentMethodType = $this->currentMethod = $this->currentParams = null;
			}
		}
	}
	
	/**
	 * Filter classes
	 * Extracts all the ignored classes and methods and methods types
	 *
	 */
	private function filterClasses () {
		foreach ($this->classes as $class=>$methods) {
			if (in_array($class, $this->ignoredClasses)) {
				unset($this->classes[$class]);
				continue;
			}
			
			foreach ($methods as $method=>$attrs) {
					
				if (($attrs["type"] == "public" && $this->ignorePublic === true) ||
					($attrs["type"] == "protected" && $this->ignoreProtected === true) ||
					($attrs["type"] == "private" && $this->ignorePrivate === true) ||
					($attrs["type"] == "static" && $this->ignoreStatic === true))
				{
					unset($this->classes[$class][$method]);
				}
				
				if (isset($this->ignoredMethods[$class]) && is_array($this->ignoredMethods[$class])) {
					if (in_array($method, $this->ignoredMethods[$class])) {
						unset($this->classes[$class][$method]);
					}
				}
			}

		}
	}
	
	/**
	 * Parse a comment
	 * Extracts description, parameters type and return type
	 *
	 * @param string $comment
	 * @return array
	 */
	private function parseComment ($comment) {
		$comment = trim($comment);
		if ($comment == "") return "";
		
		if (strpos($comment, "/*") === 0 && strripos($comment, "*/") === strlen($comment)-2) {
			$lines = preg_split("(\\n\\r|\\r\\n\\|\\r|\\n)", $comment);
			$description = "";
			$returntype = "";
			$params = array();
			while (next($lines)) {
				$line = trim(current($lines));
				$line = trim(substr($line, strpos($line, "* ")+2));
				if (isset($line[0]) && $line[0] == "@") {
					$parts = explode(" ", $line);
					if ($parts[0] == "@return") {
						$returntype = $parts[1];
					} elseif ($parts[0] == "@param") {
						$params[$parts[2]] = $parts[1];
					} elseif ($parts[0] == "@var") {
						$params['type'] = $parts[1];
					}
				} else {
					$description .= "\n".trim($line);
				}
			}
			
			$comment = array("description"=>$description, "params"=>$params, "return"=>$returntype);
			return $comment;
		} else {
			return "";
		}
		
	}
	
	/**
	 * Parse the classes
	 *
	 */
	private function parseClasses () {
		$classes = $this->classes;
		$this->classes = array();
		foreach ($classes as $class=>$methods) {
			foreach ($methods as $method=>$attributes) {
				$this->classes[$class][$method]["type"] = $attributes["type"];
				$commentParsed = $this->parseComment($attributes["comment"]);
				$this->classes[$class][$method]["returnType"] = !isset($commentParsed["return"]) ? false : $commentParsed["return"];
				$this->classes[$class][$method]["description"] = isset($commentParsed["description"]) ? $commentParsed["description"] : "";
				if (is_array($attributes["params"])) {
					foreach ($attributes["params"] as $param) {
						$paramName = substr($param, 1);
						$this->classes[$class][$method]["params"][$paramName]["varName"] = $param;
						if (isset($commentParsed["params"][$param]))
							$this->classes[$class][$method]["params"][$paramName]["varType"] = $commentParsed["params"][$param];
					}
				}
			}
		}
	}
	
	/**
	 * Get all the parsed classes from the files (filtered)
	 *
	 * @return array
	 */
	public function getClasses () {
		foreach ($this->files as $file) {
			$this->allData = token_get_all(file_get_contents($file));
			$this->parseFile(file_get_contents($file));
		}
		$this->filterClasses();
		$this->parseClasses();
		return $this->classes;
	}
	
	/**
	 * Get all found classes (after parsing)
	 *
	 * @return array
	 */
	public function getFoundClasses () {
		return $this->foundClasses;
	}
	
	/**
	 * Get all the variables of the classes defined in the files
	 *
	 * @return array
	 */
	public function getClassesVars () {
		return $this->classesVars;
	}

}

?>