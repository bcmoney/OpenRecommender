<?php

/*
	In this file we are scanning the image folders and 
	return a JSON object with file names. It is used by
	jQuery to display the images on the front page:
*/

// The standard header for json data:
header('Content-type: application/json');

$perPage = 24;

// Scanning the thumbnail folder for JPG images:
$g = glob('uploads/thumbs/*.jpg');

if(!$g){
	$g = array();
}

$names = array();
$modified = array();

// We loop though the file names returned by glob,
// and we populate a second file with modifed timestamps.

for($i=0,$z=count($g);$i<$z;$i++){
	$path = explode('/',$g[$i]);
	$names[$i] = array_pop($path);	
	$modified[$i] = filemtime($g[$i]);
}

// Multisort will sort the array with the filenames
// according to their timestamps, given in $modified:

array_multisort($modified,SORT_DESC,$names);

$start = 0;

// browse.php can also paginate results with an optional
// GET parameter with the filename of the image to start from:

if(isset($_GET['start']) && strlen($_GET['start'])>1){
	$start = array_search($_GET['start'],$names);
	
	if($start === false){
		// Such a picture was not found
		$start = 0;
	}
}

// nextStart is returned alongside the filenames,
// so the script can pass it as a $_GET['start']
// parameter to this script if "Load More" is clicked

$nextStart = '';

if($names[$start+$perPage]){
	$nextStart = $names[$start+$perPage];
}

$names = array_slice($names,$start,$perPage);

// Formatting and returning the JSON object:

echo json_encode(array(
	'files' => $names,
	'nextStart'	=> $nextStart
));

?>