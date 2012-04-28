<?php
/***************************************************************************
 *                                 vogoo.php
 *                            -------------------
 *   begin                : Monday, Apr 4, 2005
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

if (!defined("VOGOO"))
{

define("VOGOO","vogoo");
include (VOGOO_DIR."config.php");
include (VOGOO_DIR."db.php");

// Engine constants
define("VG_THRESHOLD_NR_COMMON_RATINGS",30);
define("VG_THRESHOLD_MULT",2);
define("VG_THRESHOLD_RATING",0.66);
define("VG_COST",5.0);
define("VG_NOT_INTERESTED",-1.0);
define("VG_DIRECT_LINKS",false);
define("VG_DIRECT_SLOPE",false);
if (VG_DIRECT_LINKS || VG_DIRECT_SLOPE)
{
	include(VOGOO_DIR."directitems.php");
}

if (!isset($vg_dbms))
{
	die("VOGOO LIB not installed !");
}

class vogoo_class
{
	var $db;
	var $connected;

	function vogoo_class($vg_dbhost,$vg_dbuser,$vg_dbpasswd,$vg_dbname)
	{
		$this->db = new vg_sql_db($vg_dbhost,$vg_dbuser,$vg_dbpasswd,$vg_dbname,false);
		$this->connected = ($this->db->db_connect_id != false);
	}

	// {{{ Members
	function member_num_ratings($member_id,$real_ratings = true,$not_interested = false,$cat = 1)
	{
		if (!isset($member_id) || !is_numeric($member_id))
		{
			return false;
		}

		$sql = <<<EOF
SELECT count(*) AS number_of_ratings
FROM vogoo_ratings
WHERE member_id = {$member_id}
AND category = {$cat}
EOF;

		// Filter real ratings/not interested information
		if ($real_ratings)
		{
			if (!$not_interested)
			{
				$sql .= ' AND rating >= 0.0';
			}
		}
		else
		{
			// if not_interested is set to false, then the user is a weirdo ;)
			// don't handle this case
			$sql .= ' AND rating = '.VG_NOT_INTERESTED;
		}

		if ( !($result = $this->db->sql_query($sql)) )
		{
			return false;
		}

		$row = $this->db->sql_fetchrow($result);
		$nr = $row['number_of_ratings'];
		$this->db->sql_freeresult($result);
		return $nr;
	}

	function member_average_rating($member_id,$cat = 1)
	{
		if (!isset($member_id) || !is_numeric($member_id))
		{
			return false;
		}

		$sql = <<<EOF
SELECT avg(rating) AS average
FROM vogoo_ratings
WHERE member_id = {$member_id}
AND category = {$cat}
AND rating >= 0.0
EOF;

		if ( !($result = $this->db->sql_query($sql)) )
		{
			return false;
		}

		$row = $this->db->sql_fetchrow($result);
		$avg = $row['average'];
		$this->db->sql_freeresult($result);
		if ($avg == null)
		{
			return 0.0;
		}
		return $avg;
	}

	function member_ratings($member_id,$orderby_date = false,$orderby_rating = false,$sort_order_ASC = true,$real_ratings = true,$not_interested = false,$cat = 1)
	{
		if (!isset($member_id) || !is_numeric($member_id))
		{
			return false;
		}

		$sql = <<<EOF
SELECT product_id,rating,ts
FROM vogoo_ratings
WHERE member_id = {$member_id}
AND category = {$cat}
EOF;

		// Filter real ratings/not interested information
		if ($real_ratings)
		{
			if (!$not_interested)
			{
				$sql .= ' AND rating >= 0.0';
			}
		}
		else
		{
			$sql .= ' AND rating = '.VG_NOT_INTERESTED;
		}

		if ($orderby_date || $orderby_rating)
		{
			$sql .= " ORDER BY ";
			$sql .= $orderby_date ? 'ts ' : 'rating ';
			$sql .= $sort_order_ASC ? 'ASC' : 'DESC';
		}

		if ( !($result = $this->db->sql_query($sql)) )
		{
			return false;
		}

		$arr = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$arr[] = array(0=>$row['product_id'],'product_id'=>$row['product_id'],1=>$row['rating'],'rating'=>$row['rating'],2=>$row['ts'],ts=>$row['ts']);
		}
		$this->db->sql_freeresult($result);
		return $arr;
	}

	function delete_member($member_id)
	{
		if (!isset($member_id) || !is_numeric($member_id))
		{
			return false;
		}

		$sql = <<<EOF
DELETE
FROM vogoo_ratings
WHERE member_id = {$member_id}
EOF;
		if ( !($this->db->sql_query($sql)) )
		{
			return false;
		}

		return true;
	}

	// }}}

	// {{{ Products
	function product_num_ratings($product_id,$cat = 1)
	{
		if (!isset($product_id) || !is_numeric($product_id))
		{
			return false;
		}

		$sql = <<<EOF
SELECT count(*) AS number_of_ratings
FROM vogoo_ratings
WHERE product_id = {$product_id}
AND rating >= 0.0
AND category = {$cat}
EOF;

		if ( !($result = $this->db->sql_query($sql)) )
		{
			return false;
		}

		$row = $this->db->sql_fetchrow($result);
		$nr = $row['number_of_ratings'];
		$this->db->sql_freeresult($result);
		return $nr;
	}

	function product_average_rating($product_id,$cat = 1)
	{
		if (!isset($product_id) || !is_numeric($product_id))
		{
			return false;
		}

		$sql = <<<EOF
SELECT avg(rating) AS average
FROM vogoo_ratings
WHERE product_id = {$product_id}
AND category = {$cat}
AND rating >= 0.0
EOF;

		if ( !($result = $this->db->sql_query($sql)) )
		{
			return false;
		}

		$row = $this->db->sql_fetchrow($result);
		$avg = $row['average'];
		$this->db->sql_freeresult($result);
		if ($avg == null)
		{
			return 0.0;
		}
		return $avg;
	}

	function product_ratings($product_id,$orderby_date = false,$orderby_rating = false,$sort_order_ASC = true,$cat = 1)
	{
		if (!isset($product_id) || !is_numeric($product_id))
		{
			return false;
		}

		$sql = <<<EOF
SELECT member_id,rating,ts
FROM vogoo_ratings
WHERE product_id = {$product_id}
AND rating >= 0.0
AND category = {$cat}
EOF;

		if ($orderby_date || $orderby_rating)
		{
			$sql .= " ORDER BY ";
			$sql .= $orderby_date ? 'ts ' : 'rating ';
			$sql .= $sort_order_ASC ? 'ASC' : 'DESC';
		}

		if ( !($result = $this->db->sql_query($sql)) )
		{
			return false;
		}

		$arr = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$arr[] = array(0=>$row['member_id'],1=>$row['rating'],2=>$row['ts'],member_id=>$row['member_id'],rating=>$row['rating'],ts=>$row['ts']);
		}
		$this->db->sql_freeresult($result);
		return $arr;
	}

	function delete_product($product_id,$cat = 1)
	{
		if (!isset($product_id) || !is_numeric($product_id))
		{
			return false;
		}

		$sql = <<<EOF
DELETE
FROM vogoo_ratings
WHERE product_id = {$product_id}
AND category = {$cat}
EOF;
		if ( !($this->db->sql_query($sql)) )
		{
			return false;
		}

		return true;
	}
	// }}}

	// {{{ Combined
	function get_rating($member_id,$product_id,$not_interested = false,$cat = 1)
	{
		if (!isset($member_id) || !is_numeric($member_id) || !isset($product_id) || !is_numeric($product_id))
		{
			return false;
		}

		$sql = <<<EOF
SELECT rating,ts
FROM vogoo_ratings
WHERE member_id = {$member_id}
AND product_id = {$product_id}
AND category = {$cat}
EOF;

		if (!$not_interested)
		{
			$sql .= ' AND rating >= 0.0';
		}

		if ( !($result = $this->db->sql_query($sql)) )
		{
			return false;
		}

		if ($this->db->sql_numrows($result) != 1)
		{
			$this->db->sql_freeresult($result);
			return array();
		}
		$row = $this->db->sql_fetchrow($result);
		$ret = array(0=>$row['rating'],1=>$row['ts'],rating=>$row['rating'],ts=>$row['ts']);
		$this->db->sql_freeresult($result);
		return $ret;
	}

	function set_rating($member_id,$product_id,$rating,$cat = 1)
	{
		if (!isset($member_id) || !is_numeric($member_id) || !isset($product_id) || !is_numeric($product_id) || ($rating < 0.0 && $rating != VG_NOT_INTERESTED) || $rating > 1.0)
		{
			return false;
		}

		$sql = <<<EOF
SELECT rating
FROM vogoo_ratings
WHERE member_id = {$member_id}
AND product_id = {$product_id}
AND category = {$cat}
EOF;
		if ( !($result = $this->db->sql_query($sql)) )
		{
			return false;
		}

		$nr = $this->db->sql_numrows($result);
		if ($nr == 1)
		{
			$row = $this->db->sql_fetchrow($result);
			$previous = $row['rating'];
			$this->db->sql_freeresult($result);
			if (VG_DIRECT_LINKS)
			{
				set_direct_links($member_id,$product_id,$cat,$rating,$previous);
			}
			if (VG_DIRECT_SLOPE)
			{
				set_direct_slope($member_id,$product_id,$cat,$rating,$previous);
			}
			$sql = <<<EOF
UPDATE vogoo_ratings
SET rating = {$rating}, ts = NOW()
WHERE member_id = {$member_id}
AND product_id = {$product_id}
AND category = {$cat}
EOF;
			if ( !($result = $this->db->sql_query($sql)) )
			{
				return false;
			}
			if ($this->db->sql_affectedrows() != 1)
			{
				return false;
			}
		}
		else if ($nr == 0)
		{
			$this->db->sql_freeresult($result);
			if (VG_DIRECT_LINKS)
			{
				set_direct_links($member_id,$product_id,$cat,$rating,-1.0);
			}
			if (VG_DIRECT_SLOPE)
			{
				set_direct_slope($member_id,$product_id,$cat,$rating,-1.0);
			}
			$sql = <<<EOF
INSERT INTO vogoo_ratings(member_id,product_id,category,rating,ts)
VALUES ({$member_id},{$product_id},{$cat},{$rating},NOW())
EOF;
			if ( !($result = $this->db->sql_query($sql)) )
			{
				return false;
			}
			if ($this->db->sql_affectedrows() != 1)
			{
				return false;
			}
		}
		else
		{
			return false;
		}
		return true;
	}

	function automatic_rating($member_id,$product_id,$purchase,$cat = 1)
	{
		if (!isset($member_id) || !is_numeric($member_id) || !isset($product_id) || !is_numeric($product_id))
		{
			return false;
		}
		if ($purchase)
		{
			return $this->set_rating($member_id,$product_id,1.0,$cat);
		}
		else
		{
			// A click
			$res = $this->get_rating($member_id,$product_id,false,$cat);
			if (count($res) == 0)
			{
				return $this->set_rating($member_id,$product_id,0.7,$cat);
			}
			else if ($res[0] < 1.0)
			{
				return $this->set_rating($member_id,$product_id,$res[0]+0.01,$cat);
			}
		}
	}

	function set_not_interested($member_id,$product_id,$cat = 1)
	{
		return $this->set_rating($member_id,$product_id,VG_NOT_INTERESTED,$cat);
	}

	function delete_rating($member_id,$product_id,$cat = 1)
	{
		if (!isset($member_id) || !is_numeric($member_id) || !isset($product_id) || !is_numeric($product_id))
		{
			return false;
		}
		if (VG_DIRECT_LINKS || VG_DIRECT_SLOPE)
		{
			$sql = <<<EOF
SELECT rating
FROM vogoo_ratings
WHERE member_id = {$member_id}
AND product_id = {$product_id}
AND category = {$cat}
EOF;
			if ( !($result = $this->db->sql_query($sql)) )
			{
				return false;
			}

			$nr = $this->db->sql_numrows($result);
			if ($nr == 1)
			{
				$row = $this->db->sql_fetchrow($result);
				if (VG_DIRECT_LINKS)
				{
					set_direct_links($member_id,$product_id,$cat,-1.0,$row['rating']);
				}
				if (VG_DIRECT_SLOPE)
				{
					set_direct_slope($member_id,$product_id,$cat,-1.0,$row['rating']);
				}
			}
			$this->db->sql_freeresult($result);
		}
		$sql = <<<EOF
DELETE
FROM vogoo_ratings
WHERE member_id = {$member_id}
AND product_id = {$product_id}
AND category = {$cat}
EOF;
		if ( !($this->db->sql_query($sql)) )
		{
			return false;
		}

		return true;
	}
	// }}}
}

$vogoo = new vogoo_class($vg_dbhost,$vg_dbuser,$vg_dbpasswd,$vg_dbname);

} // ... defined
?>
