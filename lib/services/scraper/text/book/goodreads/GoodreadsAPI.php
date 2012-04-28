<?php

/**
 * @author Sachin Khosla - @realin
 * @desc This is the wrapper class, contains all the high level functions
 * responsible of making all the calls to & fro to goodreads
 *
 * modified by @YourNextRead to integrate with goodreads
 */


require_once '../../../api/authorization/oauth/oauth2.php';
require_once '../../../Services.class.php';

/**
 * you will get the following details after
 * registering your application at
 * http://www.goodreads.com/api/keys
 *
 */
define('CONSUMER_KEY', $GLOBAL['api_config']['goodreads_api_key']);
define('CONSUMER_SECRET', $GLOBAL['api_config']['goodreads_secret_key']);
define('CALLBACK_URL', $GLOBAL['api_config']['goodreads_api_callback']);



class GoodreadsAPI
{

  private $requestTokenURL = 'http://www.goodreads.com/oauth/request_token';
  private $accessTokenURL = 'http://www.goodreads.com/oauth/access_token';
  private $loginURL = 'http://www.goodreads.com/oauth/authorize';
  private $consumer,$token,$result;
  public $return_code;


  function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL)
  {
    // define the supported SHA1 method
    $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();

    $this->consumer = new OAuthConsumer($consumer_key, $consumer_secret);

    if (!empty($oauth_token) && !empty($oauth_token_secret))
      $this->token = new OAuthConsumer($oauth_token, $oauth_token_secret);
    else
      $this->token = NULL;
  }

  /**
   * @param <string> $verify is the OAUTH_VERIFIER passed in the callback url
   * @return access token
   */

  function getAccessToken($verify = '' )
  {
    $parameters = array();
    if ($verify != '')
    {
      $data['oauth_verifier'] = $verify;
    }
    $request = $this->makeRequest($this->accessTokenURL,false, $data);
    $token = OAuthUtil::parse_parameters($request);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }

  /**
   *
   * @param <string> $url URL to send the get request
   * @param <array> $data anydata to be sent
   */

  function doGet($url, $data = array())
  {

    $response = $this->makeRequest($url, false, $data);
    return $response;
  }

  /**
   *
   * @param <string> $url URL to send the POST request
   * @param <array> $data anydata to be sent
   */

  function doPost($url, $data = array())
  {
    $response = $this->makeRequest($url, true, $data);
    return $response;
  }

  /**
   * gets the request token for the first time
   */
  function getRequestToken($oauth_callback = NULL)
  {
    $params = array();
    if (!empty($oauth_callback))
    {
      $params['oauth_callback'] = $oauth_callback;
    }
    $request = $this->makeRequest($this->requestTokenURL,false, $params);
    $token = OAuthUtil::parse_parameters($request);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }

  /**
   * Creates the Authorization/login URL
   */
  function getLoginURL($token)
  {
    $token = $token['oauth_token'];
    return $this->loginURL . "?oauth_token={$token}";
  }

  /**
   * Prepares the request for the CURL
   */
  function makeRequest($url, $is_post = false, $data = array())
  {

    $method = ($is_post == true)?'POST':'GET';
    $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $data);
    $request->sign_request($this->sha1_method, $this->consumer, $this->token);

    if($is_post === true)
    {
      return $this->makeCurl($request->get_normalized_http_url(), $method, $request->to_postdata());
    }
    else
      return $this->makeCurl($request->to_url());
  }

  /**
   * Does all the CURL request, capable of sending both GET & POST requests
   */
  private function makeCurl($url,$is_post = false,$data=null)
  {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_USERAGENT, 'GoodreadsOAuth v0.2.0-beta2');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

    if (!empty($data) && $is_post == true)
    {
      curl_setopt($curl, CURLOPT_POST, TRUE);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    $this->result = curl_exec($curl);
    $this->return_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return $this->result;
  }
}


?>