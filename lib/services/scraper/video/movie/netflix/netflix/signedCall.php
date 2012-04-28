<?php
/**
 * This file is the object that makes the calls to
 * Methods with signed protection types
 *
 * License: LGPL
 *
 * @copyright  2011 Aziz Hussain
 * @version    1.0
 * @link       http://azizsaleh.com
 * @email      azizsaleh@gmail.com
 */ 


class signedCall
{
	public $request;
	public $configs;
	
	/* Constructor */
	public function __construct(&$this_call, &$configs)
	{
		$this->request = $this_call;	
		$this->configs = $configs;
	}
	
	/* Call Maker */
	public function makeCall($api_url, $request_params)
	{
		$api_url = $this->composeVariables($api_url, $request_params);
		
		if (isset($request_params['output'])) {
			$this->request->setReturnType($request_params['output']);
		}

		$this->request->makeRequest($api_url, array());
	}
	
	/*
	This function will compose variables
	and add required keys depending on call
	
	@param		string	api_url
	@param		array	request_vars	
	
	@return		string
	*/
	private function composeVariables($api_url, $request_vars)
	{		
		$auth_object = new OAuthSimple($this->configs->consumer_key, $this->configs->shared_key);  
		$request = $auth_object->sign(
				array(	'path'			=> $api_url,  
						'parameters' 	=> $request_vars,  
						'signatures' 	=> array(
								'consumer_key'	=> $this->configs->consumer_key,  
								'shared_secret'	=> $this->configs->shared_key 
				)));

		return $request['signed_url'];
	}
	
}