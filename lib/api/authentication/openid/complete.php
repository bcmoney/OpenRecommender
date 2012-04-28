<?php

$form = $_POST;
if ($form['action'] == 'PROTECT') {
	
	@chmod('./install.php',0400);
	@chmod('./registration.php',0400);
	@chmod('./complete.php',0400);
	
	/* uncomment to delete the files instead of changing permissions
	@unlink('./install.php');
	@unlink('./registration.php');
	@unlink('./complete.php');
	*/
	
	header('location: ./index.php');
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>phpMyOpenID - Installation Complete</title>
</head>
<body>
	<h1>phpMyOpenID Installation</h1>
	<h2>Installation Complete</h2>
	<p>Your OpenID should now be operational! <a href="./install.php">Add another user?</a></p>
	
	<p>In order to use your domain name as your OpenID, you should now add the following tags to the index page of your website:</p>
	
	<?php $domain = ($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'your-domain.com' ?>
	<code>
	&lt;link rel=&quot;openid.server&quot; href=&quot;http://<?php echo $domain ?>/phpmyopenid/index.php&quot; /&gt;<br />
	&lt;link rel=&quot;openid.delegate&quot; href=&quot;http://<?php echo $domain ?>/phpmyopenid/index.php&quot; /&gt;
	</code>
		
	<?php if (strtolower($_SERVER['SERVER_SOFTWARE']) != 'apache') { ?>
		<p><strong>Note:</strong> It looks as if you are not running PHP on an Apache server.  If this is the case and you are running PHP in CGI mode, you may need to add some rules to your <code>.htaccess</code> file in order for phpMyOpenID to function properly.  A selection of these rules may be found <a href="./htaccess.txt">here</a>.</p>
	<?php } ?>
		
	  <p>Once you have finished <a href="./install.php">adding users</a> you should protect your phpMyOpenID installation by setting the installation files to chmod 0400 (you can do this by clicking the button below!)</p>
	
	<form method="post" action="">
		<input type="hidden" name="action" value="PROTECT" />
		<input type="submit" class="submit" value="Protect phpMyOpenID" />
	</form>