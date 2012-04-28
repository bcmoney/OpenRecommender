<?php

// you can have any code in the file, not only one class

$a = array(1,2,3,4,5);
$b = array(6,7,8,9,10);

class example1 {
	
	public $param1=array();
	public $param2;
	
	/**
	 * Constructor
	 *
	 * @param string $p1
	 */
	function __construct($p1 = "") {
		$this->param2 = $p1;
	}
	

	/**
	 * Adds two numbers
	 *
	 * @param float $p1
	 * @param float $p2
	 * @return float
	 */
	protected function add($p1, $p2) {
		return ($p1+$p2);
	}
	
	/**
	 * Make array
	 *
	 * @param mixed $el1
	 * @param mixed $el2
	 * @return array
	 */
	public function makeArray ($el1, $el2) {
		return array($el1, $el2);
	}
}


class example1_1 {
	
	/**
	 * Example
	 *
	 * @var example1
	 */
	public $example1Obj;
	
	/**
	 * Constructors
	 *
	 * @param dummy $ex1
	 */
	function __construct($ex1) {
		if ($ex1 instanceof dummy)
			$this->example1Obj = $ex1;
	}
	
	/**
	 * Get the example
	 *
	 * @return example1
	 */
	final function getEx () {
		return $this->example1Obj;
	}
	
}

?>