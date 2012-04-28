<?php
/**
 * This file is the object that makes the calls to
 * Methods with protected types
 *
 * License: LGPL
 *
 * @copyright  2011 Aziz Hussain
 * @version    1.0
 * @link       http://azizsaleh.com
 * @email      azizsaleh@gmail.com
 */ 

class protectedCall
{
	public $request;
	public $configs;
	
	public $storage;
	public $user_id;
	
	/* Constructor */
	public function __construct (&$this_call, &$configs, &$storage)
	{
		$this->request = $this_call;	
		$this->configs = $configs;
		$this->storage = $storage;
	}
	
	/* Call Maker */
	public function makeCall ($api_url, $request_params = array())
	{
		// Before making the call, check if user tokens exists
		$this->getUserTokens($api_url, $request_params);		
		
		$api_url = $this->composeVariables($api_url, $request_params);

		if (isset($request_params['output'])) {
			$this->request->setReturnType($request_params['output']);
		}

		$this->request->makeRequest($api_url, array());
	}
	
	/*
	This function will request a request token and secret keys
	
	@param		string	api_url
	@param		array	request_params
	
	@return		int|boolean
	*/
	public function getUserTokens(&$api_url, $request_params = array())
	{
		$user_token = $this->storage->getUserToken();
		
		$api_url = str_replace('%user_id%', urlencode($user_token[4]), $api_url);
		
		// If no token, create one
		if($user_token === false) {
			$token_get = new getToken(true, $this->configs);
		}

		// If user still in step 1 or 2, go to next step
		if($user_token[2] != 3)
		{
			// We have the token stored, return it
			$this->configs->oauth_token 		= $user_token[0];
			$this->configs->oauth_token_secret 	= $user_token[1];
			$this->configs->netflix_userid		= $user_token[4];
			return;
		}
		
		// If step three, check we can access the tokens
		if($user_token[2] == 3 && $user_token[4] != '') {						
			$api_url = $this->composeVariables($api_url, $request_params);
			
			if (isset($request_params['output'])) {
				$this->request->setReturnType($request_params['output']);
			}
		
			$this->request->makeRequest($api_url, array());
			
			// If we do not have access yet, then user did not complete the login
			if($this->request->getResponse()->error == 1 ) {
				$token_get = new getToken(false, $this->configs);
			}
		} else {
			$token_get = new getToken(false, $this->configs);
		}
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