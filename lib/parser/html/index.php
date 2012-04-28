<?php
require_once 'htmlpurifier/HTMLPurifier.standalone.php';
require_once 'simplehtmldom/simple_html_dom.php';
    
function scrape($url, $path, $parse) {
	
	$config = HTMLPurifier_Config::createDefault();
		$config->set('Core.Encoding', 'UTF-8'); //encoding of output
		$config->set('HTML.Doctype', 'XHTML 1.1'); //doctype of output
	$purifier = new HTMLPurifier($config);
	
	$dirty_html = file_get_contents($url);	
    $clean_html = $purifier->purify($dirty_html);
	$html = str_get_html($clean_html);
	
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
	unset($dirty_html);
	unset($clean_html);
	unset($html);
	
    return $ret;
}


// -----------------------------------------------------------------------------
$url = (!empty($_REQUEST['url'])) ? $_REQUEST['url'] : 'http://www.openrecommender.org';
$path = (!empty($_REQUEST['path'])) ? $_REQUEST['path'] : 'a';
$parse = (!empty($_REQUEST['parse'])) ? $_REQUEST['parse'] : 'default';
$output = (!empty($_REQUEST['output'])) ? $_REQUEST['output'] : 'attr';
$debug = (!empty($_REQUEST['debug'])) ? $_REQUEST['debug'] : false;

$text = scrape($url,$path,$parse);
	if($debug) {
		echo "<hr/><pre>";
		print_r($text);
		echo "</pre><hr/>";
	}
foreach($text as $k=>$v) {
    echo '<strong>'.$k.':</strong> '.$v;
	foreach($v->$output as $val=>$t) {
		echo ' | '.$t.'<br/>';
	}
}

?>