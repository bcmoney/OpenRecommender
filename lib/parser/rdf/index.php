<?php
# Testing SimpleRdfParser.

# Copyright (c) 2002-2004 Morten Frederiksen
# License: http://www.gnu.org/licenses/gpl
define('RDFAPI_INCLUDE_DIR', '/rap/api/');
require_once('SimpleRdfParser.class.php');

error_reporting(0); //suppress error reporting

$uri = (!empty($_REQUEST['uri'])) ? $_REQUEST['uri'] : 'http://www.wasab.dk/morten/2004/05/SimpleRdfParser-example-in.rdf';//'foaf.rdf';
$format = (!empty($_REQUEST['f'])) ? strtolower($_REQUEST['f']) : 'data';
$rdf_object = '';
$DEBUG = true;

/* 
 * the function that will recursivly search an array. 
 */
function searchArrayRecursive($needle, $haystack) {
  // loop through the haystack that has been passed in 
  foreach ($haystack as $key => $arr) {
    // check to make sure that the element is an array 
    if(is_array($arr)) {
      $ret = searchArrayRecursive($needle, $arr); //recursive call
	  // check to make sure that the function call did not return -1 and return the value of the $key and the $ret      
      if($ret!=-1) { return $key.','.$ret; }
    }
	else {
      // check the array element and see if it matches the search term. if it does, return the $key of the element.
      if($arr == $needle) { return (String)$key; }
    }
  }
  // nothing was found, return -1 
  return -1; 
}


# Parse and reserialise.
$p = new SimpleRdfParser();
$rdf = @file($uri);
if(is_array($rdf)) {
    $rdf = join('', $rdf);
	//convert XML string to Triples (PHP Array object)
    if(is_array($rdf_object = @$p->string2triples($rdf, $uri))) {
		$xml = $p->triples2string(); //convert Triples back to XML string
		if($DEBUG) {
			print('<pre>');
				print_r($rdf_object);
			print('</pre>');			
		}
		else if($format == 'rdf' || $format == 'xml' || $format == 'rdf/xml') {
			header('Content-Type: application/rdf+xml');			
			print($xml);
		}
		else {		
			$findWhat = "http://xmlns.com/foaf/0.1/name"; //set the value to find in the array 			
			$result = searchArrayRecursive($findWhat, $myArray); //call the function with the needle and haystack. 
			// check to make sure the returned result was not -1 
			if($result != -1) { 			  
			  $result = explode(',', $result); //create the result array from the string returned from the function
			  //loop through the array to create an array format string such as $array[this][that][0]
			  foreach($result as $element) { 				
				$result .= '['.$element.']'; //append the element to the $result string
			  }
			}
			//check to make sure the result of the function is not -1 
			if($result != -1) {
			  echo 'Found '.$findWhat.'! '.$result; //tell us we found what we were looking for. 
			}
			else { 			  
			  echo 'Couldn\'t Find <b>\''.$findWhat.'\'</b> in the array.'; //tell us that we didnt find any array keys matching the search term.
			}
		}
    }
}
else {
    $error = 'Unable to fetch from: ' . $uri;
	echo $error;
}

?>