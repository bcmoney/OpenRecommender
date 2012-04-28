<?php
/**
 * This is a sample implementation for storage of keys
 * In File storage
 *
 * NOTE: If you are using any other file storage types than regular files, you can resuse this file,
 * just make sure that all methods are implemented using your fav file manager object
 *
 * License: LGPL
 *
 * @copyright  2011 Aziz Hussain
 * @version    1.0
 * @link       http://azizsaleh.com
 * @email      azizsaleh@gmail.com
 */ 
class storageObject
{
	/* Variable holders */
	private $token_file;
	private $newLine = PHP_EOL;
	
	/* 
	Construct DB 
	
	@param		array	configs
	
	@return		null
	*/
	public function __construct($configs)
	{
		$this->token_file = BASE_DIR . $configs->token_dir . DIRECTORY_SEPARATOR;
		
		if (!is_readable ($this->token_file)) {
			die ('Unable to read from folder: ' . $this->file_name);	
		}
		
		if (!is_writable ($this->token_file)) {
			die ('Unable to write to folder: ' . $this->file_name);	
		}
	}
	
	/* 
	Function to add user token record 
	
	@param		string	user_token
	@param		string	token_secret
	@param		int		step_id
	@param		string	netflix_user_id
	
	@return		boolean
	*/
	public function addUserToken ($user_token = '', $token_secret = '', $step_id = 1, $netflix_user_id = '')
	{	
		settype($step_id, "integer");
		
		// Write info to the file
		$this->_my_write($this->token_file . '.' . session_id(), $user_token . ',' . $token_secret . ',' . $step_id . ',' . $netflix_user_id, 'w');
	}
	
	/* 
	Function to retrieve user token 
	
	@return		array
	*/
	public function getUserToken() 
	{
		if (!file_exists($this->token_file . '.' . session_id())) {
			return false;
		}
		$file = fopen($this->token_file . '.' . session_id(), 'r');
		
		if (!$file){
			die ('Unable to read from file: ' . session_id());	
		}
		
		$user_info 			= explode(',', fgets($file));
			
		$user_token 		= trim($user_info[0]);
		$token_secret 		= trim($user_info[1]);
		$step_id 			= trim($user_info[2]);
		$netflix_user_id	= trim($user_info[3]);
			
		return array($user_token, $token_secret, $step_id, session_id(), $netflix_user_id);
	}
	
	/*
	This function will check if user exists in file
	if so, remove so we insert new token
	
	@return		true
	*/
	public function removeUserToken()
	{
		if (!is_file($this->token_file . '.' . session_id())) {
			return true;
		}
		
		return unlink($this->token_file . '.' . session_id());
	}
	
	/*
	Get step id for a user
	
	@return		int		
	*/
	public function getStepID()
	{
		$info = $this->getUserToken();

		return $info[2];
	}
	
	/*
	Set step id for a user
	
	@param		int		step_id
	
	@return		boolean
	*/
	public function setStepID($step_id)
	{
		settype($step_id, "integer");
		
		$info = $this->getUserToken();
		
		return $this->addUserToken($info[0], $info[1], $step_id, $info[3]);
	}
	
	/*
	This function acts as a wrapper to fopen
	
	@param		string	file_name
	@param		string	file_info
	@param		string	access_type
	
	@return		boolean
	*/
	private function _my_write($file_name, $file_info, $access_type)
	{
		$f = fopen($file_name, $access_type);
		if (!$f) { 
			die("Unable to access ($access_type) to $file_name");
		}
		fwrite($f, $file_info);
		fclose($f);
		
		return true;
	}
	
	/* 
	This function escapes inputs before inserting 
	
	@param		string		variable
	
	@return		string
	*/
	private function escape_string($variable)
	{
		return preg_replace('/[^0-9a-zA-Z\.\-\_\+]/','',$variable);
	}
}