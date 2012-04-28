<?php

class dummy {

	/**
	 * Constructor
	 *
	 * @param string $x1
	 * @return integer
	 */
	public function __construct($x1) {
		return (int)$x1;
	}
	
	/**
	 * Constructor
	 *
	 * @param string $p1
	 * @param string $p2
	 * @return string
	 */
	public function test ($p1, $p2) {
		return $p1.$p2;
	}
	/**
	 * Input param Object (complexType)
	 * Return mixed (anyType)
	 *
	 * @param XMLCreator $p1
	 * @param string $p2
	 * @return array
	 */
	function test22222 ($p1, $p2) {
		return array($p1, $p2);
	}

	/**
	 * Input param 1 mixed
	 * Input param 2 array of objects (complexType)
	 * Return array of objects
	 * 
	 * @param mixed $p1
	 * @param XMLCreator[] $p2
	 * @return XMLCreator[]
	 */
	function test33333 ($p1, $p2) {
		unset($p1);
		return array($p2);
	}
	
	/**
	 * Get current date and time
	 *
	 * @return DateTime
	 */
	function getCurrentDateTime () {
		return new DateTime();
	}
}


?>