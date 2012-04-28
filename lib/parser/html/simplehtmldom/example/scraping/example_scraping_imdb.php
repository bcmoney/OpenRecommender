<?php
include_once('../../simple_html_dom.php');

function scrape_IMDB($url) {
  // create HTML DOM
  $html = file_get_html($url);
		
  $ret['Title'] = str_replace(" - IMDb", "",$html->find('title',0)->innertext); // get Movie's title	
	$ret['Poster'] = $html->find('meta[property="og:image"]',0)->getAttribute('content');
	$ret['Link'] = $html->find('meta[property="og:url"]',0)->getAttribute('content');
	$ret['Description'] = $html->find('p[itemprop="description"]',0)->innertext;
    $ret['Rating'] = $html->find('span[itemprop="ratingValue"]',0)->innertext . "/" . $html->find('span[itemprop="bestRating"]',0)->innertext; // get star-rating
    $ret['Director'] = $html->find('a[itemprop="director"]',0)->innertext;
	$ret['Writer'] = $html->find('div.txt-block a',0)->innertext;
	$ret['Trailer'] = 'http://imdb.com'. (is_object($html->find('a[itemprop="trailer"]',0)) ? $html->find('a[itemprop="trailer"]',0)->getAttribute('href') : ((is_object($html->find('div span[class="video_slate"] a',0))) ? $html->find('div span[class="video_slate"] a',0)->getAttribute('href') : ''));
	$ret['TrailerVideo'] = (!empty($ret['Trailer']) && isset($ret['Trailer'])) ? '<iframe src="'.$ret['Trailer'].'#video-player-container" bgcolor="#000000" topmargin="0" marginwidth="0" marginheight="0" leftmargin="0" margin="none" width="665" height="480" scrolling="no" frameborder="0"></iframe><noframes><a href="'.$ret['Trailer'].'#video-player-container" target="_blank"></a></noframes>' : ''; //<embed width="640" height="480" flashvars="file=http%3A%2F%2Fwww.totaleclips.com%2FPlayer%2FBounce.aspx%3Feclipid%3De18019%26bitrateid%3D472%26vendorid%3D102%26type%3D.mp4&amp;recommendations=recommendations&amp;autostart=true&amp;width=640&amp;height=480&amp;image=http://ia.media-imdb.com/images/M/MV5BMjA0NTQ5NTA5M15BMl5BanBnXkFtZTcwMTYyMTc2MQ@@._V1_.jpg&amp;backcolor=0x000000&amp;frontcolor=0xCCCCCC&amp;lightcolor=0xFFFFCC&amp;shuffle=false&amp;callback=metrics&amp;repeat=list&amp;linktarget=_top&amp;enablejs=true" allowscriptaccess="always" allowfullscreen="true" wmode="transparent" quality="high" bgcolor="#FFFFFF" name="player" id="player" style="undefined" src="http://www.imdb.com/images/js/app/video/mediaplayer.swf" type="application/x-shockwave-flash" />		
	//$ret['MPAA'] = $html->find('span[itemprop="contentRating"]',0)->innertext;
	
    //loop through any thumbnails/stills
    $ret['Images'] = '';
	foreach($html->find('div.mediastrip img') as $image) {
		$ret['Images'] .= ''.$image->getAttribute('src').',';
	}
	
    // get cast
    foreach($html->find('table.cast_list a img') as $actor) {
        $ret[$actor->getAttribute('title')] = $actor->getAttribute('src');
    }	

	//2nd request, this time to get the Movie Recommendations page for this film ID
	$rec_html = file_get_html($url.'/recommendations');	
	$recommendations = $rec_html->find('div#tn15content table tbody tr td font a');	
		// get recommendations
		foreach($recommendations as $recommendation) {
			$ret[$recommendation->innertext] = "http://www.imdb.com".$recommendation->getAttribute('href');
		}
	
    // clean up memory
    $html->clear();
    unset($html);

    return $ret;
}

function clean($mytext) {	
	$mytext = preg_replace("[^A-Za-z0-9]", "", $mytext); //CLEAN OUT ALL NON-ALPHA NUMERICAL CHARACTERS	
	$mytext = trim($mytext); //REMOVE BEGINNING AND ENDING SPACES
	return $mytext;
}

// -----------------------------------------------------------------------------
// test it!
$IMDB_MOVIE_ID = (!empty($_REQUEST['m'])) ? clean($_REQUEST['m']) : 'tt0335266'; // http://imdb.com/title/<IMDB_MOVIE_ID>/
$IMDB_ACTOR_ID = (!empty($_REQUEST['a'])) ? clean($_REQUEST['a']) : 'nm0945522'; // http://www.imdb.com/name/<IMDB_ACTOR_ID>/
$ret = scrape_IMDB('http://imdb.com/title/'.$IMDB_MOVIE_ID); 

foreach($ret as $k=>$v) {
    echo '<strong>'.$k.':</strong> '.$v.'<br>';
}

?>