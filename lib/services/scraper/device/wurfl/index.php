<?php

require_once "TeraWurfl.php"; //include the Tera-WURFL file

$wurflObj = new TeraWurfl(); //instantiate the Tera-WURFL object
$wurflObj->getDeviceCapabilitiesFromAgent(); //get the capabilities of the current client

//see if this client is on a wireless device (or if they can't be identified)
if(!$wurflObj->getDeviceCapability("is_wireless_device")){
	echo "<h2>You should not be here</h2>";
}

echo "Markup: ".$wurflObj->getDeviceCapability("preferred_markup"); //see what this device's preferred markup language is
  $width = $wurflObj->getDeviceCapability("resolution_width"); //see the display resolution WIDTH
  $height = $wurflObj->getDeviceCapability("resolution_height"); //see the display resolution HEIGHT
echo "<br/>Resolution: $width x $height<br/>";

?>