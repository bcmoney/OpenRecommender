<?php
/**
 * Class xArray - eXtended arrays.
 * 
 * This class allows for a different way of array manipulation, 
 * especially facilitating the operations on the 
 * arrays of objects (this may come in handy when working with, 
 * say, database results). 
 * It is modeled after Enumerable/Array objects of
 * prototype.js javascript library, and incorporates most
 * of the functionallity found there. Plus, most methods accept 
 * a boolean $inPlace argument that replaces the working 
 * (current) xArray with the method result.
 * 
 * A note about the "lambda/callback" datatype that you'll find in 
 * the comments ($iterator arguments) - this is either a valid 
 * PHP callback, or a string containing valid PHP function body
 * (ie. can be passed as an argument to create_function). 
 * 
 * Created on 13-Nov-06
 *
 * @package xArray
 * @author Vladislav Bailovic <malatestapunk@gmail.com>
 */

class xArray {
	
	/**
	 * Internal storage.
	 * You shouldn't use it directly.
	 * 
	 * @access private
	 */
	var $_store = array();
	
	/**
	 * Constructor.
	 * 
	 * @param mixed $objectArray Either an array, or a list of arguments
	 */
	function xArray ($objectArray=false) {
		$passedArray = func_get_args();
		if (!is_array($objectArray)) {
			$objectArray = (count ($passedArray) > 1) ? $passedArray : array();
		} 
		$this->_store = $objectArray;  
		$this->reset();
	}
	
	/**
	 * Appends value/array of values to the end of the array.
	 * 
	 * @param mixed $what Will append it to the end of the xArray
	 */
	function append ($what) {
		if (is_array($what)) return $this->appendArray($what);
		else return $this->appendSingle($what);		
	}
	
	/**
	 * Appends array.
	 * You *could* use this method yourself, but there is no need
	 * to do so - append() will do the datatype guesswork for you.
	 * 
	 * @param array $which Array to append
	 */
	function appendArray ($which) {
		if (!is_array($which)) return false;
		foreach ($which as $ind=>$obj) $this->append ($obj, $ind);		
	}
	
	/**
	 * Appends single value.
	 * You *could* use this method yourself, but there is no need
	 * to do so - append() will do the datatype guesswork for you.
	 * 
	 * @param scalar What What to append
	 * @param int $where Where to append
	 */
	function appendSingle ($what, $where=false) {
		if (is_array($what)) return false;
		if (false === $where) $this->_store[] = $what;
		else $this->_store[$where] = $what;
		return true;
	}
	
	/**
	 * Prepends value/array of values at the beginning of the array.
	 * 
	 * @param mixed $what What to prepend
	 */
	function prepend ($what) {
		if (is_array($what)) return $this->prependArray($what);
		else return $this->prependSingle($what);	
	}
	
	/**
	 * Prepends array.
	 * You *could* use this method yourself, but there is no need
	 * to do so - prepend() will do the datatype guesswork for you.
	 * 
	 * @param array $which Array to prepend
	 */
	function prependArray ($which) {
		if (!is_array($which)) return false;
		$l1 = $this->length();
		$l2 = count ($which);
		$this->_store = array_merge ($which, $this->_store);
		return ($this->length() == $l1+$l2) ? true : false;
	}
	
	/**
	 * Prepends single value.
	 * You *could* use this method yourself, but there is no need
	 * to do so - prepend() will do the datatype guesswork for you.
	 * 
	 * @param scalar $what What to prepend
	 */
	function prependSingle ($what) {
		if (is_array($what)) return false;
		return $this->prependArray (array($what));
	}
	
	/**
	 * Returns xArray length;
	 * 
	 * @return int xArray length
	 */
	function length () {
		return count($this->_store);
	}
	
	/**
	 * Returns reversed xArray.
	 * If passed $inPlace argument, reverses xArray itself.
	 * 
	 * @param bool $inPlace If set, operation takes place on the xArray itself
	 * @return xArray Reversed xArray
	 */
	function reverse ($inPlace=false) {
		$return = $this->_store;
		$return = array_reverse($return);
		if ($inPlace !== false) {
			$this->_store = $return;
			$this->reset();
		}
		return new xArray($return);
	}
	
	/**
	 * Resets xArray iteration pointer.
	 */
	function reset () {
		reset ($this->_store);
	}
	
	/**
	 * Returns first element of the xArray.
	 */
	function first () {
		return reset ($this->_store);
	}
	
