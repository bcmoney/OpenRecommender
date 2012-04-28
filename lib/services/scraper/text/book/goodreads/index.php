<?php
/**
 * @author Sachin Khosla
 * @description: This file requests the request token and
 * creates the authorization/login link
 * Stores the oauth_token in the session variables.
 *
 * modified by @YourNextRead to integrate with goodreads
 */


require_once('GoodreadsAPI.php');

session_start();


$connection = new GoodreadsAPI(CONSUMER_KEY, CONSUMER_SECRET);
$request_token = $connection->getRequestToken(CALLBACK_URL);


$_SESSION['oauth_token']  = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

$authorize_url = $connection->getLoginURL($request_token);
//echo "token: " . $request_token['oauth_token'] . "\n";
//echo " secret: " . $request_token['oauth_token_secret'];

echo "<a href='$authorize_url'>Sign in to <img src='goodreads-badge.png' alt='goodreads' /></a>";

?>