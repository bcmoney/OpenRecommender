<?php
/**
 * Copyright (c) 2011 ScientiaMobile, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the COPYING file distributed with this package.
 *
 * @package    WURFL
 * @copyright  ScientiaMobile, Inc.
 * @author     Steve Kamerman <steve AT scientiamobile.com>
 * @license    GNU Affero General Public License
 * @version    $id$
 */
if (!isset($_GET['force_ua'])) {
	$_GET['force_ua'] = 'SonyEricssonK700i/R2AC SEMC-Browser/4.0.2 Profile/MIDP-2.0 Configuration/CLDC-1.1';
}
?>
<form action="check_wurfl.php" method="GET">
Mobile device user agent:<br />
<input type="text" name="force_ua" size="100" value="<?php echo $_GET['force_ua']?>">
<input type="submit" name="submit" value="Submit"/>
</form>
Try some of these:
<ul>
  <li><pre>SonyEricssonK700i/R2AC SEMC-Browser/4.0.2 Profile/MIDP-2.0 Configuration/CLDC-1.1</pre></li>
  <li><pre>MOT-T720/S_G_05.30.0CR MIB/2.0 Profile/MIDP-1.0 Configuration/CLDC-1.0</pre></li>
  <li><pre>SAGEM-myX5-2/1.0 Profile/MIDP-2.0 Configuration/CLDC-1.0 UP.Browser/6.2.2.6.d.2 (GUI) MMP/1.0</pre></li>
  <li><pre>NokiaN90-1/3.0541.5.2 Series60/2.8 Profile/MIDP-2.0 Configuration/CLDC-1.1</pre></li>
</ul>
<hr size="1" />
<?php
list($usec, $sec) = explode(" ", microtime());
$start = ((float)$usec + (float)$sec); 

// Include the Tera-WURFL file
require_once realpath(dirname(__FILE__).'/TeraWurfl.php');

list($usec, $sec) = explode(" ", microtime());
$load_class = ((float)$usec + (float)$sec); 

// instantiate the Tera-WURFL object
$wurflObj = new TeraWurfl();

list($usec, $sec) = explode(" ", microtime());
$init_class = ((float)$usec + (float)$sec); 

// Get the capabilities from the object
$matched = $wurflObj->GetDeviceCapabilitiesFromAgent($_GET['force_ua']);

list($usec, $sec) = explode(" ", microtime());
$end = ((float)$usec + (float)$sec); 

echo "Time to load TeraWurfl.php:".($load_class-$start)."<br>\n";
echo "Time to initialize class:".($init_class-$load_class)."<br>\n";
echo "Time to find the user agent:".($end-$init_class)."<br>\n";
echo "Total:".($end-$start)."<br>\n";
$cached = ($wurflObj->foundInCache)? "<strong>(Found in cache)</strong>": "";
echo "<br>Total Queries: ".$wurflObj->db->numQueries." $cached<br>\n";

$text = ($matched)?"a conlusive":"an <font color=\"#990000\">inconclusive</font>";
echo "<h3>Test resulted in $text match</h3>";
echo "<pre>";
var_export($wurflObj->capabilities);
echo "</pre>";

?>
<form action="check_wurfl.php" method="GET">
Mobile device user agent:<br />
<input type="text" name="force_ua" size="100" value="<?php echo $_GET['force_ua']?>">
<input type="submit" name="submit" value="Submit"/>
</form>