	/**
	 * Returns last element of the xArray.
	 * 
	 * @return mixed Last xArray element
	 */
	function last () {
		$item = end ($this->_store);
		reset ($this->_store);
		return $item;
	}
	
	/**
	 * Returns next element of the xArray.
	 * 
	 * @return mixed Value of the curent xArray member
	 */
	function fetch () {
		$item = each ($this->_store);
		if ($item) { 
			list($index, $value) = $item;
			return $value;	
		} else {
			$this->reset();
		}
		return false;
	}
	
	/**
	 * Calls $iterator callback/lambda on every member of xArray.
	 * 
	 * @param lambda/callback $iterator Lambda/callback function
	 */
	function each ($iterator) {
		if (!is_callable($iterator)) $iterator = create_function ('$value, $index', $iterator);
		foreach ($this->_store as $index=>$value) {
			call_user_func_array ($iterator, array($value, $index));
		}
	}
	
	/**
	 * True if all true.
	 * Returns true if all xArray members return true form supplied
	 * $iterator callback/lambda.
	 * Second parameter forces strict testing.
	 * 
	 * @param lambda/callback $iterator Lambda/callback function
	 * @param bool $strict Forces strict testing
	 * @return bool True if all true, false otherwise
	 */
	function all ($iterator, $strict=false) {
		if (!is_callable($iterator)) $iterator = create_function ('$value, $index', $iterator);
		foreach ($this->_store as $index=>$value) {
			$ret = call_user_func_array ($iterator, array($value, $index));
			if ($strict) {
				if ($ret === false) return false;
			} else {
				if (!$ret) return false;
			}
		}
		return true;
	}
	
	/**
	 * True if any is true.
	 * Returns true if any one of xArray members returns true form supplied
	 * $iterator callback/lambda.
	 * Second parameter forces strict testing.
	 * 
	 * @param lambda/callback $iterator Lambda/callback function
	 * @param bool $strict Forces strict testing
	 * @return bool True if any returns true, false otherwise
	 */
	function any ($iterator, $strict=false) {
		$returnValue = false;
		if (!is_callable($iterator)) $iterator = create_function ('$value, $index', $iterator);
		foreach ($this->_store as $index=>$value) {
			$ret = call_user_func_array ($iterator, array($value, $index));
			if ($strict) {
				if ($ret === true) $returnValue = true;
			} else {
				if ($ret) $returnValue = true;
			}
		}
		return $returnValue;
	}
	
	/**
	 * Calls $iterator callback/lambda on each xArray member
	 * and returns result as xArray.
	 * If passed $inPlace attribute, result replaces current xArray.
	 * 
	 * @param lambda/callback $iterator Lambda/callback function
	 * @param bool $inPlace If set, operation takes place on the xArray itself
	 * @return xArray Resulting xArray
	 */
	function collect ($iterator, $inPlace=false) {
		$result = array();
		if (!is_callable($iterator)) $iterator = create_function ('$value, $index', $iterator);
		foreach ($this->_store as $index=>$value) {
			$result[$index] = call_user_func_array ($iterator, array($this->_makeValueClone($value), $index));
		}
		if ($inPlace !== false) {
			$this->_store = $result;
			$this->reset();
		}
		return new xArray($result);
	}
	
	/**
	 * Same as collect().
	 */
	function map ($iterator, $inPlace=false) {return $this->collect($iterator, $inPlace);}
	
	/**
	 * Returns first xArray member that returns true from $iterator 
	 * callback/lambda. False if none found.
	 * Second parameter forces strict testing.
	 * 
	 * @param lambda/callback $iterator Lambda/callback function
	 * @param bool $strict Forces strict testing
	 * @return mixed First xArray member that returns true
	 */
	function detect ($iterator, $strict=false) {
		$returnValue = false;
		if (!is_callable($iterator)) $iterator = create_function ('$value, $index', $iterator);
		foreach ($this->_store as $index=>$value) {
			$ret = call_user_func_array ($iterator, array($value, $index));
			if ($strict) {
				if ($ret === true) return $value;
			} else {
				if ($ret) return $value;
			}
		}
		return false;
	}
	
	/**
	 * Same as detect().
	 */
	function find ($iterator, $strict=false) {return $this->detect($iterator,$strict);}
	
