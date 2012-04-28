<?php
/**
 * This file is the object that makes the calls to
 * Methods with open types
 *
 * License: LGPL
 *
 * @copyright  2011 Aziz Hussain
 * @version    1.0
 * @link       http://azizsaleh.com
 * @email      azizsaleh@gmail.com
 */ 


class nonAuthenticatedCall
{
	public $request;
	public $configs;
	
	/* Constructor */
	public function __construct( &$this_call, &$configs)
	{
		$this->request = $this_call;	
		$this->configs = $configs;
	}
	
	/* Call Maker */
	public function makeCall($api_url, $request_params)
	{
		$request_vars = $this->composeVariables($request_params);

		if (isset($request_params['output'])) {
			$this->request->setReturnType($request_params['output']);
		}

		$this->request->makeRequest($api_url, $request_vars);
	}
	
	/*
	This function will compose variables
	and add required keys depending on call
	
	@param		array	request_vars
	
	@return		array
	*/
	private function composeVariables($request_vars)
	{
		$request = '';

		// All calls require key
		$request .= 'oauth_consumer_key=' . $this->configs->consumer_key . '&';
					
		foreach($request_vars as $var_val => $var_value)
		{
			$request .= $var_val . '=' . $this->request->percentEncode ($var_value) . '&';
		}
		
		$request = substr($request, 0, -1);

		return $request;
	}
	
}