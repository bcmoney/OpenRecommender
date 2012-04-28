<?php

include_once "FriendFeed.class.php";

//OAUTH credentials
$consumer_key = "";
$consumer_key_secret = "";

$ff = new Friendfeed($consumer_key ,$consumer_key_secret); 
$tokens = $ff->fetch_oauth_request_token();
$request_token = $tokens["oauth_token"];
$oauth_token_secret = $tokens["oauth_token_secret"]; 
// Save the response in session for further use. 
  $_SESSION["'ff_oauth_token"] =  $request_token;
  $_SESSION["ff_oauth_token_secret"] =  $oauth_token_secret; 
$auth_url = $ff->get_oauth_authentication_url($tokens);

$oauth_token = $_REQUEST['oauth_token'];  // Provided you in URL $_SESSION["ff_oauth_token_secret"] =  $oauth_token_secret; //  Retrive ff_oaut
$action = (!empty($_REQUEST['action'])) ? $_REQUEST['action'] : 'profile'; //action to perform (defaults to 

if (!empty($oauth_token)) {  
  if ($action == "status" || $action == "update") {
    $ff = new FriendFeed($consumer_key, $consumer_key_secret, array('oauth_token'=>$rowList['access_token'], 'oauth_token_secret'=>$rowList['access_token_secret'])); 
    $feed = $Ff->post_entry($bookmark_title,$bookmark_url,'me'); 
    print_r($feed) // Check the response to validate status is updated successfully.
  }
  else { //default to a read access to get Profile information
    $ff = new FriendFeed($consumer_key, $consumer_key_secret, array('oauth_token'=>$db_tokens['oauth_token'], 'oauth_token_secret'=>$db_tokens['access_token_secret']));
  }  
}
else {
  echo '<a href="'.$auth_url.'">Login to frienfeed</a><br>';
}



?>