	/**
	 * Tests xArray members against supplied regular expression pattern.
	 * Returns matching results as xArray.
	 * If $iterator callback/lambda given, result contains return values
	 * of $iterator for each match.
	 * If given $inPlace attribute, will replace current xArray with result.
	 * 
	 * @param string $pattern Pattern to match against
	 * @param lambda/callback $iterator Lambda/callback function
	 * @param bool $inPlace If set, operation takes place on the xArray itself
	 * @return xArray Resulting xArray
	 */
	function grep ($pattern, $iterator=false, $inPlace=false) {
		$result = array();
		if ($iterator && !is_callable($iterator)) $iterator = create_function ('$value, $index', $iterator);
		foreach ($this->_store as $index=>$value) {
			if (is_string($value) && preg_match ($pattern, $value)) { 
				$result[$index] = ($iterator) ? 
					call_user_func_array ($iterator, array($this->_makeValueClone($value), $index))
					:
					$value;
			}
		}
		if ($inPlace !== false) {
			$this->_store = $result;
			$this->reset();
		}
		return new xArray($result);
	}
	
	/**
	 * Checks xArray members for a pattern.
	 * 
	 * @param string $pattern Pattern to check
	 * @return bool True if match found, false otherwise
	 */
	function has ($pattern) {
		$test = $this->grep($pattern);
		return ($test->length()>0) ? true : false;	
	}
	
	/**
	 * Tests xArray keys against supplied regular expression pattern.
	 * Returns matching results as xArray.
	 * If $iterator callback/lambda given, result contains return values
	 * of $iterator for each match.
	 * If given $inPlace attribute, will replace current xArray with result.
	 * 
	 * @param string $pattern Pattern to check keys against
	 * @param lambda/callback $iterator Lambda/callback function
	 * @param bool $inPlace If set, operation takes place on the xArray itself
	 * @return xArray Resulting xArray
	 */
	function grepKeys ($pattern, $iterator=false, $inPlace=false) {
		$result = array();
		if ($iterator && !is_callable($iterator)) $iterator = create_function ('$value, $index', $iterator);
		foreach ($this->_store as $index=>$value) {
			if (preg_match ($pattern, $index)) { 
				$result[$index] = ($iterator) ? 
					call_user_func_array ($iterator, array($this->_makeValueClone($value), $index))
					:
					$value;
			}
		}
		if ($inPlace !== false) {
			$this->_store = $result;
			$this->reset();
		}
		return new xArray($result);
	}
	
	/**
	 * Checks xArray members' keys for a pattern.
	 * 
	 * @param string $pattern Pattern to check
	 * @return bool True if match found, false otherwise
	 */
	function hasKey ($pattern) {
		$test = $this->grepKeys($pattern);
		return ($test->length()>0) ? true : false;
	}
	
	/**
	 * Calls $methodName method on each xArray object member.
	 * Returns results as xArray.
	 * Optional arguments array is passed to each method.
	 * 
	 * @param string $methodName Method name to invoke
	 * @param array $args Arguments to pass to the invoked method
	 * @return xArray Resulting xArray
	 */
	function invoke ($methodName, $args=array()) {
		$return = array(); 
		if (is_object($args) && get_class($this) == get_class($args)) $args = $args->toArray();
		foreach ($this->_store as $index=>$value) {
			if (is_object($value) && method_exists($value, $methodName))
				$return[$index] = call_user_func_array (array(&$value, $methodName), $args);
		}
		return new xArray($return);
	}
	
	/**
	 * Calls $methodName method of a particular xArray member.
	 * 
	 * @param string $methodName Method to invoke
	 * @param string/int $id xArray member identifier
	 * @param array $args Arguments to pass to the invoked method
	 * @return mixed Return value of the invoked method
	 */
	function invokeSingle ($methodName, $id, $args=array()) {
		$item = $this->get($id);
		if (is_object($item) && method_exists($item, $methodName)) 
			return call_user_func_array (array(&$item, $methodName), $args);
	}

	/**
	 * Returns the element with the greatest result of calling the $iterator
	 * callback/lambda for each xArray element, if given. Else returns element
	 * with greatest value.
	 * 
	 * @param lambda/callback $iterator Lambda/callback function
	 * @return mixed Member with maximum result
	 */
	function max ($iterator=false) {
		$return = array();
		if (!$iterator) $iterator = 'return $value;';
		if (!is_callable($iterator)) $iterator = create_function ('$value, $index', $iterator);
		foreach ($this->_store as $index=>$value) {
			$return[$index] = call_user_func_array ($iterator, array($value, $index));
		}
		@arsort($return);
		$key = key($return);
		return $this->get($key);
	}
	
