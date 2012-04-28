<?php 

$title1 = (!empty($_REQUEST['t1'])) ? $_REQUEST['t1'] : 'The Union: The Business of Getting High (2007)';
$title2 = (!empty($_REQUEST['t2'])) ? $_REQUEST['t2'] : 'A World Without Cancer - The Story Of Vitamin B17';
	
	$p = 0;
	$i = similar_text($title1, $title2, $p); 
	
	echo("Matched: $i ,<br/>Percentage Similarity: $p%"); 
?>