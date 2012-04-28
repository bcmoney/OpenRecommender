<?php
/***************************************************************************
 *                                 visitors.php
 *                            -------------------
 *   begin                : Wednesday, Jun 29, 2005
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
	die("Please include vogoo.php before any use of visitors.php !");
}

if (!defined("VOGOO_VISITORS"))
{

define("VOGOO_VISITORS","vogoo");
if (isset($HTTP_SESSION_VARS))
{
	$vogoo_session = &$HTTP_SESSION_VARS;
}
else if (isset($_SESSION))
{
	$vogoo_session = &$_SESSION;
}
else
{
	unset($vogoo_session);
}

class vogoo_visitor_class
{

	function num_ratings($real_ratings = true,$not_interested = false,$cat = 1)
	{
		global $vogoo_session;
		if (!isset($vogoo_session['vogoo_visitor']))
		{
			return false;
		}
		$nr = 0;
		foreach ($vogoo_session['vogoo_visitor'] as $value)
		{
			if ($value['cat'] == $cat)
			{
				if ($value['rating'] >= 0.0 && $real_ratings)
				{
					$nr++;
				}
				if ($value['rating'] == VG_NOT_INTERESTED && $not_interested)
				{
					$nr++;
				}
			}
		}
		return $nr;
	}

	function average_rating($cat = 1)
	{
		global $vogoo_session;
		if (!isset($vogoo_session['vogoo_visitor'])) {
			return false;
		}
		$total = 0.0;
		$nr = 0;
		foreach ($vogoo_session['vogoo_visitor'] as $value)
		{
			if ($value['cat'] == $cat)
			{
				if ($value['rating'] >= 0.0)
				{
					$total += $value['rating'];
					$nr++;
				}
			}
		}
		if ($nr == 0)
		{
			return 0.0;
		}
		else
		{
			return $total/$nr;
		}
	}

	function ratings($orderby_date = false,$orderby_rating = false,$sort_order_ASC = true,$real_ratings = true,$not_interested = false,$cat = 1)
	{
		global $vogoo_session;
		if (!isset($vogoo_session['vogoo_visitor'])) {
			return false;
		}
		$arr = array();
		foreach ($vogoo_session['vogoo_visitor'] as $value)
		{
			if ($value['cat'] == $cat)
			{
				if ($value['rating'] >= 0.0 && $real_ratings)
				{
					$arr[] = array(0=>$value['product_id'],1=>$value['rating'],2=>$value['ts'],product_id=>$value['product_id'],rating=>$value['rating'],ts=>$value['ts']);
				}
				if ($value['rating'] == VG_NOT_INTERESTED && $not_interested)
				{
					$arr[] = array(0=>$value['product_id'],1=>$value['rating'],2=>$value['ts'],product_id=>$value['product_id'],rating=>$value['rating'],ts=>$value['ts']);
				}
			}
		}
		if ($orderby_rating)
		{
			$temparr = array();
			foreach ($arr as $value)
			{
				$temparr[] = $value[1];
			}
			if ($sort_order_ASC)
			{
				array_multisort($temparr,SORT_ASC,$arr);
			}
			else
			{
				array_multisort($temparr,SORT_DESC,$arr);
			}
		}
		else if ($orderby_date)
		{
			$temparr = array();
			foreach ($arr as $value)
			{
				$temparr[] = $value[2];
			}
			if ($sort_order_ASC)
			{
				array_multisort($temparr,SORT_ASC,$arr);
			}
			else
			{
				array_multisort($temparr,SORT_DESC,$arr);
			}
		}

		return $arr;
	}

	// {{{ Combined
	function get_rating($product_id,$not_interested = false,$cat = 1)
	{
		global $vogoo_session;
		if (!isset($vogoo_session['vogoo_visitor']) || !isset($product_id) || !is_numeric($product_id))
                {
                        return false;
                }
		foreach ($vogoo_session['vogoo_visitor'] as $value)
		{
			if ($value['cat'] == $cat && $value['product_id'] == $product_id)
			{
				if ($value['rating'] < 0.0)
				{
					if ($not_interested)
					{
						return array(0=>$value['rating'],1=>$value['ts'],rating=>$value['rating'],ts=>$value['ts']);
					}
					else
					{
						return array();
					}
				}
				else
				{
					return array(0=>$value['rating'],1=>$value['ts'],rating=>$value['rating'],ts=>$value['ts']);
				}
			}
		}
		return array();
	}

	function set_rating($product_id,$rating,$cat = 1)
	{
		global $vogoo_session;
		if (!isset($vogoo_session['vogoo_visitor']) || !isset($product_id) || !is_numeric($product_id) || ($rating < 0.0 && $rating != VG_NOT_INTERESTED) || $rating > 1.0)
                {
                        return false;
                }
		$ts = date("Y-m-d H:i:s");
		foreach ($vogoo_session['vogoo_visitor'] as $key=>$value)
		{
			if ($value['cat'] == $cat && $value['product_id'] == $product_id)
			{
				$vogoo_session['vogoo_visitor'][$key]['rating'] = $rating;
				$vogoo_session['vogoo_visitor'][$key]['ts'] = $ts;
				return true;
			}
		}
		$arr = array("cat" => $cat,"product_id" => $product_id,"rating" => $rating,"ts" => $ts);
		$vogoo_session['vogoo_visitor'][] = $arr;

		return true;
	}

	function set_not_interested($product_id,$cat = 1)
	{
		return $this->set_rating($product_id,VG_NOT_INTERESTED,$cat);
	}

	function delete_rating($product_id,$cat = 1)
	{
		global $vogoo_session;
		if (!isset($vogoo_session['vogoo_visitor']) || !isset($product_id) || !is_numeric($product_id))
                {
                        return false;
                }
		$i = 0;
		foreach ($vogoo_session['vogoo_visitor'] as $key=>$value)
		{
			if ($value['cat'] == $cat && $value['product_id'] == $product_id)
			{
				unset($vogoo_session['vogoo_visitor'][$key]);
				return true;
			}
			$i++;
		}


		return true;
	}

	function automatic_rating($product_id,$purchase,$cat = 1)
	{
		if (!isset($product_id) || !is_numeric($product_id))
                {
                        return false;
                }
		if ($purchase)
		{
			return $this->set_rating($product_id,1.0,$cat);
		}
		else
		{
			// A click
			$res = $this->get_rating($product_id,false,$cat);
			if (count($res) == 0)
			{
				return $this->set_rating($product_id,0.7,$cat);
			}
			else if ($res[0] < 1.0)
			{
				return $this->set_rating($product_id,$res[0]+0.01,$cat);
			}
		}
	}

	function convert($member_id)
	{
		global $vogoo_session;
		global $vogoo;

		if (!isset($vogoo_session['vogoo_visitor']))
                {
                        return false;
                }
		// Search
		$sql = <<<EOF
SELECT COUNT(*)
FROM vogoo_ratings
WHERE member_id = {$member_id}
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$row = $vogoo->db->sql_fetchrow($result);
		$vogoo->db->sql_freeresult($result);
		if ($row[0] != 0)
		{
			foreach ($vogoo_session['vogoo_visitor'] as $value)
			{
				$sql = <<<EOF
SELECT rating
FROM vogoo_ratings
WHERE member_id = {$member_id}
AND product_id = {$value['product_id']}
AND category = {$value['cat']}
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
UPDATE vogoo_ratings
SET rating = {$value['rating']}, ts = '{$value['ts']}'
WHERE member_id = {$member_id}
AND product_id = {$value['product_id']}
AND category = {$value['cat']}
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
INSERT INTO vogoo_ratings(member_id,product_id,category,rating,ts)
VALUES ({$member_id},{$value['product_id']},{$value['cat']},{$value['rating']},'{$value['ts']}')
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
			}
		}
		else
		{
			foreach ($vogoo_session['vogoo_visitor'] as $value)
			{
				$sql = <<<EOF
INSERT INTO vogoo_ratings(member_id,product_id,category,rating,ts)
VALUES ({$member_id},{$value['product_id']},{$value['cat']},{$value['rating']},'{$value['ts']}')
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
		}

		unset($vogoo_session['vogoo_visitor']);
		return true;
	}

	// }}}
}

$vogoo_visitor = new vogoo_visitor_class;
if (!isset($vogoo_session['vogoo_visitor']))
{
	$vogoo_session['vogoo_visitor'] = array();
}

} // ... defined
?>
