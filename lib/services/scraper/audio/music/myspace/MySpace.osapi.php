<?php

require_once "../../../../api/opensocial/osapi/osapi.php";

/**
 * Get mediaItems for album
 * @using osapi PHP SDK
 * SEE: http://wiki.developer.myspace.com/index.php?title=Media_Items_APIs
 */
 
$appId = '207107';
$appKey = '<app key>';
$appSecret = '<app secret>';
$userId = '109301429';
 
$osapi = new osapi(new osapiMySpaceProvider(), new osapiOAuth2Legged($appKey, $appSecret, $userId));
$batch = $osapi->newBatch();
 
$user_params = array(
    'userId' => $userId, 
    'groupId' => '@self', 
    'albumId' => 'myspace.com.album.756400',
    'count' => 2
);
 
// The second option in the $batch->add() assigns a request Id.
$batch->add($osapi->mediaItems->get($user_params), 'get_mediaItems');
 
// Send all batched commands
$result = $batch->execute();
 
// Demonstrate iterating over a response set, checking for an error & working with the result data. 
foreach ($result as $key => $result_item) {
    if ($result_item instanceof osapiError) {
      echo "<h2>There was a <em>".$result_item->getErrorCode()."</em> error with the <em>$key</em> request:</h2>";
      echo "<pre>".htmlentities($result_item->getErrorMessage())."<<nowiki>/</nowiki>pre>";
    } else {
      echo "<h2>Response for the <em>$key</em> request:</h2>";
      echo "<pre>".htmlentities(print_r($result_item, True))."<<nowiki>/</nowiki>pre>";
    }
}

?>