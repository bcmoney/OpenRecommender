<?php
/***************************************************************************
 *                                install.php
 *                            -------------------
 *   begin                : Thursday, Apr 7, 2005
 *   copyright            : (C) 2001 The phpBB Group, (C) 2005 Stephane DROUX
 *
 *   Notice: Most of this file is taken from the install.php file of the phpBB v2 forum
 *   The stylesheet associated is also a modified version of the subSilver.css stylesheet that comes with the phpBB v2 forum
 *   
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

if (@file_exists('../config.php'))
{
	include('../config.php');
}

$already_installed = false;
if (defined("VOGOO_INSTALLED"))
{
	$already_installed = true;
}
if (!isset($HTTP_POST_VARS) && isset($_POST))
{
	$HTTP_POST_VARS = $_POST;
}

$dbms = isset($HTTP_POST_VARS['dbms']) ? $HTTP_POST_VARS['dbms'] : ''; 
$dbhost = (!empty($HTTP_POST_VARS['dbhost'])) ? $HTTP_POST_VARS['dbhost'] : 'localhost';
$dbuser = (!empty($HTTP_POST_VARS['dbuser'])) ? $HTTP_POST_VARS['dbuser'] : '';
$dbpasswd = (!empty($HTTP_POST_VARS['dbpasswd'])) ? $HTTP_POST_VARS['dbpasswd'] : '';
$dbname = (!empty($HTTP_POST_VARS['dbname'])) ? $HTTP_POST_VARS['dbname'] : '';

// Define schema info
$available_dbms = array(
	'mysql'=> array(
		'LABEL'                 => 'MySQL 3.x'
	),
	'mysql4' => array(
		'LABEL'                 => 'MySQL 4.x'
	),
	'postgres' => array(
		'LABEL'                 => 'PostgreSQL 7.x'
	)
/*	'mssql' => array(
		'LABEL'                 => 'MS SQL Server 7/2000',
	),
	'msaccess' => array(
		'LABEL'                 => 'MS Access [ ODBC ]',
	),
	'mssql-odbc' => array(
		'LABEL'                 => 'MS SQL Server [ ODBC ]',
	)*/
);

$valid_dbms = false;
$dbms_select = '<select name="dbms">';
while (list($dbms_name, $details) = @each($available_dbms))
{
	if ($dbms_name == $dbms)
	{
		$selected = ' selected';
		$valid_dbms = true;
	}
	else
	{
		$selected = '';
	}
	$dbms_select .= '<option value="' . $dbms_name . '"' . $selected . '>' . $details['LABEL'] . '</option>';
}
$dbms_select .= '</select>';

