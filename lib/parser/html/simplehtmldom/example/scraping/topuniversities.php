<?php
include_once('../../simple_html_dom.php');

error_reporting(0); //suppress warnings on local (notices from "safe_mode")
ini_set('max_execution_time', 300); //increase maxmimum execution time

function scraping_TopUniversities($url) {
    // create HTML DOM
    $html = file_get_html($url);

    // get university
    foreach($html->find('div.search-results div.search-result-profile dt.title a') as $school) {
        echo "INSERT INTO school ('".$school->innertext."', '".$school->getAttribute('href')."');<br/>";
    }    
	
    // clean up memory
    $html->clear();
    unset($html);

    return $ret;
}


// -----------------------------------------------------------------------------
// test it!
for ($i = 104; $i < 141; $i++) {
	scraping_TopUniversities("http://www.topuniversities.com/search/universities/?page=$i");
}
    
?>