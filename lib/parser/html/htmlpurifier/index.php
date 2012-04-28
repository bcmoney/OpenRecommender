<?php
    require_once 'HTMLPurifier.standalone.php';
    
    $config = HTMLPurifier_Config::createDefault();
		$config->set('Core.Encoding', 'ISO-8859-1'); // encoding of output
		$config->set('HTML.Doctype', 'XHTML 1.1'); // doctype of output
    $purifier = new HTMLPurifier($config);
    
	$url = (!empty($_REQUEST['url'])) ? $_REQUEST['url'] : 'http://openrecommender.org';
	$dirty_html = file_get_contents($url);
	
    $clean_html = $purifier->purify($dirty_html);
	
	echo $clean_html;
?>