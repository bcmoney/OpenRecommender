<?php
/**
 * This is the configuration file
 *
 * Change anything, just make sure that:
 
 * 1) Values are enclosed in double quotes
 * 2) No single quotes in the configuration
 * 3) Comments start with a semicolon (;)
 * 4) You can add config blocks [config_name] for readability purposes only
 * 5) If you are storing tokens in files, make sure that the file directory is writable
 
 *
 * @copyright  2011 Aziz Hussain
 * @version    1.0
 * @link       http://azizsaleh.com
 * @email      azizsaleh@gmail.com
 */ 

if (!defined('BASE_DIR')) {
	die('Invalid Acces');
}

$api_configs = '



[api_configs]
; This is your Netflix Key
consumer_key 	= "qh68ecaeu3ayy265m4ye258y"

; This is your Netflix Shared Secret
shared_key		= "pvNtMRaWSy"

; Your application name
app_name		= "BCmoney MobileTV"

; Token callback (page which requests final tokens)
app_callback	= "token_response.php" ; "http://www.azizsaleh.com/work/Netflix-API/token_response.php"

; Redirect page after tokens are gotten
app_redirect	= "test.php" ; "http://www.azizsaleh.com/work/Netflix-API/example.php?demo=3"

; If using Protected calls, you will need to store the user ID and authorization
; token somewhere (unless you want the user to login everytime)

; Storage object (located in storage directory)
; For DB, use "storageObject.mysql.php"
token_storage 	= "storageObject.file.php"

; Token directory (location from application bath)
; Tokens stored in file in .session_id as TOKEN, TOKEN_SECRET, STEP_ID
token_dir 		= token_directory

[db_info]
;DB Username
db_hostname		= localhost
db_username		= root
db_password		= ""
db_database		= test

; Table & Fields info
db_table		= user_tokens
db_user_id		= session_id
db_token		= token_string
db_token_secret	= token_secret_string
db_step_id		= step_id
db_netflix_id	= netflix_id

; Any other configurations you add, will be accessible from the config object
[other_info]

';