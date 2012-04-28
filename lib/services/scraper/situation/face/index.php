<?php

require_once "../../../../../config.php";
include_once "FaceRestClient.php";

$face = new FaceRestClient($config['face_api_key'], $config['face_secret_key'], $config['face_api_key']);
$me = $face->account_authenticate();
$dectected = $face->faces_detect($config['SITE'],'/lib/services/scraper/situation/face/uploadlatest.png', $me);

?>