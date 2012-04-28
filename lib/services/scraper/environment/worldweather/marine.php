<?php

require_once "../../../../../config.php";

$sunrise = 6;    #sunrise/sunset don't have to be exact as we're looking at a 3 hour period
$sunset = 18;
$lat = 41.5;
$long = -71.4;
$key = $config['worldweather_api_key'];

$url = "http://free.worldweatheronline.com/feed/marine.ashx?q=" . $lat . "," . $long . "&format=json&key=" . $key;
$json_string = file_get_contents($url);
$json = json_decode($json_string,true);

$conditions = new SimpleXMLElement("http://www.worldweatheronline.com/feed/wwoConditionCodes.xml", null, true);
$cc = array();

foreach($conditions as $wcc) {
    $cc["$wcc->code"]["description"] = trim($wcc->description);
    $cc["$wcc->code"]["day_icon"] = "http://www.worldweatheronline.com/images/wsymbols01_png_64/" . trim($wcc->day_icon) . ".png";
    $cc["$wcc->code"]["night_icon"] = "http://www.worldweatheronline.com/images/wsymbols01_png_64/" . trim($wcc->night_icon) . ".png";
}

echo "<h1>Marine Forecast for " . $json['data']['nearest_area'][0]['latitude'] . "," . $json['data']['nearest_area'][0]['longitude'] . " as of " . $json['data']['weather'][0]['date'] . "</h1>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Marine Forecast</title>
</head>
<body>
<?php    
foreach($json['data']['weather'] as $weather) {
?>
<table border="1">
  <tr>
    <th width="80"><?php echo $weather['date']?></th>
    <th width="64">Icon</th>
    <th width="70">Air<br/>Temp</th/>
    <th width="40">RH</th>
    <th width="75">Precip<br/>Rate/hr</th>
    <th width="75">Visibility</th>
    <th width="80">Pressure</th>
    <th width="100">Wind</th>
    <th width="70">Wave<br/>Height</th>
    <th width="120">Swell</th>
    <th width="70">Swell<br/>Period</th>
    <th width="60">Water<br/>Temp</th>
  </tr>
  <tr></tr>
 <?php
      foreach ($weather['hourly'] as $hourly) {
        $hour = $hourly['time']/100;
        if (($hour < $sunrise) or ($hour >= $sunset))
            $icon = $cc[$hourly['weatherCode']]['night_icon'];
        else
            $icon = $cc[$hourly['weatherCode']]['day_icon'];
           
        echo "\t<tr>\n";
        echo "\t\t<td>" . $hour . ":00</td>\n";      
        echo "\t\t<td>" . '<img src="' . $icon . '" border="0"' 
            . '" alt ="' .  $cc[$hourly['weatherCode']]['description'] . '"'
            . '" title="' . $cc[$hourly['weatherCode']]['description'] . '"/></td>'."\n";
        echo "\t\t<td>" . $hourly['tempF']."F/".$hourly['tempC']."C</td>\n";
        echo "\t\t<td>" . $hourly['humidity']."%</td>\n";
        echo "\t\t<td>" . floor($hourly['precipMM'] / 3 / 2.54 + 0.5)/10 . '"/'
                        . floor($hourly['precipMM'] / 3 + 0.5) . "mm</td>\n";
        echo "\t\t<td>" . floor($hourly['visibility']/1.15 + 0.5) . " nm</td>\n";
        echo "\t\t<td>" . $hourly['pressure']."</td>\n";
        echo "\t\t<td>" . $hourly['winddir16Point'] . " - "
                        . floor($hourly['windspeedMiles']/1.15 + 0.5) . " kt</td>\n";
        echo "\t\t<td>" . floor($hourly['sigHeight_m'] * 3.28 + 0.5) . "ft/"
                        . $hourly['sigHeight_m'] . "m</td>\n";
        echo "\t\t<td>" . floor($hourly['swellHeight_m'] * 3.28 + 0.5) . "ft/"
                        . $hourly['swellHeight_m']."m @ "
                        . $hourly['swellDir'] . "&deg;</td>\n";
        echo "\t\t<td>"    . $hourly['swellPeriod_secs'] . " sec</td>\n";
        echo "\t\t<td>" . $hourly['waterTemp_F'] . "F/"
                        . $hourly['waterTemp_C'] . "C</td>\n";
        echo "\t<tr>\n";
      }
      echo "</table><br/><br/>\n";
    }
?>
Powered by <a href="http://www.worldweatheronline.com/" title="Free local weather content provider" target="_blank">World Weather Online</a>
</body>
</html>