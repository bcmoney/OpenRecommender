<?php
require 'MicroFormatParser.php';

$url = (!empty($_REQUEST['url'])) ? $_REQUEST['url'] : 'testpage.html';
$html = file_get_contents($url);

// Instantiate the parser.
$mfParser = new MicroFormatParser();

// Set parser options.
$mfParser->parserSetup (array (
	'hcard' => true,
	'hreview' => true,
	'hcalendar' => true,
	'reltag' => true,
));
// Parse
$mf = $mfParser->parseSource($html);

// Output the resulting xArray
echo '<pre>';
$mf->each('
	echo "<h1>".get_class($value)."</h1>";
	var_export($value);
	echo "<hr />";
');
/*
### See documentation for xArray on more ways to manipulate the data ###
*/
?>