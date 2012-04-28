<?php
/**
 * This is the request object that actually makes the CURL calls
 *
 *
 * @copyright  2011 Aziz Hussain
 * @version    1.0
 * @link       http://azizsaleh.com
 * @email      azizsaleh@gmail.com
 */ 

class Request
{
	/* Private variables */
	private $response;
	private $raw_response;
	private $configs;	
	private $return_type = 'xml';
	
	
	/* Constructor, just copy over the config variables */
	public function __construct(&$configs)
	{
		$this->configs = $configs;
	}		
	
	/*
	Set return type
	
	@param		string		type
	*/
	public function setReturnType($type = NULL)
	{
		if (!empty($type)) {
			$this->return_type = $type;	
		}
	}
	
	/* 
	Get formatted response 
	
	@return		array
	*/
	public function getResponse()
	{
		return $this->response;
	}
	
	/* 
	Get raw response 
	
	@return		array
	*/
	public function getRawResponse()
	{
		return $this->raw_response;
	}
	
	/*
	This function will make the request
	
	@param		string		api_link
	@param		array		post_data
	
	@return		array
	*/
	public function makeRequest($api_link, $request_data)
	{
		/* Execute CURL */
		$curl_handler = curl_init();     		        
		
		if(count($request_data) > 0) {
			curl_setopt($curl_handler, CURLOPT_URL, $api_link . '?' . $request_data);
		} else {
			curl_setopt($curl_handler, CURLOPT_URL, $api_link);
		}
		
		curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, true);			
        $this->raw_response = curl_exec($curl_handler);        
		
		$errorMessage = curl_errno($curl_handler);
        $errorNumber = curl_error($curl_handler);
		
        curl_close($curl_handler);
		
		if ($this->raw_response === false)
		{
			echo "Error while executing CURL: # $errorNumber ($errorMessage)";
		}

		/* Format and return response */
		$this->formatResponse();
		
		return $this->response;
	}
	
	/* 
	This function will HMAC_SHA1 the signatures 
	
	@param		string		data
	@param		string		secret_key
	
	@return		string
	*/
	public function HMAC_SHA1 ($data, $secret_key) {
    	return base64_encode (hash_hmac ('sha1', $data, $secret_key, true));
	}
	
	/* 
	This function will encode params 
	
	@param		string		data
	
	@return		string		data
	*/
	public function percentEncode($data)
	{
		return urlencode($data);
	}	
	
	/* This function will format raw response */
	private function formatResponse()
	{
		// IF XML, parse it
		if ($this->return_type == 'xml')
		{
			$xml_parser = xml_parser_create();
			xml_parse_into_struct($xml_parser, $this->raw_response, $vals, $index);
			xml_parser_free($xml_parser);
			
			$this->response = array($vals, $index);
		}
		// If json return format, decode
		if ($this->return_type == 'json')
		{
			$this->response = json_decode($this->raw_response);
		}
		
		// URL variables
		if ($this->return_type == 'xml')
		{
			parse_str($this->raw_response, $this->response);
			$this->response = (object) $this->response;
		}
	}
}