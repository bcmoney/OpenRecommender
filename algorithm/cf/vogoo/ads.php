<?php
/***************************************************************************
 *                                 ads.php
 *                            -------------------
 *   begin                : Wednesday, Aug 22, 2005
 *   copyright            : (C) 2005 Stephane DROUX
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
	die("Please include vogoo.php before any use of ads.php !");
}

if (!defined("VOGOO_ADS"))
{

define("VOGOO_ADS","vogoo");

class vogoo_ads_class
{
	function record_ad($ad_id,$mini,&$products,$cat = 1)
	{
		global $vogoo;

		if (!isset($ad_id) || !is_numeric($ad_id) || !isset($products) || $mini < 1)
		{
			return false;
		}
		$sql = <<<EOF
SELECT mini
FROM vogoo_ads
WHERE ad_id = {$ad_id} AND category = {$cat}
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}

		$nr = $vogoo->db->sql_numrows($result);
		$vogoo->db->sql_freeresult($result);
		if ($nr == 1)
		{
			$sql = <<<EOF
UPDATE vogoo_ads
SET mini = {$mini}
WHERE ad_id = {$ad_id} AND category = {$cat}
EOF;
			if ( !($result = $vogoo->db->sql_query($sql)) )
			{
				return false;
			}
			if ($vogoo->db->sql_affectedrows() != 1)
			{
				return false;
			}
		}
		else if ($nr == 0)
		{
			$sql = <<<EOF
INSERT INTO vogoo_ads(ad_id,category,mini)
VALUES ({$ad_id},{$cat},{$mini})
EOF;
			if ( !($result = $vogoo->db->sql_query($sql)) )
			{
				return false;
			}
			if ($vogoo->db->sql_affectedrows() != 1)
			{
				return false;
			}
		}
		else
		{
			return false;
		}
		$sql = <<<EOF
DELETE FROM vogoo_ads_products
WHERE ad_id = {$ad_id} AND category = {$cat}
EOF;
		if ( !($vogoo->db->sql_query($sql)) )
		{
			return false;
		}

		foreach ($products as $p_id) {
			$sql = <<<EOF
INSERT INTO vogoo_ads_products(ad_id,category,product_id)
VALUES({$ad_id},{$cat},{$p_id})
EOF;
			if ( !($result = $vogoo->db->sql_query($sql)) )
			{
				return false;
			}
			if ($vogoo->db->sql_affectedrows() != 1)
			{
				return false;
			}
		}

		return true;
	}

	function delete_ad($ad_id,$cat = 1)
	{
		global $vogoo;
		if (!isset($ad_id) || !is_numeric($ad_id))
		{
			return false;
		}
		$sql = <<<EOF
DELETE FROM vogoo_ads_products
WHERE ad_id = {$ad_id}
AND category = {$cat}
EOF;
		if ( !($vogoo->db->sql_query($sql)) )
		{
			return false;
		}

		$sql = <<<EOF
DELETE FROM vogoo_ads
WHERE ad_id = {$ad_id}
AND category = {$cat}
EOF;
		if ( !($vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		return true;
	}

	function get_ad_info($ad_id,$cat = 1)
	{
		global $vogoo;
		if (!isset($ad_id) || !is_numeric($ad_id))
		{
			return false;
		}
		$ret = array();
		$sql = <<<EOF
SELECT mini
FROM vogoo_ads
WHERE ad_id = {$ad_id} AND category = {$cat}
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		if ($vogoo->db->sql_numrows($result) != 1)
		{
			$vogoo->db->sql_freeresult($result);
			$ret['mini'] = 0;
			$ret['product_ids'] = array();
		}
		else
		{
			$row = $vogoo->db->sql_fetchrow($result);
			$ret['mini'] = $row['mini'];
			$vogoo->db->sql_freeresult($result);

			$sql = <<<EOF
SELECT product_id
FROM vogoo_ads_products
WHERE ad_id = {$ad_id} AND category = {$cat}
EOF;
			if ( !($result = $vogoo->db->sql_query($sql)) )
			{
				return false;
			}
			$ret['product_ids'] = array();
			while ($row = $vogoo->db->sql_fetchrow($result))
			{
				$ret['product_ids'][] = $row['product_id'];
			}

		}
		return $ret;
	}

	// {{{ Member and visitor
	function member_targeted_ads($member_id,$cat = 1)
	{
		global $vogoo;
		if (!isset($member_id) || !is_numeric($member_id))
		{
			return false;
		}
		$ret = array();

		$threshold = VG_THRESHOLD_RATING;
		$sql = <<<EOF
SELECT a.ad_id,a.mini,count(p.product_id) as cnt
FROM vogoo_ads a,vogoo_ratings r,vogoo_ads_products p
WHERE r.member_id={$member_id}
AND p.product_id=r.product_id
AND r.rating >= {$threshold}
AND a.ad_id=p.ad_id
AND r.category={$cat}
AND a.category=r.category
AND p.category=a.category
GROUP BY a.ad_id
HAVING cnt >= a.mini
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		while ($row = $vogoo->db->sql_fetchrow($result))
		{
			$ret[] = $row['ad_id'];
		}
		$vogoo->db->sql_freeresult($result);

		return $ret;
	}

	function visitor_targeted_ads($cat = 1)
	{
		global $vogoo;
		global $vogoo_session;
		if (!defined("VOGOO_VISITORS"))
		{
			return false;
		}
		$ret = array();

		$threshold = VG_THRESHOLD_RATING;
		$sql = <<<EOF
SELECT a.ad_id,a.mini,count(p.product_id) as cnt
FROM vogoo_ads a,vogoo_ads_products p
WHERE p.product_id IN (
EOF;
		$nr = 0;
		foreach ($vogoo_session['vogoo_visitor'] as $value)
		{
			if ($value['cat'] == $cat)
			{
				if ($value['rating'] >= VG_THRESHOLD_RATING)
				{
					if ($nr > 0)
					{
						$sql .= ',';
					}
					$sql .= $value['product_id'];
					$nr++;
				}
			}
		}
		if ($nr == 0)
		{
			return $ret;
		}

		$sql .= <<<EOF
)
AND a.ad_id=p.ad_id
AND p.category={$cat}
AND a.category = p.category
GROUP BY a.ad_id
HAVING cnt >= a.mini
EOF;

		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		while ($row = $vogoo->db->sql_fetchrow($result))
		{
			$ret[] = $row['ad_id'];
		}
		$vogoo->db->sql_freeresult($result);

		return $ret;
	}
	// }}}
}

$vogoo_ads = new vogoo_ads_class;

} // ... defined
?>
