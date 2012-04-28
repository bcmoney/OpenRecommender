<?php

/** Truveo XML API
 * Basic Video Search application using PHP 5
 * Using SimpleXML library for XML parsing, and CURL for server to server requests
 * Author: Moninder Jheeta
 * Date: August 26, 2006
 */

try {

	$appid = "1x1jhj64466mi12ia";

	if (isset($_REQUEST['q'])) {

		// get the query from the q (search box) variable	
		
		$query = $_REQUEST['q'];

		// urlencode the query, and assemble the REST request
	
		$apiRequest = "http://xml.truveo.com/apiv3?appid=$appid&method=truveo.videos.getVideos&query=".urlencode($query)."&start=0&results=25&showAdult=1";

		// open a curl session to process the REST request
		// use the option CURLOPT_HEADER to make sure that http headers are retrieved along with the REST response xml
		// use the option CURLOPT_RETURNTRANSFER to have the REST response put into a string variable for your use
  	
  	$curlSession = curl_init($apiRequest);
  	curl_setopt($curlSession, CURLOPT_HEADER, true);
  	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
  	$xmlResponse = curl_exec($curlSession);
  	curl_close($curlSession);

		// parse the HTTP status code out of the status header to make sure that the request returned correctly
		
		$httpStatusHeader = array();
		preg_match('/\d\d\d/', $xmlResponse, $httpStatusHeader);	
		$httpStatusCode = $httpStatusHeader[0];

		// since the $xmlResponse variable includes the http headers as well as the actual xml response, get
		// the xml response by taking the part of $xmlResponse starting from <?xml

		$xml = strstr($xmlResponse, '<?xml');

		// using the SimpleXML library, available in PHP 5, load the xml string into an object
		// for more info on SimpleXML, see http://php.net/manual/en/ref.simplexml.php 

		$xmlObject = simplexml_load_string($xml);

		// if the HTTP status code is 403, 503, or 400, then throw an exception
		// which includes the error message given by the REST API

		if ($httpStatusCode == 400 || $httpStatusCode == 403 || $httpStatusCode == 503 ) {
			$Error = $xmlObject->Error;
			throw new Exception($Error);
		}	

		// get the VideoSet, VideoSet title, and number of videos from the xml response, and pass to the 
		// createThumbnailTable function along with the dimensions desired for the table.

		$VideoSet = $xmlObject->VideoSet;
		$VideoSetTitle = $VideoSet->title[0];
		$numVideos = intval($VideoSet->totalResultsReturned[0]);

		$cellsPerRow = 5;
		$rows = 5;
		$html = createThumbnailTable($VideoSet, $numVideos, $cellsPerRow, $rows);
	
		// the $html variable is displayed in the ResultsDiv below
	}

}
catch (Exception $e) {
	$html = $e->getMessage();
}


function createThumbnailTable($VideoSet, $numVideos, $cellsPerRow, $rows) {	
	$html = '<table style="width: 100%; margin: 0 0 0 0; border: 0px; border-style: none; border-collapse: collapse; 
	 							vertical-align: top;">';
	for ($j=0; $j < $rows; $j++) {
		$html .= '<tr>';
 		for ($k=0; $k < $cellsPerRow; $k++) {
			$i = ($j * $cellsPerRow) + $k;
			$html .= '<td align="center" valign="top" style="padding: 12px 4px 12px 4px;">';
			if ($i >= $numVideos) { 
				$html .= '&nbsp;'; 
			}
			else {
				// get the properties of each video from the VideoSet
				// any property of a video can be accessed as $Video->someproperty[0]
				 
 				$Video = $VideoSet->Video[$i];
				$id = $Video->id[0];
				$title = $Video->title[0];
				$videoUrl = $Video->videoUrl[0];
				$thumbnailUrl = $Video->thumbnailUrl[0];
					
				$html .= '<div style="width: 130px;">';
				$html .= '<a href="javascript:playVideo(\'' . $videoUrl . '\',' . $id . '); void(0);"><img src="' . 
										$thumbnailUrl .  '" class="thumbnail" alt="click to play video"/></a>';
 				$html .= '<div><a href="javascript:playVideo(\'' . $videoUrl . '\',' . $id . '); void(0);">' . $title . 
					 					'</a></div>';
				$html .= '</div>';
			}
			$html .= '</td>';
		}
 		$html .= '</tr>';
	}
	$html .= '</table>';
	return $html;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<title>SkipSearch - Truveo Video Search</title>
<!-- Set the document styles. -->
<style type="text/css">
body { font-family: Arial, sans-serif; font-size: 8pt; background-color: #EAEAEA; width: 100%; text-align: center; }
#bodyDiv { width: 700px; margin:0px auto; padding: 30px 10px 10px 10px; }
#logo { padding: 20px 0px 20px 0px; }
#TitleBarDiv { font-weight: bold; font-size: 10pt; padding: 25px 5px 15px 5px; }
#ResultsDiv { background-color: white; border: 1px solid #7f9db9; }
a { text-decoration: none; color:blue; }
a:hover { text-decoration: underline; }
.thumbnail { width: 75px; height: 56px; border: 1px solid blue; }
</style>
<script type="text/javascript">/* <![CDATA[ */
// Function playVideo() opens a new browser window to load the specified videoUrl.
function playVideo(videoUrl, id) {
        window.open(videoUrl, '', 'width=800,height=800,location=no,menubar=no,resizable=yes,scrollbars=yes');
}
/* ]]> */
</script>
</head>
<body>
	<div id="bodyDiv">
		<div id="logo"><img src="truveo_logo.gif" /></div>
		<form name="searchForm" method="POST" action="index.php" style="margin:0;">
			<div style="width:100%; text-align:center;">
				<nobr>
					<input type="text" name="q" id="searchBox" size="60" value="" />
					<input type="submit" name="searchButton" id="searchBox" value="Search Video" />
				</nobr>
			</div>
		</form>
		<div id="TitleBarDiv">&nbsp;
			<?php
				if (isset($VideoSetTitle)) 
					print $VideoSetTitle;
			?>
		</div>
		<div id="ResultsDiv">&nbsp;
			<?php
				if (isset($html))
					print $html; 		
			?>
		</div>
	</div>
</body>
</html>