	/**
	 * Returns the element with the smallest result of calling the $iterator
	 * callback/lambda for each xArray element, if given. Else returns element
	 * with smallest value.
	 * 
	 * @param lambda/callback $iterator Lambda/callback function
	 * @return mixed Member with minimum result
	 */
	function min ($iterator=false) {
		$return = array();
		if (!$iterator) $iterator = 'return $value;';
		if (!is_callable($iterator)) $iterator = create_function ('$value, $index', $iterator);
		foreach ($this->_store as $index=>$value) {
			$return[$index] = call_user_func_array ($iterator, array($value, $index));
		}
		@asort($return);
		$key = key($return);
		return $this->get($key);
	}
	
	/**
	 * Returns xArray with all elements that return true-a-like value
	 * from $iterator callback/lambda. Loose testing.
	 * If given $inPlace parameter, replaces current xArray.
	 * 
	 * @param lambda/callback $iterator Lambda/callback function
	 * @param bool $inPlace If set, operation takes place on the xArray itself
	 * @return xArray Resulting xArray
	 */
	function select ($iterator, $inPlace=false) {
		$return = array();
		if (!is_callable($iterator)) $iterator = create_function ('$value, $index', $iterator);
		foreach ($this->_store as $index=>$value) {
			$ret = call_user_func_array ($iterator, array($this->_makeValueClone($value), $index));
			if ($ret) $return[$index] = $value;
		}
		if ($inPlace !== false) {
			$this->_store = $return;
			$this->reset();
		}
		return new xArray($return);
	}
	
	/**
	 * Returns xArray with all elements that return false-a-like value
	 * from $iterator callback/lambda. Loose testing.
	 * If given $inPlace parameter, replaces current xArray.
	 * 
	 * @param lambda/callback $iterator Lambda/callback function
	 * @param bool $inPlace If set, operation takes place on the xArray itself
	 * @return xArray Resulting xArray
	 */
	function reject ($iterator, $inPlace=false) {
		$return = array();
		if (!is_callable($iterator)) $iterator = create_function ('$value, $index', $iterator);
		foreach ($this->_store as $index=>$value) {
			$ret = call_user_func_array ($iterator, array($this->_makeValueClone($value), $index));
			if (!$ret) $return[$index] = $value;
		}
		if ($inPlace !== false) {
			$this->_store = $return;
			$this->reset();
		}
		return new xArray($return);
	}
	
	/**
	 * Returns xArray with all elements' $propertyName properties. 
	 * If given $inPlace parameter, replaces current xArray.
	 * 
	 * @param string $propertyName Name of the members' property to pluck
	 * @param bool $inPlace If set, operation takes place on the xArray itself
	 * @return xArray Resulting xArray
	 */
	function pluck ($propertyName, $inPlace=false) {
		$return = array();
		foreach ($this->_store as $index=>$value) {
			if (is_object($value) && isset($value->$propertyName)) $return[$index] = $value->$propertyName;
		}
		if ($inPlace !== false) {
			$this->_store = $return;
			$this->reset();
		}
		return new xArray($return);
	}
	
	/**
	 * Returns a property of a single, particular member.
	 * 
	 * @param string $propertyName Name of the property to pluck
	 * @param string/int $id xArray member identifier
	 * @return mixed Property value
	 */
	function pluckSingle ($propertyName, $id) {
		$item = $this->get($id);
		if (is_object($item) && isset($item->$propertyName)) return $item->$propertyName;
		return false;
	}
	
	/**
	 * Returns xArray sorted by $iterator callback/lambda result.
	 * If given $inPlace parameter, replaces current xArray.
	 * 
	 * @param lambda/callback $iterator Lambda/callback function
	 * @param bool $inPlace If set, operation takes place on the xArray itself
	 * @return xArray Resulting xArray
	 */
	function sortBy ($iterator, $inPlace=false) {
		if (!is_callable($iterator)) $iterator = create_function ('$a, $b', $iterator);
		$return = $this->_store;
		usort ($return, $iterator);
		if ($inPlace !== false) {
			$this->_store = $return;
			$this->reset();
		}
		return new xArray($return);
	}
	
	/**
	 * Returns xArray as simple array.
	 * 
	 * @return array xArray array representation
	 */
	function toArray () {
		return $this->_store;
	}
	
