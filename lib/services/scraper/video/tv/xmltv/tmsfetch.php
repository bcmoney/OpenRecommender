<?php

$DEBUG = true;

$appname = 'BCmoney MobileTV';
$username = 'bcmoney';
$password = 'mchammer';
$wsdl_url = 'http://docs.tms.tribune.com/tech/tmsdatadirect/schedulesdirect/tvDataDelivery.wsdl';
$preferred_lineup = 2; //which one of your timeslots you'd like to display

$start = gmdate("Y-m-d\TH:i:s\Z",time()); //today
$stop = gmdate("Y-m-d\TH:i:s\Z",time()+3600*24); //tomorrow (now + 1 day)

$client = new SoapClient(
              $wsdl_url,
              array('trace'     => 0,    // Set to 1 to use __getLastRequest
                'exceptions'  => 0,
                'user_agent'  => "php/".$appname,
                'login'       => strtolower($username),
                'password'    => $password
              )
            );

$data = $client->download($start,$stop);

/* ******************************************************** */
/* PARSING */
$lineup = $data->xtvd->lineups->lineup[$preferred_lineup];

$epg_header = '<th><a href=""><-Back</a></th>';
foreach($timeslot) {
  $epg_header .= '<th>'.$timeslot.'</th>'
}

$epg_listings = '<table>';
foreach ($data->xtvd->stations->station as $station) {
  $stationID = $station['id'];
  $stationCallSign = $station->callSign;
  $stationName = $station->name;   
  $stationChannel = $lineup->map['channel'];
  $epg_listings .= '<tr>';
  $epg_listings .= '<td><span title="'.$stationName.'">'.$stationCallSign.'<br/>'.$stationChannel.'</td>';
  foreach($data->xtvd->schedules->schedule as $schedule) {
    if($schedule['station'] == $stationID) {
      $epg_listings .= '<td><a href="'.$schedule->.'" title="'.$schedule[''].'" rel="'.$schedule[''].'">'.$schedule[''].'</a></td>';
      $programID = '';
      foreach($data->xtvd->programs->program as $program) {        
        if ($program['id'] == $programID) {
          $showType = $program->showType;
          $showTitle = $program->title;
          $episodeTitle = $program->subtitle;
          $episodeDesc = $program->description;
          $episodeAirDate = $program->originalAirDate;
          $episodeNumber = str_replace($program->series,"",$programID);
          $syndicatedEpisodeNumber = $program->series;
          $epg_listings .= '<td><a href="http://www.imdb.com/find?s=all&q='.urlencode($showTitle).'">'.$showTitle.'</a> - '.$episodeTitle.' ('.$episodeAirDate.')<br/>'.$episodeDesc.'</td>';
        }
      }
    }
  }
  $epg_listings .= '</tr>';
}
$epg_listings .= '</table>';
/* ******************************************************** */
#$json = json_encode($data); // Convert SimpleXML object to JSON
#$response_array = json_decode($json); // Convert JSON object to JSON Array (PHP 5.2+)

?>
<html>
<head>
<title><?php echo $appname ?> - <?php echo $username ?>'s EPG</title>
</head>
<body>
<pre>
  <?php ($DEBUG) ? print_r($data) : echo "&nbsp;" ?>
</pre>
<table>
<tr>
  <?php echo $epg_header; ?>
</tr>
<tr>
  <?php echo $epg_listings; ?>
</tr>
</table>
</body>
</html>