<?php 

// start the session
session_start();
$included = false;

// if auth_username has already been defined, then load the appropriate config file
if ($_SESSION['auth_username']) {
	$config = './config/' . $_SESSION['auth_username'] . '.php';
	if (file_exists($config)) {
		require($config);
		$included = true;
	} else {
		$_SESSION = array();
	}
}

if (!$included) {
	
	// Check to see how many config files exist
	$config_files = glob('./config/*.php');

	// If there aren't any config files, then redirect to install script
	if (!$config_files) {
		header('location: ./install.php');
		exit;
	}

	if (count($config_files) > 1) {
		require('./multi_user.php'); // load multi user config
	} else {
		require($config_files[0]); // load single user config
	}
}

require_once ('./phpmyid.php');
exit;