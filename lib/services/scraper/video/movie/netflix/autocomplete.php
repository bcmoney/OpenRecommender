<?php 

define ('BASE_DIR', dirname(__FILE__) .'/');
require_once ( BASE_DIR . 'Configuration.php');
require_once ( BASE_DIR . 'includes/NetflixAPI.php');
require_once ( BASE_DIR . 'includes/Request.php');
require_once ( BASE_DIR . 'includes/OAuthSimple.php');

require_once ( BASE_DIR . 'netflix/nonAuthenticatedCall.php');
require_once ( BASE_DIR . 'netflix/protectedCall.php');
require_once ( BASE_DIR . 'netflix/signedCall.php');
require_once ( BASE_DIR . 'netflix/getToken.php');


$query = (!empty($_REQUEST['q'])) ? $_REQUEST['q'] : 'Rocky';

$Netflix = new NetflixAPI();
$results = $Netflix->getCatalogTitlesAutoComplete($query); //$results is now an array containing the results

//DEBUG: echo "<pre>"; print_r($results); echo "</pre><br/>";
echo '{ "movie": { "title": [';
foreach ($results->autocomplete->autocomplete_item as $movie) {
  echo '"'.$movie->title->short.'", ';
}
echo ']}}';
?>