<?php

/**
 * phpMyOpenID - An easy to use installer for phpMyOpenID
 *
 * @package phpMyOpenID
 * @author Ben Dodson <ben@bendodson.com>
 * @copyright 2007-2008
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License
 * @url http://www.bendodson.com/openid/
 * @version 1.0
 */

session_start();

$realm = 'phpmyid';

$form = $_POST;
if ($form) {
	$username = htmlentities($form['username']);
	$password = ($form['password'] == $form['retypepassword']) ? $form['password'] : '';
	if (!$username) {
		$error = 'fields';
	} else if (!$password) {
		$error = 'password';
	}
	
	if (!$error) {
		
		$config = '<?php' . "\n\n";
		$config .= '$GLOBALS[\'profile\'] = array(' . "\n";
		$config .= "\t" . "'auth_username' => '" . $username . "'," . "\n";
		$config .= "\t" . "'auth_password' => '" . md5($username . ':' . $realm . ':' . $password) . "'," . "\n";
		$config .= "\t" . "'auth_realm' => '" . $realm . "'" . "\n";
		$config .= ');' . "\n\n";
		
		$filename = 'config/' . $username . '.php';
		if (!$handle = fopen($filename, 'w')) {
			echo "Cannot open file ($filename)";
			exit;
		}
		if (fwrite($handle, $config) === false) {
			echo "Cannot write to file ($filename)";
			exit;
		}
		
		$_SESSION['username'] = $username;
		
		header('location: ./registration.php');
		exit;
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>phpMyOpenID - Step 1</title>
</head>
<body>
	<h1>phpMyOpenID Installation</h1>
	<h2>Step One</h2>
	<p>Please choose a username and password for your new OpenID account:</p>
	
	<?php
	
	if ($error == 'fields') {
		echo '<p>You must fill in all of the fields</p>';
	}
	
	if ($error == 'password') {
		echo '<p>Your passwords didn\'t match!</p>';
	}
	
	?>
	
	<form method="post" action="">
		<label for="username">Username:</label>
		<input type="text" class="text" name="username" id="username" value="<?php echo $username ?>" />
		<br />
		
		<label for="password">Password:</label>
		<input type="password" class="text" name="password" id="password" value="" />
		<br />
		
		<label for="retypepassword">Retype Password:</label>
		<input type="password" class="text" name="retypepassword" id="retypepassword" value="" />
		<br />
		
		<input type="submit" class="submit" value="Proceed to Step 2 &rarr;" />
	</form>	
		
