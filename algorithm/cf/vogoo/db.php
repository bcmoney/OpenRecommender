<?php
/***************************************************************************
 *                                 db.php
 *                            -------------------
 *   begin                : Saturday, Feb 13, 2001
 *   copyright            : (C) 2001 The phpBB Group
 *   email                : support@phpbb.com
 *
 *   $Id: db.php,v 1.2 2005/04/06 11:36:09 sdroux Exp $
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

//
// Important notice: file modified by Stephane DROUX
// The original version of the file provided by The phpBB Group can be downloaded from http://www.phpbb.com
//

if ( !defined('VOGOO') )
{
	die("Hacking attempt");
}

switch($vg_dbms)
{
	case 'mysql':
	case 'mysql4':
		include(VOGOO_DIR . 'db/mysql.php');
		break;

	case 'postgres':
		include(VOGOO_DIR . 'db/postgres7.php');
		break;

	case 'mssql':
		include(VOGOO_DIR . 'db/mssql.php');
		break;

	case 'oracle':
		include(VOGOO_DIR . 'db/oracle.php');
		break;

	case 'msaccess':
		include(VOGOO_DIR . 'db/msaccess.php');
		break;

	case 'mssql-odbc':
		include(VOGOO_DIR . 'db/mssql-odbc.php');
		break;
}

?>
