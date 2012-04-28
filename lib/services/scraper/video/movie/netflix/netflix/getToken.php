<?php
/**
 * This object will retrieve a token for
 * a certain user and store it in the storageObject
 *
 * License: LGPL
 *
 * @copyright  2011 Aziz Hussain
 * @version    1.0
 * @link       http://azizsaleh.com
 * @email      azizsaleh@gmail.com
 */ 


class getToken
{
	public $request;
	public $configs;
	
	public $storage;
	public $user_id;
	public $step_id;
	
	/* Constructor */
	public function __construct ($force_reget = false, &$configs)
	{
		// Load configurations
		$this->configs 			= $configs;
		
		// Load HTTP Request object
		$this->request 			= new Request($this->configs);
				
		// Database handler
		$this->storage 			= new storageObject($this->configs);
		
		// If force_reget
		if ($force_reget == true)
		{
			// Clear previous tokens
			$this->storage->removeUserToken();
			$this->storage->addUserToken();
		}
		
		// If user did not proceed the login page
		$this->step_id 			= $this->storage->getStepID();
		
		// Check which step to take
		switch($this->step_id)
		{
			case 1:
			$this->_step1_requestToken();
			break;
			
			case 2:
			case 3:
			$code_info = $this->storage->getUserToken();
			$this->configs->oauth_token 		= $code_info[1];
			$this->configs->oauth_token_secret 	= $code_info[1];
		
			$this->_step3_redirectUserToLogin();
			break;
			
			case 4:
			die("This user already has keys in the stored");
			break;			
		}
	}
		
	/*
	This function will send a request to netflix for tokens and login page
	*/
	private function _step1_requestToken()
	{
		$api_url = $this->composeVariables('http://api.netflix.com/oauth/request_token', array());

		$this->request->makeRequest($api_url, array());
		
		$response = $this->request->getResponse();

		$this->configs->oauth_token 		= $response->oauth_token;
		$this->configs->oauth_token_secret 	= $response->oauth_token_secret;
		$this->configs->app_name		 	= $response->application_name;
		$this->configs->login_url 			= $response->login_url;
		
		// Update user tokens in DB
		$this->storage->addUserToken($response->oauth_token, $response->oauth_token_secret); 
		$this->storage->setStepID(2);
		
		// Redirect user to login
		$this->_step3_redirectUserToLogin();
	}
	
	/*
	This function will redirect usres to login page
	*/
	
	private function _step3_redirectUserToLogin()
	{
		$app_name 	= $this->request->percentEncode($this->configs->app_name);
		$call_back 	= $this->request->percentEncode($this->configs->app_callback);

		$login_link = 'https://api-user.netflix.com/oauth/login?application_name=' . $app_name;
		$login_link	.= '&oauth_callback=' . $call_back;
		$login_link .= '&oauth_consumer_key= ' .$this->configs->consumer_key . '&oauth_token=' . $this->configs->oauth_token;
		
		$this->storage->setStepID(3);
		
		header("Location: $login_link");
		die();
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