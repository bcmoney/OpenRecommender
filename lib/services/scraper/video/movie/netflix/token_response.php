<?php
/**
 * This is the file that gets a response 
 * from netflix with OK, no get your user links
 *
 *
 * @copyright  2011 Aziz Hussain
 * @version    1.0
 * @link       http://azizsaleh.com
 * @email      azizsaleh@gmail.com
 */ 

@session_start();
define ('BASE_DIR', dirname(__FILE__) .'/');
define ('USER_ID', session_id());

require_once ( BASE_DIR . 'Configuration.php');
require_once ( BASE_DIR . 'includes/NetflixAPI.php');
require_once ( BASE_DIR . 'includes/Request.php');
require_once ( BASE_DIR . 'includes/OAuthSimple.php');

require_once ( BASE_DIR . 'netflix/nonAuthenticatedCall.php');
require_once ( BASE_DIR . 'netflix/protectedCall.php');
require_once ( BASE_DIR . 'netflix/signedCall.php');
require_once ( BASE_DIR . 'netflix/getToken.php');

 
 /*
 This class handles netflix response
 */
 class netflixResponse
 {
	public $storage;
	public $configs;
	public $request;
	
	/*
	Consructor
	
	@param		string		authroziation_code
	@param		string		api_file = NULL
	*/
	public function __construct($authorization_code, $api_file = NULL)
	{
		// Load configurations
		$this->configs 			= new Configuration($api_file);
		
		// Load HTTP Request object
		$this->request 			= new Request($this->configs);
				
		// Database handler
		$this->storage 			= new storageObject($this->configs);
		
		// Load configs from db
		$code_info = $this->storage->getUserToken();
		$this->configs->oauth_token 		= $code_info[0];
		$this->configs->oauth_token_secret 	= $code_info[1];

		$this->_requestFinalTokens($authorization_code);
	}
	
	/*
	This function will request the final keys using 
	consumer key and consumer secret		
	
	@param		string		authorization_code
	*/
	private function _requestFinalTokens($authorization_code)
	{
		$api_url = $this->composeVariables('http://api.netflix.com/oauth/access_token', 
										   array('output' => 'json'));

		$this->request->makeRequest($api_url, array());
		
		$response = $this->request->getRawResponse();
		
		preg_match_all('/oauth_token":"(.*)","user_id.*oauth_token_secret":"(.*)"/', $response, $results);

		$oauth_token 			= $results[1][0];
		$oauth_token_secret 	= $results[2][0];

		$this->storage->addUserToken($oauth_token, $oauth_token_secret, 4);
		
		/*
		For some reason, the above user ID doesn't work most of the time..
		let's re-query and get the final netflix user_id ....
		*/
		$Netflix = new NetflixAPI();
		$user_info = $Netflix->getCurrentUser();

		$netflix_userid = preg_replace('/.*users\//', '', $user_info->resource->link->href);
		
		$this->storage->addUserToken($oauth_token, $oauth_token_secret, 4, $netflix_userid);
		
		// Rediect user
		header("Location: " . $this->configs->app_redirect);
		exit();
	}
	
	/*
	This function will compose variables
	and add required keys depending on call
	
	@param		string	api_url
	@param		array	request_vars	
	
	@return		string
	*/
	private function composeVariables($api_url, $request_vars = array())
	{
		$signatures = array(
						'consumer_key'	=> $this->configs->consumer_key,  
						'shared_secret'	=> $this->configs->shared_key );
		

		// Add secret codes for user
		if (isset($this->configs->oauth_token) && isset($this->configs->oauth_token_secret))
		{
			$signatures['access_token'] 	= $this->configs->oauth_token;
			$signatures['access_secret'] 	= $this->configs->oauth_token_secret;
		}

		$auth_object = new OAuthSimple($this->configs->consumer_key, $this->configs->shared_key);  
		$request = $auth_object->sign(
				array(	'path'			=> $api_url,  
						'parameters' 	=> $request_vars,  
						'signatures' 	=> $signatures));

		return $request['signed_url'];
	}
 }
 
 
 // Check if we have a response
 if (isset($_GET['oauth_token'])) {
	$get_auth = new netflixResponse($_GET['oauth_token']); 
 }