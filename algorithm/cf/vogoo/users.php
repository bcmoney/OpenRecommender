<?php
/***************************************************************************
 *                                 users.php
 *                            -------------------
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
	die("Please include vogoo.php before any use of users.php !");
}

if (!defined("VOGOO_USERS"))
{

define("VOGOO_USERS","vogoo");

class vogoo_users_class
{
	function member_similarity($member_id1,$member_id2,$cat = 1)
	{
		global $vogoo;

		if (!isset($member_id1) || !is_numeric($member_id1) || !isset($member_id2) || !is_numeric($member_id2))
                {
                        return false;
                }

		$nr_ratings1 = $vogoo->member_num_ratings($member_id1,true,false,$cat);
		$sql = <<<EOF
SELECT COUNT(r2.product_id) c2,SUM((r2.rating-r1.rating)*(r2.rating-r1.rating)) s
FROM vogoo_ratings r1,vogoo_ratings r2
WHERE r1.member_id = {$member_id1}
AND r2.member_id={$member_id2}
AND r2.product_id=r1.product_id
AND r1.category = {$cat}
AND r2.category=r1.category
AND r1.rating >= 0.0
AND r2.rating >= 0.0
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$row = $vogoo->db->sql_fetchrow($result);
		$vogoo->db->sql_freeresult($result);
		$nr_common_ratings = $row['c2'];
		$spread = $row['s'] * VG_COST * VG_COST * 20.0;
		if ($nr_common_ratings == 0)
		{
			return 0;
		}
		$temp_factor = (float)$spread / (float)$nr_common_ratings;
		if ($temp_factor > 100)
		{
			return 0;
		}
		if ($nr_common_ratings > VG_THRESHOLD_NR_COMMON_RATINGS || ($nr_common_ratings * VG_THRESHOLD_MULT) >= $nr_ratings1)
		{
			return 100 - (int)$temp_factor;
		}
		$temp_factor2 = 0;
		if ($nr_ratings1 < (VG_THRESHOLD_NR_COMMON_RATINGS * VG_THRESHOLD_MULT))
		{
			$temp_factor2 = (float)($nr_common_ratings * VG_THRESHOLD_MULT) / (float)$nr_ratings1;
		}
		else
		{
			$temp_factor2 = (float)($nr_common_ratings * VG_THRESHOLD_MULT) / (float)(VG_THRESHOLD_NR_COMMON_RATINGS * VG_THRESHOLD_MULT);
		}
		$temp_factor2 *= $temp_factor2;
		return (int)((100.0 - $temp_factor) * (0.1 + 0.9 * $temp_factor2));
	}

	function member_k_similarities($member_id,$k,&$similarities,$cat = 1)
	{
		global $vogoo;

		if (!isset($member_id) || !is_numeric($member_id) || !isset($k) || !is_numeric($k))
		{
			return false;
		}

		$similarities = array();

		$nr_ratings = $vogoo->member_num_ratings($member_id,true,false,$cat);
		$sql = <<<EOF
SELECT r2.member_id,COUNT(r2.product_id) c2,SUM((r2.rating-r1.rating)*(r2.rating-r1.rating)) s
FROM vogoo_ratings r1,vogoo_ratings r2
WHERE r1.member_id = {$member_id}
AND r2.product_id=r1.product_id
AND r1.category = {$cat}
AND r2.category=r1.category
AND r1.rating >= 0.0
AND r2.rating >= 0.0
AND r2.member_id <> r1.member_id
GROUP BY r2.member_id
EOF;

		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$spread = array();
		while ($row = $vogoo->db->sql_fetchrow($result))
		{
			$spread[$row['member_id']] = array($row['s'] * VG_COST * VG_COST * 20.0,$row['c2']);
		}
		$vogoo->db->sql_freeresult($result);

		$arr = array();
		for ($i = 0;$i <= 100;$i++)
		{
			$arr[] = array();
		}
		foreach ($spread as $key=>$value)
		{
			$temp_factor = (float)($value[0]) / (float)($value[1]);
			if ($temp_factor > 100)
			{
				$arr[0][] = $key;
			}
			else
			{
				if ($value[1] >= VG_THRESHOLD_NR_COMMON_RATINGS || ($value[1] * VG_THRESHOLD_MULT) >= $nr_ratings)
				{
					$arr[100 - (int)$temp_factor][] = $key;
				}
				else
				{
					$temp_factor2 = 0;
					if ($nr_ratings < (VG_THRESHOLD_NR_COMMON_RATINGS * VG_THRESHOLD_MULT))
					{
						$temp_factor2 = (float)($value[1] * VG_THRESHOLD_MULT) / (float)$nr_ratings;
					}
					else
					{
						$temp_factor2 = (float)($value[1] * VG_THRESHOLD_MULT) / (float)(VG_THRESHOLD_NR_COMMON_RATINGS * VG_THRESHOLD_MULT);
					}
					$temp_factor2 *= $temp_factor2;
					$arr[(int)((100.0 - $temp_factor) * (0.1 + 0.9 * $temp_factor2))][] = $key;
				}
			}
		}
		unset($spread);
		// Now create the array for the k nearest neighbours
		$i = 100;
		$j = 0;
		while ($j < $k && $i >= 0)
		{
			$n = count($arr[$i]);
			$p = 0;
			while ($p < $n && $j < $k)
			{
				$similarities[] = array(0=>$arr[$i][$p],1=>$i,'member_id'=>$arr[$i][$p],'sim'=>$i);
				$j++;
				$p++;
			}
			$i--;
		}
		return true;
	}

	function visitor_similarity($member_id2,$cat = 1)
	{
		global $vogoo;
		global $vogoo_session;
		if (!defined("VOGOO_VISITORS"))
		{
			return false;
		}

		if (!isset($vogoo_session['vogoo_visitor'])) {
			return false;
		}

		$arr = $vogoo->member_ratings($member_id2,false,false,true,true,false,$cat);
		if (!is_array($arr) && $arr == false)
		{
			return false;
		}

		$products_ratings_member_id1 = array();
		$nr_ratings1 = 0;
		foreach ($vogoo_session['vogoo_visitor'] as $value)
		{
			if ($value['cat'] == $cat && $value['rating'] >= 0.0)
			{
				$products_ratings_member_id1[$value['product_id']] = $value['rating'];
				$nr_ratings1++;
			}
		}
		$nr_common_ratings = 0;
		$nr_ratings2 = count($arr);
		$pdt = 0;
		$spread = 0;
		if ($nr_ratings1 == 0 || $nr_ratings2 == 0)
		{
			return 0;
		}
		else
		{
			for ($i = 0;$i < $nr_ratings2;$i++)
			{
				$pdt = $arr[$i][0];
				if (isset($products_ratings_member_id1[$pdt]))
				{
					$rating = $products_ratings_member_id1[$pdt];
					$tempspread = $arr[$i][1] - $rating;
					$tempspread *= $tempspread;
					$spread += $tempspread;
					$nr_common_ratings++;
				}
			}
			if ($nr_common_ratings == 0)
			{
				return 0;
			}
			$temp_factor = (float)$spread * VG_COST * VG_COST * 20.0 / (float)$nr_common_ratings;
			if ($temp_factor > 100)
			{
				return 0;
			}
			if ($nr_common_ratings > VG_THRESHOLD_NR_COMMON_RATINGS || $nr_common_ratings * VG_THRESHOLD_MULT >= $nr_ratings1)
			{
				return 100 - (int)$temp_factor;
			}
			$temp_factor2 = 0;
			if ($nr_ratings1 < VG_THRESHOLD_NR_COMMON_RATINGS * VG_THRESHOLD_MULT)
			{
				$temp_factor2 = (float)($nr_common_ratings * VG_THRESHOLD_MULT) / (float)$nr_ratings1;
			}
			else
			{
				$temp_factor2 = (float)($nr_common_ratings * VG_THRESHOLD_MULT) / (float)(VG_THRESHOLD_NR_COMMON_RATINGS * VG_THRESHOLD_MULT);
			}
			$temp_factor2 *= $temp_factor2;
			return (int)((100.0 - $temp_factor) * (0.1 + 0.9 * $temp_factor2));
		}
	}

	function visitor_k_similarities($k,&$similarities,$cat = 1)
	{
		global $vogoo;
		global $vogoo_session;
		if (!defined("VOGOO_VISITORS"))
		{
			return false;
		}

		if (!isset($vogoo_session['vogoo_visitor']) || !isset($k) || !is_numeric($k)) {
			return false;
		}


		$similarities = array();

		$products_ratings = array();
		$products = array();
		$nr_ratings = 0;
		$pdt = 0;
		foreach ($vogoo_session['vogoo_visitor'] as $value)
		{
			if ($value['cat'] == $cat && $value['rating'] >= 0.0)
			{
				$pdt = $value['product_id'];
				$products_ratings[$pdt] = $value['rating'];
				$products[] = $pdt;
				$nr_ratings++;
			}
		}
		if ($nr_ratings == 0)
		{
			return true;
		}

		// Read all the ratings for these products
		$sql = <<<EOF
SELECT member_id,product_id,rating
FROM vogoo_ratings
WHERE rating >= 0.0
AND category = {$cat}
AND product_id IN (
EOF;

		for ($i = 0;$i < $nr_ratings;$i++)
		{
			if ($i != 0)
			{
				$sql .= ',';
			}
			$sql .= $products[$i];
		}
		$sql .= ')';
		// free products array
		unset($products);

		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		unset($sql);
		$spread = array();
		$member = 0;
		$temp_spread = 0;
		$rating1 = 0;
		$rating2 = 0;
		$temp = 0;
		while ($row = $vogoo->db->sql_fetchrow($result))
		{
			$member = $row['member_id'];
			$pdt = $row['product_id'];
			$rating1 = $products_ratings[$pdt];
			$rating2 = $row['rating'];
			if (isset($spread[$member]))
			{
				$temp_spread = $spread[$member];
				$temp = $rating2 - $rating1;
				$temp *= $temp;
				$temp_spread[0] += $temp;
				$temp_spread[1]++;
				$spread[$member] = $temp_spread;
			}
			else
			{
				$temp_spread = $rating2 - $rating1;
				$temp_spread *= $temp_spread;
				$spread[$member] = array($temp_spread,1);
			}
		}
		$vogoo->db->sql_freeresult($result);
		unset($products_ratings);
		unset($row);

		$arr = array();
		for ($i = 0;$i <= 100;$i++)
		{
			$arr[] = array();
		}
		foreach ($spread as $key=>$value)
		{
			$temp_factor = (float)($value[0]) * VG_COST * VG_COST * 20.0 / (float)($value[1]);
			if ($temp_factor > 100)
			{
				$arr[0][] = $key;
			}
			else
			{
				if ($value[1] >= VG_THRESHOLD_NR_COMMON_RATINGS || $value[1] * VG_THRESHOLD_MULT >= $nr_ratings)
				{
					$arr[100 - (int)$temp_factor][] = $key;
				}
				else
				{
					$temp_factor2 = 0;
					if ($nr_ratings < VG_THRESHOLD_NR_COMMON_RATINGS * VG_THRESHOLD_MULT)
					{
						$temp_factor2 = (float)($value[1] * VG_THRESHOLD_MULT) / (float)$nr_ratings;
					}
					else
					{
						$temp_factor2 = (float)($value[1] * VG_THRESHOLD_MULT) / (float)(VG_THRESHOLD_NR_COMMON_RATINGS * VG_THRESHOLD_MULT);
					}
					$temp_factor2 *= $temp_factor2;
					$arr[(int)((100.0 - $temp_factor) * (0.1 + 0.9 * $temp_factor2))][] = $key;
				}
			}
		}
		unset($spread);
		// Now create the array for the k nearest neighbours
		$i = 100;
		$j = 0;
		while ($j < $k && $i >= 0)
		{
			$n = count($arr[$i]);
			$p = 0;
			while ($p < $n && $j < $k)
			{
				$similarities[] = array(0=>$arr[$i][$p],1=>$i,member_id=>$arr[$i][$p],sim=>$i);
				$j++;
				$p++;
			}
			$i--;
		}
		return true;
	}
	
	function member_k_recommendations($member_id,$k,&$similarities,&$recommendations,$cat = 1,$filter = false)
	{
		global $vogoo;

		if (!isset($member_id) || !is_numeric($member_id) || !isset($k) || !is_numeric($k) || !isset($similarities))
		{
			return false;
		}

		$recommendations = array();

		$sql = <<<EOF
SELECT product_id
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

		$nr = count($similarities);
		$i = 0;
		$j = 0;
		while ($i < $k && $j < $nr)
		{
			$member = $similarities[$j]['member_id'];
			$sql = <<<EOF
SELECT product_id,rating,ts
FROM vogoo_ratings
WHERE member_id = {$member}
AND rating >= 0.0
AND category = {$cat}
EOF;
			if ( !($result = $vogoo->db->sql_query($sql)) )
			{
				return false;
			}
			while ($i < $k && $row = $vogoo->db->sql_fetchrow($result))
			{
				$pdt = $row['product_id'];
				if (!in_array($pdt,$products) && (!$filter || $filter[$pdt]))
				{
					$rating = $row['rating'];
					$products[] = $pdt;
					if ($rating >= VG_THRESHOLD_RATING)
					{
						$recommendations[] = array(0=>$member,1=>$similarities[$j][1],2=>$pdt,3=>$rating,4=>$row['ts'],member_id=>$member,sim=>$similarities[$j][1],product_id=>$pdt,rating=>$rating,ts=>$row['ts']);
						$i++;
					}
				}
			}
			$j++;
			$vogoo->db->sql_freeresult($result);
		}

		return true;
	}

	function visitor_k_recommendations($k,&$similarities,&$recommendations,$cat = 1,$filter = false)
	{
		global $vogoo_session;
		global $vogoo;
		if (!defined("VOGOO_VISITORS"))
		{
			return false;
		}	
		if (!isset($vogoo_session['vogoo_visitor']) || !isset($k) || !is_numeric($k) || !isset($similarities))
                {
                        return false;
                }

		$recommendations = array();

		$products = array();
		foreach ($vogoo_session['vogoo_visitor'] as $value)
		{
			if ($value['cat'] == $cat)
			{
				$products[] = $value['product_id'];
			}
		}

		$nr = count($similarities);
		$i = 0;
		$j = 0;
		while ($i < $k && $j < $nr)
		{
			$member = $similarities[$j][0];
			$arr = $vogoo->member_ratings($member,false,false,true,true,false,$cat);
			if (!$arr)
        	        {
                	        return false;
	                }
			$nr2 = count($arr);
			$z = 0;
			while ($i < $k && $z < $nr2)
			{
				$pdt = $arr[$z][0];
				if (!in_array($pdt,$products) && (!$filter || $filter[$pdt]))
				{
					$rating = $arr[$z][1];
					$products[] = $pdt;
					if ($rating >= VG_THRESHOLD_RATING)
					{
						$recommendations[] = array(0=>$member,1=>$similarities[$j][1],2=>$pdt,3=>$rating,4=>$arr[$z][2],member_id=>$member,sim=>$similarities[$j][1],product_id=>$pdt,rating=>$rating,ts=>$arr[$z][2]);
						$i++;
					}
				}
				$z++;
			}
			$j++;
		}

		return true;
	}

	function get_product_recommendation($product_id,&$similarities,$cat = 1)
	{
		global $vogoo;

		if (!isset($product_id) || !is_numeric($product_id) || !isset($similarities))
		{
			return false;
		}

		$ret = array();
		$nr = count($similarities);
		if ($nr == 0)
		{
			return $ret;
		}
		$arr = array();
		for ($i = 0;$i < $nr;$i++)
		{
			$arr[$similarities[$i][0]] = $similarities[$i][1];
		}

		$sql = <<<EOF
SELECT member_id,rating,ts
FROM vogoo_ratings
WHERE product_id = {$product_id}
AND rating >= 0.0
AND category = {$cat}
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}

		// Don't return any recommandation under 50% of similarity
		$max_similarity = 50;
		while ($row = $vogoo->db->sql_fetchrow($result))
		{
			$member = $row['member_id'];
			if (isset($arr[$member]))
			{
				$temp_sim = $arr[$member];
				if ($temp_sim > $max_similarity)
				{
					$ret = array(0=>$member,1=>$temp_sim,2=>$row['rating'],3=>$row['ts'],member_id=>$member,sim=>$temp_sim,rating=>$row['rating'],ts=>$row['ts']);
					$max_similarity = $temp_sim;
				}
			}
		}
		$vogoo->db->sql_freeresult($result);
		return $ret;
	}

	function get_product_ratings_by_similarity($product_id,&$similarities,$cat = 1)
	{
		global $vogoo;

		if (!isset($product_id) || !is_numeric($product_id) || !isset($similarities))
		{
			return false;
		}

		$ret = array();
		$nr = count($similarities);
		if ($nr == 0)
		{
			return $ret;
		}
		$arr = array();
		for ($i = 0;$i < $nr;$i++)
		{
			$arr[$similarities[$i][0]] = array($similarities[$i][1],$i);
		}

		$sql = <<<EOF
SELECT member_id,rating,ts
FROM vogoo_ratings
WHERE product_id = {$product_id}
AND rating >= 0.0
AND category = {$cat}
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}

		$temp_ret = array();
		while ($row = $vogoo->db->sql_fetchrow($result))
		{
			$member = $row['member_id'];
			if (isset($arr[$member]))
			{
				$temp = $arr[$member];
				$temp_ret[$temp[1]] = array(0=>$member,1=>$temp[0],2=>$row['rating'],3=>$row['ts'],member_id=>$member,sim=>$temp[0],rating=>$row['rating'],ts=>$row['ts']);
			}
		}
		$vogoo->db->sql_freeresult($result);
		unset($arr);

		// Create a ret array from temp_ret
		for ($i = 0;$i < $nr;$i++)
		{
			if (isset($temp_ret[$i]))
			{
				$ret[] = $temp_ret[$i];
			}
		}
		return $ret;
	}
}

$vogoo_users = new vogoo_users_class;

} // ... defined
?>
