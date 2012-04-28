<?php
/**
 * This is the configuration load object
 *
 * It loads the file configs.ini.php into 
 * an array using parse_ini_string
 *
 * @copyright  2011 Aziz Hussain
 * @version    1.0
 * @link       http://azizsaleh.com
 * @email      azizsaleh@gmail.com
 */ 

class Configuration
{
	public $consumer_key 	= '';
	public $shared_key 		= '';
	public $request_method 	= '';
	
	public $app_name 		= '';
	public $app_callback 	= '';
	
	/* This class reads the configuration file and loads it */
	public function __construct($api_file = NULL)
	{
		if($api_file == NULL)
		{
			$api_file = 'configs.ini.php';
		}
		
		include($api_file);
		
		$configInfo = parse_ini_string($api_configs);
		
		/* Load the configuration options into variable */
		foreach ($configInfo as $optionName => $option_value){
			$this->$optionName = $option_value;
		}
		
		$this->_loadStorageObject();
	}	
	
	/* This function will load the storage object file */
	private function _loadStorageObject()
	{
		require_once(BASE_DIR. 'storage' . DIRECTORY_SEPARATOR . $this->token_storage);
	}
}

// Check if we have parse_ini_string enabled
if (!function_exists('parse_ini_string')) {
	// Create it
	function parse_ini_string($string_body) {
		
		$new_array = array();
		
		preg_match_all('/(.*)\= (.+)/i', $string_body, $results);
		foreach($results[1] as $sub => $config_val) {
			$new_array[trim($config_val)] = str_replace(array('"', "'"), '', trim($results[2][$sub]));
		}

		return $new_array;
	}	
}