<?php

/*
This file is designed to full phpMyID into thinking that there is a single user.  These details will never work as the password isn't hashed - however, when phpMyID is trying to authorize the account, it will auto switch to a different config file.  This is just to fool the setup checks so that I didn't have to rewrite all of the phpmyid.php file.
*/

$GLOBALS['profile'] = array(
	'auth_username' => 'phpmyopenid',
	'auth_password' => 'phpmyopenid',
	'auth_realm' => 'phpmyid'
);

?>