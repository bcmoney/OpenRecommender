<?php
include_once('simple_html_dom.php');

function scrape($url, $path, $parse) {
    // create HTML DOM
    $html = file_get_html($url);
	switch($parse) {
		case 'tag':
			$ret = $html->find($path)->tag;
			break;
		case 'outertext':
			$ret = $html->find($path)->outertext;
			break;
		case 'innertext':
			$ret = $html->find($path)->innertext;
			break;
		case 'plaintext':
			$ret = $html->find($path)->plaintext;
			break;			
		default:
			$ret = $html->find($path);
			break;	
	}		
		// clean up memory
		$html->clear();
		unset($html);
    return $ret;
}


// -----------------------------------------------------------------------------
$url = (!empty($_REQUEST['url'])) ? $_REQUEST['url'] : 'http://www.openrecommender.org';
$path = (!empty($_REQUEST['path'])) ? $_REQUEST['path'] : 'title';
$parse = (!empty($_REQUEST['parse'])) ? $_REQUEST['parse'] : 'default';
$output = (!empty($_REQUEST['output'])) ? $_REQUEST['output'] : 'attr';
$debug = (!empty($_REQUEST['debug'])) ? $_REQUEST['debug'] : false;

$text = scrape($url,$path,$parse);
	if($debug) {
		echo "<pre>"; 
		print_r($text);
		echo "</pre>";
	}
foreach($text as $k=>$v) {
    echo '<strong>'.$k.':</strong> '.$v;
	foreach($v->$output as $val=>$t) {
		echo ' | '.$t.'<br/>';
	}
}

?>