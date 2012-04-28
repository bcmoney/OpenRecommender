<?php
/***************************************************************************
 *                                 items.php
 *                            -------------------
 *   begin                : Sunday, Aug 28, 2005
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
	die("Please include vogoo.php before any use of items.php !");
}

if (!defined("VOGOO_ITEMS"))
{

define("VOGOO_ITEMS","vogoo");

class vogoo_items_class
{
	// {{{ Links
	function get_linked_items($product_id,$cat = 1,$filter = false, $k = 1000000)
	{
		global $vogoo;
		if (!isset($product_id) || !is_numeric($product_id))
		{
			return false;
		}
		$ret = array();

		$sql = <<<EOF
SELECT item_id2,cnt
FROM vogoo_links
WHERE item_id1={$product_id}
AND category = {$cat}
ORDER BY cnt DESC
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$i = 0;
		while ($i < $k && $row = $vogoo->db->sql_fetchrow($result))
		{
			if (!$filter || $filter[$row['item_id2']]) {
				$ret[] = $row['item_id2'];
				$i++;
			}
		}
		$vogoo->db->sql_freeresult($result);

		return $ret;
	}

	function member_get_recommended_items($member_id,$cat = 1,$filter = false, $k = 1000000)
	{
		global $vogoo;
		if (!isset($member_id) || !is_numeric($member_id))
		{
			return false;
		}
		$ret = array();

		$sql = <<<EOF
SELECT product_id,rating
FROM vogoo_ratings
WHERE member_id = {$member_id}
AND category = {$cat}
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$products = array();
		while ($row = $vogoo->db->sql_fetchrow($result))
		{
			$products[] = $row['product_id'];
		}
		$vogoo->db->sql_freeresult($result);

		$threshold = VG_THRESHOLD_RATING;
		$sql = <<<EOF
SELECT l.item_id2,sum(l.cnt * (r.rating - {$threshold})) as cnter
FROM vogoo_links l,vogoo_ratings r
WHERE r.member_id={$member_id}
AND l.item_id1=r.product_id
AND r.rating >= 0.0
AND l.category=r.category
AND r.category = {$cat}
GROUP BY l.item_id2
HAVING cnter > 0
ORDER BY cnter DESC
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$i = 0;
		while ($i < $k && $row = $vogoo->db->sql_fetchrow($result))
		{
			if (!in_array($row['item_id2'],$products) && (!$filter || $filter[$row['item_id2']]))
			{
				$ret[] = $row['item_id2'];
				$i++;
			}
		}
		$vogoo->db->sql_freeresult($result);

		return $ret;
	}

	function member_get_reasons($member_id,$product_id,$cat = 1, $k = 1000000)
	{
		global $vogoo;
		if (!isset($member_id) || !is_numeric($member_id))
		{
			return false;
		}
		$ret = array();

		$threshold = VG_THRESHOLD_RATING;
		$sql = <<<EOF
SELECT r.product_id
FROM vogoo_ratings r,vogoo_links l
WHERE r.member_id = {$member_id}
AND r.category = {$cat}
AND l.category=r.category
AND r.rating >= {$threshold}
AND l.item_id1 = {$product_id}
AND r.product_id = l.item_id2
AND l.cnt > 0
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$i = 0;
		while ($i < $k && $row = $vogoo->db->sql_fetchrow($result))
		{
			$ret[] = $row['product_id'];
			$i++;
		}
		$vogoo->db->sql_freeresult($result);
		return $ret;
	}

	function visitor_get_recommended_items($cat = 1,$filter = false, $k = 1000000)
	{
		global $vogoo;
		global $vogoo_session;
		if (!defined("VOGOO_VISITORS"))
		{
			return false;
		}
		$ret = array();

		$products = array();
		$items = array();
		foreach ($vogoo_session['vogoo_visitor'] as $value)
		{
			if ($value['cat'] == $cat && ($value['rating'] >= 0.0 || $value['rating'] == VG_NOT_INTERESTED))
			{
				$items[] = array($value['product_id'],$value['rating']);
				$products[$value['product_id']] = 1;
			}
		}

		$temp = array();
		foreach ($items as $it)
		{
			if ($it[1] != VG_NOT_INTERESTED)
			{
				$sql = <<<EOF
SELECT item_id2,cnt
FROM vogoo_links
WHERE category={$cat}
AND item_id1 = {$it[0]}
EOF;
				if ( !($result = $vogoo->db->sql_query($sql)) )
				{
					return false;
				}
				while ($row = $vogoo->db->sql_fetchrow($result))
				{
					if ((!$filter || $filter[$row['item_id2']]) && ($row['cnt'] != 0) && (!isset($products[$row['item_id2']])))
					{
						$temp[$row['item_id2']] += ($it[1] - VG_THRESHOLD_RATING) * $row['cnt'];
					}
				}
				$vogoo->db->sql_freeresult($result);
			}
		}
		unset($items);
		unset($products);

		$order = array();
		foreach ($temp as $key=>$value)
		{
			if ($value > 0)
			{
				$order[] = $value;
				$ret[] = array($key,$value);
			}
		}
		unset($temp);
		array_multisort($order,SORT_DESC,$ret);
		$arr = array();
		$i = 0;
		foreach ($ret as $r)
		{
			if ($i == $k) break;
			$arr[] = $r[0];
			$i++;
		}
		return $arr;
	}

	function visitor_get_reasons($product_id,$cat = 1, $k = 1000000)
	{
		global $vogoo;
		global $vogoo_session;

		if (!defined("VOGOO_VISITORS"))
		{
			return false;
		}
		$ret = array();

		$products = array();
		foreach ($vogoo_session['vogoo_visitor'] as $value)
		{
			if ($value['cat'] == $cat)
			{
				if ($value['rating'] >= VG_THRESHOLD_RATING)
				{
					$products[] = $value['product_id'];
				}
			}
		}

		$sql = <<<EOF
SELECT item_id2
FROM vogoo_links
WHERE category={$cat}
AND item_id1={$product_id}
AND item_id2 IN (
EOF;
		$nr = 0;
		foreach ($products as $p)
		{
			if ($nr > 0)
			{
				$sql .= ',';
			}
			$sql .= $p;
			$nr++;
		}
		if ($nr == 0)
		{
			return $ret;
		}

		$sql .= <<<EOF
)
AND cnt > 0
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$i = 0;
		while ($i < $k && $row = $vogoo->db->sql_fetchrow($result))
		{
			$ret[] = $row['item_id2'];
			$i++;
		}
		$vogoo->db->sql_freeresult($result);
		return $ret;
	}
	// }}}

	// {{{ Slope
	function get_slope_items($product_id,$min_nr_links = 1,$cat = 1,$filter = false, $k = 1000000)
	{
		global $vogoo;
		if (!isset($product_id) || !is_numeric($product_id) || $min_nr_links < 1)
		{
			return false;
		}
		$ret = array();

		$sql = <<<EOF
SELECT item_id2,(diff_slope / cnt) as avg_diff
FROM vogoo_links
WHERE item_id1={$product_id}
AND category = {$cat}
AND cnt != 0
AND cnt >= {$min_nr_links}
ORDER BY avg_diff DESC
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$i = 0;
		while ($i < $k && $row = $vogoo->db->sql_fetchrow($result))
		{
			if (!$filter || $filter[$row['item_id2']]) {
				$ret[] = array(0=>$row['item_id2'],1=>$row['avg_diff'],product_id=>$row['item_id2'],diff=>$row['avg_diff']);
				$i++;
			}
		}
		$vogoo->db->sql_freeresult($result);

		return $ret;
	}

	function member_predict($member_id,$product_id,$cat = 1)
	{
		global $vogoo;
		if (!isset($member_id) || !is_numeric($member_id))
		{
			return false;
		}

		$sql = <<<EOF
SELECT sum(l.cnt) cnter,sum(r.rating * l.cnt - l.diff_slope) diff
FROM vogoo_links l,vogoo_ratings r
WHERE l.item_id1={$product_id}
AND r.member_id={$member_id}
AND r.product_id=l.item_id2
AND l.category={$cat}
AND r.category = l.category
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$row = $vogoo->db->sql_fetchrow($result);
		$vogoo->db->sql_freeresult($result);

		if ($row['cnter'] == 0)
		{
			return false;
		}
		else
		{
			$temp = $row['diff']/$row['cnter'];
			if ($temp > 1.0)
			{
				$temp = 1.0;
			}
			else if ($temp < 0.0)
			{
				$temp = 0.0;
			}
			return $temp;
		}
	}

	function member_predict_all($member_id,$cat = 1,$filter = false, $k = 1000000)
	{
		global $vogoo;
		if (!isset($member_id) || !is_numeric($member_id))
		{
			return false;
		}
		$ret = array();

		$sql = <<<EOF
SELECT product_id,rating
FROM vogoo_ratings
WHERE member_id = {$member_id}
AND category = {$cat}
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
                {
                        return false;
                }
		$products = array();
		while ($row = $vogoo->db->sql_fetchrow($result))
		{
			$products[] = $row['product_id'];
		}
		$vogoo->db->sql_freeresult($result);

		$sql = <<<EOF
SELECT l.item_id2,sum(l.cnt) cnter,sum(r.rating * l.cnt + l.diff_slope) diff
FROM vogoo_links l,vogoo_ratings r
WHERE r.member_id={$member_id}
AND r.rating >= 0.0
AND l.item_id1=r.product_id
AND l.cnt != 0
AND r.category={$cat}
AND l.category = r.category
GROUP BY l.item_id2
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$temp2 = array();
		while ($row = $vogoo->db->sql_fetchrow($result))
		{
			if (!in_array($row['item_id2'],$products) && (!$filter || $filter[$row['item_id2']]))
			{
				$temp = $row['diff']/$row['cnter'];
				if ($temp > 1.0)
				{
					$temp = 1.0;
				}
				else if ($temp < 0.0)
				{
					$temp = 0.0;
				}
				$temp2[] = $temp;
				$ret[] = array(0=>$row['item_id2'],1=>$temp,product_id=>$row['item_id2'],rating=>$temp);
			}
		}
		$vogoo->db->sql_freeresult($result);

		array_multisort($temp2,SORT_DESC,$ret);
		return array_slice($ret,0,$k);
	}

	function visitor_predict($product_id,$cat = 1)
	{
		global $vogoo;
		global $vogoo_session;
		if (!defined("VOGOO_VISITORS"))
		{
			return false;
		}
		$ret = array();

		$products = array();
		foreach ($vogoo_session['vogoo_visitor'] as $value)
		{
			if ($value['cat'] == $cat && $value['rating'] >= 0.0)
			{
				$products[$value['product_id']] = $value['rating'];
			}
		}

		$sql = <<<EOF
SELECT item_id2,cnt,diff_slope
FROM vogoo_links
WHERE item_id1={$product_id}
AND category={$cat}
AND cnt > 0
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$numer = 0.0;
		$denom = 0;
		while ($row = $vogoo->db->sql_fetchrow($result))
		{
			if (isset($products[$row['item_id2']]))
			{
				$numer += $products[$row['item_id2']] * $row['cnt'] - $row['diff_slope'];
				$denom += $row['cnt'];
			}
		}
		$vogoo->db->sql_freeresult($result);

		if ($denom == 0)
		{
			return false;
		}
		else
		{
			$temp = $numer/$denom;
			if ($temp > 1.0)
			{
				$temp = 1.0;
			}
			else if ($temp < 0.0)
			{
				$temp = 0.0;
			}
			return $temp;
		}
	}

	function visitor_predict_all($cat = 1,$filter = false, $k = 1000000)
	{
		global $vogoo;
		global $vogoo_session;
		if (!defined("VOGOO_VISITORS"))
		{
			return false;
		}
		$ret = array();

		$products = array();
		$items = array();
		foreach ($vogoo_session['vogoo_visitor'] as $value)
		{
			if ($value['cat'] == $cat && $value['rating'] >= 0.0)
			{
				$items[] = $value['product_id'];
				$products[] = array($value['product_id'],$value['rating']);
			}
		}

		$arr = array();
		foreach ($products as $p)
		{
			$sql = <<<EOF
SELECT item_id2,sum(cnt) cnter,sum({$p[1]} * cnt + diff_slope) diff
FROM vogoo_links
WHERE item_id1={$p[0]}
AND cnt > 0
AND category = {$cat}
GROUP BY item_id2
EOF;
			if ( !($result = $vogoo->db->sql_query($sql)) )
			{
				return false;
			}
			while ($row = $vogoo->db->sql_fetchrow($result))
			{
				if (!$filter || $filter[$row['item_id2']]) {
					if (isset($arr[$row['item_id2']]))
					{
						$arr[$row['item_id2']][0] += $row['cnter'];
						$arr[$row['item_id2']][1] += $row['diff'];
					}
					else
					{
						$arr[$row['item_id2']] = array($row['cnter'],$row['diff']);
					}
				}
			}
			$vogoo->db->sql_freeresult($result);
		}

		unset($products);
		$products = array();
		foreach ($arr as $key=>$value)
		{
			if (!in_array($key,$items))
			{
				$temp = $value[1]/$value[0];
				if ($temp > 1.0)
				{
					$temp = 1.0;
				}
				else if ($temp < 0.0)
				{
					$temp = 0.0;
				}
				$ret[] = array(0=>$key,1=>$temp,product_id=>$key,rating=>$temp);
				$products[] = $temp;
				$i++;
			}
		}
		array_multisort($products,SORT_DESC,$ret);
		return array_slice($ret,0,$k);
	}

	// }}}
}

$vogoo_items = new vogoo_items_class;

} // ... defined
?>