if ($already_installed)
{
	$maintitle = 'VOGOO PHP Lib is already installed.';
	$text = 'Thank you for choosing VOGOO PHP Lib.';
	$form = false;
}
else if (!$valid_dbms)
{
	$maintitle = 'Welcome to VOGOO PHP Lib Installation.';
	$text = 'Thank you for choosing VOGOO PHP Lib. In order to complete this install please fill out the details requested below. Please note that the database you install into should already exist.'; // 'If you are installing to a database that uses ODBC, e.g. MS Access you should first create a DSN for it before proceeding.';
	$form = true;
}
else
{
	$maintitle = 'VOGOO PHP Lib Installation Finished.';
	$error = false;
	$msg = '';
	// Run the installation process
	define("VOGOO_DIR","../");
	define("VOGOO","installation");
	$vg_dbms = $dbms;

	include("../db.php");
	$db = new vg_sql_db($dbhost, $dbuser, $dbpasswd, $dbname, false);
	if(!$db->db_connect_id)
	{
		$error = true;
		$msg .= 'CRITICAL ERROR : Could not connect to the database.<br />';
	}
	switch ($dbms)
	{
		case 'msaccess':
		case 'mssql-odbc':
			break;

		case 'mssql':
			$sql = '';
			break;

		case 'mysql':
		case 'mysql4':
			$sql = <<<EOF
CREATE TABLE vogoo_ratings(member_id INT NOT NULL,product_id INT NOT NULL,category INT NOT NULL,rating FLOAT NOT NULL,ts TIMESTAMP);
CREATE INDEX vogoo_ratings_mix ON vogoo_ratings(member_id);
CREATE INDEX vogoo_ratings_pix ON vogoo_ratings(product_id);
CREATE UNIQUE INDEX vogoo_ratings_mpix ON vogoo_ratings(member_id,product_id,category);
CREATE TABLE vogoo_ads(ad_id INT NOT NULL,category INT NOT NULL,mini INT NOT NULL,KEY ad_id(ad_id));
CREATE TABLE vogoo_ads_products(ad_id INT NOT NULL,category INT NOT NULL,product_id INT NOT NULL,KEY ad_id(ad_id),KEY category(category));
CREATE TABLE vogoo_links(item_id1 INT NOT NULL,item_id2 INT NOT NULL,category INT NOT NULL,cnt INT,diff_slope FLOAT);
CREATE INDEX vogoo_links_i1ix ON vogoo_links(item_id1);
CREATE INDEX vogoo_links_i2ix ON vogoo_links(item_id2);
CREATE UNIQUE INDEX vogoo_links_ix ON vogoo_links(item_id1,item_id2,category);
EOF;
			break;

		case 'postgres':
			$sql = <<<EOF
BEGIN;
CREATE TABLE vogoo_ratings(member_id INTEGER,product_id INTEGER,category INTEGER,rating FLOAT,ts TIMESTAMP);
CREATE INDEX vogoo_ratings_member_id_index ON vogoo_ratings(member_id);
CREATE INDEX vogoo_ratings_product_id_index ON vogoo_ratings(product_id);
CREATE INDEX vogoo_ratings_category_index ON vogoo_ratings(category);
CREATE TABLE vogoo_ads(ad_id INTEGER,category INTEGER,mini INTEGER);
CREATE INDEX vogoo_ads_ad_id_index ON vogoo_ads(ad_id);
CREATE TABLE vogoo_ads_products(ad_id INTEGER,category INTEGER,product_id INTEGER);
CREATE INDEX vogoo_ads_products_ad_id_index ON vogoo_ads_products(ad_id);
CREATE INDEX vogoo_ads_products_category_index ON vogoo_ads_products(category);
CREATE TABLE vogoo_links(item_id1 INTEGER,item_id2 INTEGER,category INTEGER,cnt INT,diff_slope FLOAT);
CREATE INDEX vogoo_links_item_id1_index ON vogoo_links(item_id1);
CREATE INDEX vogoo_links_item_id2_index ON vogoo_links(item_id2);
CREATE INDEX vogoo_links_category ON vogoo_links(category);
COMMIT;
EOF;
			break;
	}

	// Write out the config file.
	$config_data = '<?php'."\n";
	$config_data .= "// VOGOO LIB auto-generated config file\n// Do not change anything in this file!\n\n";
	$config_data .= '$vg_dbms = \'' . $dbms . '\';' . "\n";
	$config_data .= '$vg_dbhost = \'' . $dbhost . '\';' . "\n";
	$config_data .= '$vg_dbname = \'' . $dbname . '\';' . "\n";
	$config_data .= '$vg_dbuser = \'' . $dbuser . '\';' . "\n";
	$config_data .= '$vg_dbpasswd = \'' . $dbpasswd . '\';' . "\n";
	$config_data .= 'define(\'VOGOO_INSTALLED\', true);'."\n";
	$config_data .= '?' . '>'; // Done this to prevent highlighting editors getting confused!

	@umask(0111);
	if (!$error)
	{
		if (!($fp = @fopen('../config.php', 'w')))
		{
			$error = true;
			$msg .= 'Could not write the config.php file. Please check the write permission for this file.<br />';
		}
		else
		{
			$result = @fputs($fp, $config_data, strlen($config_data));
			@fclose($fp);
		}
	}

	if (!$error)
	{
		$sqllines = explode("\n",$sql);
		foreach ($sqllines as $line)
		{
			if (!$db->sql_query($line))
			{
				$error = true;
				$msg .= 'Could not create the database tables required by the VOGOO LIB.<br />';
			}
		}
	}

	if (!$error)
	{
		$text = 'VOGOO PHP LIB installation is complete. Thank you for choosing the VOGOO PHP Lib.<br />You can now close this window and start using the VOGOO PHP Lib in your own PHP scripts.';
	}
	else
	{
		$text = 'At least an error occured during the installation process. Please check the errors listed below:<br />';
		$text .= $msg;
	}
	$form = false;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>Welcome to VOGOO PHP LIB installation</title>
<link rel="stylesheet" href="./style.css" type="text/css">
</head>
<body bgcolor="#DDDDDD" text="#000000" link="#006699" vlink="#5584AA">
<table width="100%" border="0" cellspacing="0" cellpadding="10" align="center">
<tr>
	<td class="bodyline" width="100%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td><img src="../img/logo.gif" border="0" alt="Vogoo" vspace="1" /></td>
			<td align="center" width="100%" valign="middle"><span class="maintitle"><?php echo $maintitle; ?></span></td>
		</tr>
		</table></td>
	</tr>
	<tr>
		<td><br /><br /></td>
	</tr>
	<tr>
		<td colspan="2"><table width="90%" border="0" align="center" cellspacing="0" cellpadding="0">
		<tr>
			<td><span class="gen"><?php echo $text; ?></span></td>
		</tr>
		</table></td>
	</tr>
	<tr>
		<td><br /><br /></td>
	</tr>
<?php if ($form) { ?>
	<tr><td width="100%">
		<form action="install.php" name="install" method="post">
		<table width="100%" cellpadding="2" cellspacing="1" border="0" class="forumline">
		<tr>
			<th colspan="2">Database configuration</th>
		</tr>
		<tr>
			<td class="row1" align="right" width="30%"><span class="gen">Database Type: </span></td>
			<td class="row2"><?php echo $dbms_select; ?></td>
		</tr>
		<tr>
			<td class="row1" align="right" width="30%"><span class="gen">Database Server Hostname / DSN: </span></td>
			<td class="row2"><input type="text" name="dbhost" value="<?php echo ($dbhost != '') ? $dbhost : ''; ?>" /></td>
		</tr>
		<tr>
			<td class="row1" align="right" width="30%"><span class="gen">Your Database Name: </span></td>
			<td class="row2"><input type="text" name="dbname" value="<?php echo ($dbname != '') ? $dbname : ''; ?>" /></td>
		</tr>
		<tr>
			<td class="row1" align="right" width="30%"><span class="gen">Database Username: </span></td>
			<td class="row2"><input type="text" name="dbuser" value="<?php echo ($dbuser != '') ? $dbuser : ''; ?>" /></td>
		</tr>
		<tr>
			<td class="row1" align="right" width="30%"><span class="gen">Database Password: </span></td>
			<td class="row2"><input type="password" name="dbpasswd" value="<?php echo ($dbpasswd != '') ? $dbpasswd : ''; ?>" /></td>
		</tr>
		<tr>
			<td class="catBottom" align="center" colspan="2"><input class="mainoption" type="submit" value="Start Install" /></td>
		</tr>
		</table></form></td>
	</tr>
<?php } ?>
	</table></td>
</tr>
</table>

</body>
</html>
