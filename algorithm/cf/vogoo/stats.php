<?php
/***************************************************************************
 *                                 stats.php
 *                            -------------------
 *   begin                : Sunday, Aug 5, 2007
 *   copyright            : (C) 2007 Stephane DROUX
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


if (!defined('VOGOO_DIR'))
{
	define('VOGOO_DIR', dirname(__FILE__) . "/");
}
include (VOGOO_DIR."vogoo.php");

if (!defined("VOGOO"))
{
	die("Please include vogoo.php before any use of stats.php !");
}

if (!defined("VOGOO_STATS"))
{

define("VOGOO_STATS","vogoo");

class vogoo_stats_class
{
	function num_members($cat = 1)
	{
		global $vogoo;
		$sql = <<<EOF
SELECT COUNT(DISTINCT member_id) cnter
FROM vogoo_ratings
WHERE category = {$cat}
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$row = $vogoo->db->sql_fetchrow($result);
		$nr = $row['cnter'];
		$vogoo->db->sql_freeresult($result);
		return $nr;
	}

	function members($cat = 1)
	{
		global $vogoo;
		$sql = <<<EOF
SELECT DISTINCT member_id
FROM vogoo_ratings
WHERE category = {$cat}
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$arr = array();
		while ($row = $vogoo->db->sql_fetchrow($result))
		{
			$arr[] = $row['member_id'];
		}
		$vogoo->db->sql_freeresult($result);
		return $arr;
	}

	function num_products($cat = 1)
	{
		global $vogoo;
		$sql = <<<EOF
SELECT COUNT(DISTINCT product_id) cnter
FROM vogoo_ratings
WHERE category = {$cat}
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$row = $vogoo->db->sql_fetchrow($result);
		$nr = $row['cnter'];
		$vogoo->db->sql_freeresult($result);
		return $nr;
	}

	function products($cat = 1)
	{
		global $vogoo;
		$sql = <<<EOF
SELECT DISTINCT product_id
FROM vogoo_ratings
WHERE category = {$cat}
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$arr = array();
		while ($row = $vogoo->db->sql_fetchrow($result))
		{
			$arr[] = $row['product_id'];
		}
		$vogoo->db->sql_freeresult($result);
		return $arr;
	}

	function categories()
	{
		global $vogoo;
		$sql = <<<EOF
SELECT DISTINCT category
FROM vogoo_ratings
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$arr = array();
		while ($row = $vogoo->db->sql_fetchrow($result))
		{
			$arr[] = $row['category'];
		}
		$vogoo->db->sql_freeresult($result);
		return $arr;
	}

	function member_ratings_intervals($member_id,$max,$cat = 1)
	{
		if (!isset($member_id) || !is_numeric($member_id) || !isset($max) || !is_numeric($max))
		{
			return false;
		}
		global $vogoo;
		$sql = <<<EOF
SELECT ROUND(rating * {$max}) value, count(*) cnter
FROM vogoo_ratings
WHERE member_id={$member_id}
AND category = {$cat}
AND rating >= 0.0
GROUP BY value
EOF;
		if (!($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$arr = array();
		$i=0;
		$row = $vogoo->db->sql_fetchrow($result);
		while ($i <= $max)
		{
			if ($row && $row['value'] == $i) {
				$arr[] = $row['cnter'];
				$row = $vogoo->db->sql_fetchrow($result);
			} else {
				$arr[] = 0;
			}
			$i++;
		}
		$vogoo->db->sql_freeresult($result);
		return $arr;
	}

	function product_ratings_intervals($product_id,$max,$cat = 1)
	{
		if (!isset($product_id) || !is_numeric($product_id) || !isset($max) || !is_numeric($max))
		{
			return false;
		}
		global $vogoo;
		$sql = <<<EOF
SELECT ROUND(rating * {$max}) value, count(*) cnter
FROM vogoo_ratings
WHERE product_id={$product_id}
AND category = {$cat}
AND rating >= 0.0
GROUP BY value
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$arr = array();
		$i=0;
		$row = $vogoo->db->sql_fetchrow($result);
		while ($i <= $max)
		{
			if ($row && $row['value'] == $i) {
				$arr[] = $row['cnter'];
				$row = $vogoo->db->sql_fetchrow($result);
			} else {
				$arr[] = 0;
			}
			$i++;
		}
		$vogoo->db->sql_freeresult($result);
		return $arr;
	}
}

$vogoo_stats = new vogoo_stats_class;

} // ... defined
?>
