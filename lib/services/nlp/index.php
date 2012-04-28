<?php
/**
 * NLP
 *   OpenRecommender Natural Language Processing hooks into Entity Extraction and Content Analysis APIs.
 *
 * ADAPTED FROM:
 *   Simple Entity Extraction & Content API Test Tool - http://blog.viewchange.org/2010/05/entity-extraction-content-api-evaluation/
 *
 * @author Rob DiCiuccio, http://www.definitionstudio.com
 * @author Bryan Copeland, http://www.bryancopeland.com
 */
$DEBUG = false; //toggle debug mode to view full details of NLP API used
$SHOW_ENTITIES = true; //toggle whether entities and their links should be shown

error_reporting(0);

require_once('krumo/class.krumo.php');
// define APIs
$services = array('OpenCalais', 'Zemanta', 'Evri', 'AlchemyAPI', 'OpenAmplify', 'Yahoo', 'DBpediaSpotlight');
$content_services = array('Zemanta', 'Daylife', 'YahooBOSS', 'Bing', 'YouTube', 'Truveo', 'Vimeo', 'SocialActions','ZemantaSocialActions');

// load config
$config_file = '../../../config.php';
if(file_exists($config_file) && is_readable($config_file)) {
	include_once($config_file);
} else {
	$config_error = "Failed to load config file.";
}
              
// require models
require_once('BaseAPI.php');
foreach($services as $model) {
	require_once($model.'.php');
}
foreach($content_services as $model) {
	require_once($model.'.php');
}

// ENTITIES
if(!empty($_POST['content'])) {

	$time_start = microtime(true); //start the timer (to show processing time)
	
	$api = new $_POST['api']; //load API type (controlled from dropdown)
	$api->init_nlp($_POST['content']); // init NLP
		
	$api->query();
	
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	
	$curl_info = $api->getCurlInfo();

	$entities = $api->getEntities();	
	$entityMetadata = array();
	foreach($entities as $entity) {
		$name = str_replace("_", " ", str_replace("http://dbpedia.org/resource/","",urldecode($entity['name'])));
		$entityMetadata[$name]=array(); //create an array of all entity metadata, indexed by name		
		foreach($entity['disambiguation'] as $d) {						
			if (!empty($d)) {
				array_push($entityMetadata[$name],$d); //add metadata attached to each entity name
			}
		}		
	}	
	
	$text = $_POST['content'];
	foreach($entityMetadata as $word => $value) {	
		$text = str_replace($word, '<a href="'.$value[0].'#'.$word.'">'.$word.'</a>', $text);
	}
	
	//ENTITIES output
	if($SHOW_ENTITIES) {
		$entity_text = '<strong>ENTITIES</strong><ul>';
		foreach($entities as $entity) {
			$entity_text .= 'Name: <strong>' . $entity['name'] . '</strong> [score: ' . $entity['score'] . ']<br />';
			$entity_text .= 'Linked Data: <br />';
			$entity_text .= '<ol class="linked_data">';
			$i=1;
			foreach($entity['disambiguation'] as $d) {
				$entity_text .= '<li class="'.(($i%2==0)?'even':'odd').'">' . $d . '</li>';
				$i++;
			}
			$entity_text .= '</ol>';
		}
		$entity_text .= '</ul>';
    }
}
		
// RELATED CONTENT
if(!empty($_POST['content_api'])) {

	$time_start = microtime(true);

	$content_api = new $_POST['content_api'];

	// init content (pass entity API)
	$content_api->init_related($api);

	echo '<h3>RELATED CONTENT: Submitting to ' . get_class($content_api) . '...</h3>';
	echo '<p style="font-style:italic">API URL: ' . $content_api->getURL() . '</p>';
	echo '<p>API Arguments:</p><pre>';
	print_r($content_api->getArgs());
	echo '</pre>';

	$content_api->query();

	$time_end = microtime(true);
	$time = $time_end - $time_start;

	$curl_info = $content_api->getCurlInfo();

	echo '<p style="font-weight:bold">Raw Result (HTTP code: ' . $curl_info['http_code'] . '):</p>';
	echo '<pre>';
	echo 'Query: ' . $curl_info['url'];
	echo '</pre>';
	krumo($content_api->getRawResult());

	echo '<p style="font-weight:bold">Parsed Result:</p>';
	krumo($content_api->getData());

	echo '<p style="font-weight:bold">RELATED CONTENT:</p>';
	$related = $content_api->getRelated();
	foreach($related as $r) {
		echo '<p>';
		echo 'Name: <a href="' . $r['url'] . '" target="_blank">' . $r['title'] . '</a> [score: ' . $r['score'] . ']<br />';
		echo 'Publish Date: ' . $r['date'] . '<br />';
		echo 'Description: ' . $r['descr'] . '<br />';
		echo 'Source: ' . $r['source'] . '<br />';
		echo '</p>';
	}
	echo "<p>Query took $time seconds</p>";

	echo '<hr />';
}		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Natural Language Processing (NLP)</title>
	<style type="text/css">
		label { padding-left:25px; }
		#result { background:#ccc; border:1px solid #333; display:block }
		.linked_data .odd { background:lavender }
		.linked_data .even { background:powderblue }
	</style>
</head>
<body>
	<?php if(!empty($config_error)) echo "<div style=\"color:red\">{$config_error}</div>"; ?>
	<form method="post">
		<p>
			<label for="api">Entity API:</label>
			<select id="api" name="api">
				<?php foreach($services as $s) { ?>
				<option value="<?=$s?>"<?php if(@$_POST['api']==$s) echo ' selected="selected"';?>><?=$s?></option>
				<?php } ?>
			</select>
			<label for="content_api">Related Content API:</label>
			<select id="content_api" name="content_api">
				<option value="">None</option>
				<?php foreach($content_services as $s) { ?>
				<option value="<?=$s?>"<?php if(@$_POST['content_api']==$s) echo ' selected="selected"';?>><?=$s?></option>
				<?php } ?>
			</select>
		</p>
		<label for="content">Text:</label>
		<textarea id="content" name="content" style="width:500px;height:100px;"><?php if(!empty($_POST['content'])) echo stripslashes($_POST['content']); ?></textarea><br/>
		<input type="submit" value="submit" />
	</form>
	<div id="result">
		<?php if(!empty($text)) { echo $text; } ?>
	</div>
		<?php if($SHOW_ENTITIES) { echo $entity_text; }	?>
	<hr />
	<?php 
	  if($DEBUG) {		
		echo '<h3>NLP via ' . get_class($api) . '...</h3>';
		echo '<p style="font-style:italic">API URL: ' . $api->getURL() . '</p>';	  
		// enable error display
		ini_set('display_errors', 'on');
		error_reporting(E_ALL);	  
		//showing all details about NLP and Content Web Service requests/responses
		echo '<p>API Arguments:</p><pre>';
		print_r($api->getArgs());
		echo '</pre>';		
		echo '<p style="font-weight:bold">Raw Result (HTTP code: ' . $curl_info['http_code'] . '):</p>';
		krumo($api->getRawResult());		
		echo '<p style="font-weight:bold">Parsed Result:</p>';
		krumo($api->getData());
	    echo "<p>Query took $time seconds</p>";		
      }		
	?>
</body>
</html>