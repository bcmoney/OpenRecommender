<?php
/**
 * This is an example implementation file
 *
 * For more info view documentation.html
 *
 * License: LGPL
 *
 * @copyright  2011 Aziz Hussain
 * @version    1.0
 * @link       http://azizsaleh.com
 * @email      azizsaleh@gmail.com
 */ 
 
@session_start();

define ('BASE_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
/*
We will use this session id as a handler until
get netflix user id
*/
define ('USER_ID', session_id());

require_once ( BASE_DIR . 'Configuration.php');
require_once ( BASE_DIR . 'includes/NetflixAPI.php');
require_once ( BASE_DIR . 'includes/Request.php');
require_once ( BASE_DIR . 'includes/OAuthSimple.php');

require_once ( BASE_DIR . 'netflix/nonAuthenticatedCall.php');
require_once ( BASE_DIR . 'netflix/protectedCall.php');
require_once ( BASE_DIR . 'netflix/signedCall.php');
require_once ( BASE_DIR . 'netflix/getToken.php');

// Check if we are doing any demos
$results = array();

// Check demo variable
if (!isset($_GET['demo'])) {
	$_GET['demo'] = '';
}

ob_start();

// Non authenticated call
if($_GET['demo'] == 1) {
	$netFlixApi = new NetflixAPI();
	$results = $netFlixApi->getCatalogTitlesAutoComplete('America');
	echo "<hr />We just made the function call getCatalogTitlesAutoComplete with America as a parameter.<br />" . PHP_EOL;
	echo "This function calls the auto complete API resource.<br />" . PHP_EOL;		
}

// Non authenticated call
if($_GET['demo'] == 2) {
	$netFlixApi = new NetflixAPI();
	$results = $netFlixApi->getCatalogTitles('America');
	echo "<hr />We just made the function call getCatalogTitles with America as a parameter.<br />" . PHP_EOL;
	echo "This function calls the get catalog title API resource.<br />" . PHP_EOL;		
}

// Non authenticated call
if($_GET['demo'] == 3) {
	$netFlixApi = new NetflixAPI();
	$results = $netFlixApi->getUsersInfo();
	echo "<hr />We just made the function call getUsersInfo.<br />" . PHP_EOL;
	echo "This function will return a list of your information.<br />" . PHP_EOL;		
}


// Displaying results?
if (count($results) > 0){
	echo "<br/ >" . PHP_EOL . "<b>Results of call:</b> <br />" . PHP_EOL;
	echo '<pre>';
	print_r($results);
	echo '</pre>';
}

$page_content = ob_get_contents();
ob_end_clean();
?>
<p><strong>Netflix API 1.0 Demo [<a href="documentation.html" target="_blank">Documentation</a>]</strong></p>
<p>Un-athenticated Call Demo --- <a href="?demo=1">Click here</a></p>
<p>Signed Call Demo --- <a href="?demo=2">Click here</a></p>
<p>Protected Call Demo --- <a href="?demo=3">Click here (requires you to login @ netflix)</a></p>
<?php

echo $page_content;