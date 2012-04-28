<?php
/**
 * This is a sample implementation for storage of keys
 * In MySQL database.
 *
 * NOTE: If you are using any other DB than MySQL, you can resuse this file,
 * just make sure that all methods are implemented using your fav DB object
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
	private $db_handler;
	private $table_name;
	private $user_field;
	private $token_field;
	private $db_step_id;
	private $db_netflix_id;
	
	/* 
	Construct DB 
	
	@param		array	configs
	
	@return		null
	*/
	public function __construct($configs)
	{
		$this->db_handler = mysql_connect($configs->db_hostname, $configs->db_username, $configs->db_password);
		if (!$this->db_handler) {
			die('Can not connect to database : ' . mysql_error());
		}

		$db_selected = mysql_select_db($configs->db_database, $this->db_handler);
		if (!$db_selected) {
			die ('Can not connect to database : ' . mysql_error());
		}
		/* Set variables */
		$this->table_name 			= $configs->db_table;
		$this->user_field 			= $configs->db_user_id;
		$this->token_field 			= $configs->db_token;
		$this->token_secret_field	= $configs->db_token_secret;
		$this->db_step_id			= $configs->db_step_id;
		$this->db_netflix_id		= $configs->db_netflix_id;
	}
	
	/* 
	Function to add user token record 
	
	@param		string	user_token
	@param		string	token_secret
	@param		int		step_id
	@param		string	netflix_user_id
	
	@return		boolean
	*/
	public function addUserToken($user_token = '', $token_secret = '', $step_id = 1, $netflix_user_id = '')
	{	
		settype($step_id, "integer");
		
		$user_token 	= $this->escape_string($user_token);
		$token_secret 	= $this->escape_string($token_secret);
		
		if (!isset($netflix_userid)) {
			$netflix_userid = '';
		}
		
		$query = "REPLACE INTO 
			`{$this->table_name}` (`{$this->user_field}`, `{$this->token_field}`, 
			`{$this->token_secret_field}`,`{$this->db_step_id}`,`{$this->db_netflix_id}`) 
			VALUES ('" . USER_ID . "', '$user_token', '$token_secret','$step_id','$netflix_userid')";
		
		return mysql_query($query, $this->db_handler);					
	}
	
	/* 
	Function to retrieve user token 
	
	@return		array | boolean=false
	*/
	public function getUserToken() 
	{
		$query = "SELECT `{$this->token_field}`, 
				`{$this->token_secret_field}`, `{$this->db_step_id}`, 
				`{$this->user_field}`, `{$this->db_netflix_id}`
				FROM `{$this->table_name}` " .
				" WHERE `{$this->user_field}` = '" . USER_ID . "'";

		$user_token = mysql_fetch_assoc (mysql_query ($query, $this->db_handler));
		
		if (strlen ($user_token[$this->token_field]) > 0 ) {
			return array($user_token[$this->token_field], 
				$user_token[$this->token_secret_field], $user_token[$this->db_step_id], 
				$user_token[$this->user_field], $user_token[$this->db_netflix_id]);
		} else {
			return false;	
		}
	}
	
	/*
	Remove user token
	
	@return		boolean
	*/
	public function removeUserToken()
	{	
		$query = "DELETE FROM `{$this->table_name}` 				
				WHERE `{$this->user_field}` = '" . USER_ID . "'";
		return mysql_query($query, $this->db_handler);
	}

	
	/*
	Get step id for a user
		
	@return		int		
	*/
	public function getStepID()
	{		
		$query = "SELECT `{$this->db_step_id}`
				FROM `{$this->table_name}` 
				WHERE `{$this->user_field}` = '" . USER_ID . "'";
		$user_token = mysql_fetch_assoc (mysql_query ($query, $this->db_handler));
		
		if (strlen ($user_token[$this->db_step_id]) > 0 ) {
			return $user_token[$this->db_step_id];
		} else {
			return false;	
		}
	}
	
	/*
	Set step id for a user
	
	@param		int		step_id
	
	@return		boolean
	*/
	public function setStepID($step_id)
	{
		settype($step_id, "integer");
		
		$query = "UPDATE `{$this->table_name}` 
				SET `{$this->db_step_id}` = '$step_id' 
				WHERE `{$this->user_field}` = '" . USER_ID . "'";
		return mysql_query($query, $this->db_handler);
	}
	
	/* 
	This function escapes inputs before inserting 
	
	@param		string		variable
	
	@return		string
	*/
	private function escape_string($variable)
	{
		return mysql_real_escape_string($variable, $this->db_handler);
	}
}