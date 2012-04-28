<?php

require_once "DeviantArt.class.php";

/// EXAMPLE usage
$username = 'Basistka'; //OTHER example usernames:  'damselbirch', 'HibariAkado', 'FlyingSalmon', 'Yanirawr', 'mistress-illusion', 'liviugherman'

$da = new deviantart($username);
echo $da->__tostring();

?>