	/**
	 * Returns xArray as string.
	 * 
	 * @return string xArray string representation
	 */
	function toString ($separator=', ') {
		return @implode ($separator, $this->_store);
	}
	
	/**
	 * Returns xArray $id member.
	 * 
	 * @param int/string $id xArray member identifier
	 * @return mixed xArray member
	 */
	function get ($id) {
		if (isset($this->_store[$id])) return $this->_store[$id];
		else return false;
	}
	
	/**
	 * Sets xArray $id member to $value.
	 * Optional third parameter forces overwrite (defaults to true).
	 * 
	 * @param string/int $id xArray member identifier
	 * @param mixed $value New value to set
	 * @param bool $force Force overwrite (defaults to true)
	 * @return bool True on success, false otherwise
	 */
	function set ($id, $value, $force=true) {
		if ((isset($this->_store[$id]) && $force) || !isset($this->_store[$id])) {
			$this->_store[$id] = $value;
			return true;
		} else if (isset($this->_store[$id]) && !$force) {
			return false;
		}
	}
	
	/**
	 * Unsets xArray $id member.
	 * 
	 * @param string/int xArray member identifier
	 * @return bool True on success, false otherwise
	 */
	function remove ($id) {
		if (false === $id) return false;
		if (isset($this->_store[$id])) {
			unset($this->_store[$id]);
			return true;
		}
		else return false;
	}
	
	/**
	 * Clears xArray members.
	 */
	function clear () {
		$this->_store = array();
		return true;
	}
	
	/**
	 * Returns xArray index for $value.
	 * Optional third parameter forces strict matching. 
	 * 
	 * @param mixed $val Value to find
	 * @param bool $strict Forces strict matching
	 * @return mixed Key value if found, (bool)false otherwise
	 */
	function indexOf ($val, $strict=false) {
		return @array_search($val, $this->_store, $strict);
		
	}
	
	/**
	 * Returns xArray reindexed and with null values removed.
	 * If given $inPlace parameter, result replaces current xArray.
	 * 
	 * @param bool $inPlace If set, operation takes place on the xArray itself
	 * @return xArray Resulting xArray
	 */
	function compact ($inPlace=false) {
		$result = array();
		foreach ($this->_store as $value) {
			if ($value !== null) $result[] = $value; 
		}
		if ($inPlace !== false) {
			$this->_store = $result;
			$this->reset();
		}
		return new xArray($result);
	}
	
	/**
	 * Returns current xArray without supplied values.
	 * 
	 * @param mixed $passedArray Either an array, xArray, or a list of values to exclude 
	 * @return xArray Resulting xArray
	 */
	function without ($passedArray) {
		if (!is_array($passedArray) && 
			(is_object($passedArray) && get_class($this)==get_class($passedArray))) 
			$passedArray = $passedArray->toArray();
		if (!is_array($passedArray)) $passedArray = func_get_args();
		$result = new xArray($this->toArray());
		foreach ($passedArray as $reject) {
			$result->remove($result->indexOf($reject));
		}
		return $result;
	}
	
	/**
	 * Returns current xArray without supplied keys.
	 * 
	 * @param mixed $passedArray Either an array, xArray, or a list of keys to exclude 
	 * @return xArray Resulting xArray
	 */
	function withoutKeys ($passedArray) {
		if (!is_array($passedArray) && 
			(is_object($passedArray) && get_class($this)==get_class($passedArray))) 
			$passedArray = $passedArray->toArray();
		if (!is_array($passedArray)) $passedArray = func_get_args();
		$result = new xArray($this->toArray());
		foreach ($passedArray as $reject) {
			$result->remove($reject);
		}
		return $result;
	}
	
	/**
	 * Extracts a portion of xArray.
	 * 
	 * @param int $start Where to start extracting
	 * @param int $len How many members to extract
	 * @param bool $inPlace If set, operation takes place on the xArray itself
	 */
	function extract ($start=0, $len=false, $inPlace=false) {
		if ($len === false) $len=$this->length() - $start;
		$result = array_slice($this->_store, $start, $len);
		if ($inPlace !== false) {
			$this->_store = $result;
			$this->reset();
		}
		return new xArray($result);
	}
	
	/* #################### PHP Compatibility Routines ################# */
	
	/**
	 * Makes object clones for PHP5. 
	 * @access private
	 */
	function _makeValueClone ($val) {
		if (phpversion() >= 5 && is_object($val)) {
			return clone($val);
		}
		return $val;
	}
}
?>
