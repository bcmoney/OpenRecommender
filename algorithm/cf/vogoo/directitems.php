<?php
/***************************************************************************
 *                                 directitems.php
 *                            -------------------
 *   copyright            : (C) 2006 Stephane DROUX
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

function set_direct_links($member_id,$product_id,$cat,$rating,$previous)
{
	global $vogoo;
	$operation = '';
	if ($rating >= VG_THRESHOLD_RATING && $previous < VG_THRESHOLD_RATING)
	{
		$operation = '+1';
	}
	else if ($rating < VG_THRESHOLD_RATING && $previous >= VG_THRESHOLD_RATING)
	{
		$operation = '-1';
	}
	if ($operation != '')
	{
		$threshold = VG_THRESHOLD_RATING;
		$sql = <<<EOF
SELECT product_id
FROM vogoo_ratings
WHERE member_id = {$member_id}
AND category = {$cat}
AND rating >= {$threshold}
AND product_id <> {$product_id}
EOF;
		if ( !($result = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		$id1 = $product_id;
		while ($row = $vogoo->db->sql_fetchrow($result))
		{
			$id2 = $row['product_id'];
			$sql = <<<EOF
SELECT cnt
FROM vogoo_links
WHERE item_id1={$id1}
AND item_id2={$id2}
AND category={$cat}
EOF;
			if ( !($res3 = $vogoo->db->sql_query($sql)) )
			{
				return false;
			}
			if ($vogoo->db->sql_numrows($res3) == 1)
			{
				$sql = <<<EOF
UPDATE vogoo_links
SET cnt=cnt{$operation}
WHERE item_id1={$id1}
AND item_id2={$id2}
AND category={$cat}
EOF;
				if ( !($res4 = $vogoo->db->sql_query($sql)) )
				{
					return false;
				}
				$sql = <<<EOF
UPDATE vogoo_links
SET cnt=cnt{$operation}
WHERE item_id1={$id2}
AND item_id2={$id1}
AND category={$cat}
EOF;
				if ( !($res4 = $vogoo->db->sql_query($sql)) )
				{
					return false;
				}

				if ($operation == '-1') {
					$sql = <<<EOF
DELETE FROM vogoo_links
WHERE cnt=0
EOF;
					$vogoo->db->sql_query($sql);
				}

			}
			else if ($operation == '+1')	// We should not get anything other than +1 here but it is a good idea to check it
			{
				$sql = <<<EOF
INSERT INTO vogoo_links(item_id1,item_id2,category,cnt,diff_slope)
VALUES ({$id1},{$id2},{$cat},1,0.0)
EOF;
				if ( !($res4 = $vogoo->db->sql_query($sql)) )
				{
					return false;
				}
				$sql = <<<EOF
INSERT INTO vogoo_links(item_id1,item_id2,category,cnt,diff_slope)
VALUES ({$id2},{$id1},{$cat},1,0.0)
EOF;
				if ( !($res4 = $vogoo->db->sql_query($sql)) )
				{
					return false;
				}
			}
			$vogoo->db->sql_freeresult($res3);
		}
		$vogoo->db->sql_freeresult($result);
	}
}

function set_direct_slope($member_id,$product_id,$cat,$rating,$previous)
{
	if ($rating < 0.0 && $previous < 0.0)
	{
		// Don't do anything
		return;
	}
	global $vogoo;
	$op = '';
	if ($previous < 0.0)
	{
		$op = 'add';
	}
	else if ($rating < 0.0)
	{
		$op = 'remove';
	}

	$diff = $rating - $previous;
	$sql = <<<EOF
SELECT product_id,rating
FROM vogoo_ratings
WHERE member_id = {$member_id}
AND category = {$cat}
AND rating >= 0.0
AND product_id <> {$product_id}
EOF;
	if ( !($result = $vogoo->db->sql_query($sql)) )
	{
		return false;
	}
	$id1 = $product_id;
	while ($row = $vogoo->db->sql_fetchrow($result))
	{
		$id2 = $row['product_id'];
		$rating2 = $row['rating'];
		$sql = <<<EOF
SELECT cnt
FROM vogoo_links
WHERE item_id1={$id1}
AND item_id2={$id2}
AND category={$cat}
EOF;
		if ( !($res3 = $vogoo->db->sql_query($sql)) )
		{
			return false;
		}
		if ($vogoo->db->sql_numrows($res3) == 1)
		{
			if ($op == 'remove')
			{
				$diff = $rating2 - $previous;
				$sql = <<<EOF
UPDATE vogoo_links
SET cnt=cnt-1,diff_slope=diff_slope-{$diff}
WHERE item_id1={$id1}
AND item_id2={$id2}
AND category={$cat}
EOF;
				if ( !($res4 = $vogoo->db->sql_query($sql)) )
				{
					return false;
				}
				$sql = <<<EOF
UPDATE vogoo_links
SET cnt=cnt-1,diff_slope=diff_slope+{$diff}
WHERE item_id1={$id2}
AND item_id2={$id1}
AND category={$cat}
EOF;
				if ( !($res4 = $vogoo->db->sql_query($sql)) )
				{
					return false;
				}
			}
			else if ($op == '')
			{
				$sql = <<<EOF
UPDATE vogoo_links
SET diff_slope=diff_slope-{$diff}
WHERE item_id1={$id1}
AND item_id2={$id2}
AND category={$cat}
EOF;
				if ( !($res4 = $vogoo->db->sql_query($sql)) )
				{
					return false;
				}
				$sql = <<<EOF
UPDATE vogoo_links
SET diff_slope=diff_slope+{$diff}
WHERE item_id1={$id2}
AND item_id2={$id1}
AND category={$cat}
EOF;
				if ( !($res4 = $vogoo->db->sql_query($sql)) )
				{
					return false;
				}
			}
			else if ($op == 'add')
			{
				$diff = $rating2 - $rating;
				$sql = <<<EOF
UPDATE vogoo_links
SET cnt=cnt+1,diff_slope=diff_slope+{$diff}
WHERE item_id1={$id1}
AND item_id2={$id2}
AND category={$cat}
EOF;
				if ( !($res4 = $vogoo->db->sql_query($sql)) )
				{
					return false;
				}
				$sql = <<<EOF
UPDATE vogoo_links
SET cnt=cnt+1,diff_slope=diff_slope-{$diff}
WHERE item_id1={$id2}
AND item_id2={$id1}
AND category={$cat}
EOF;
				if ( !($res4 = $vogoo->db->sql_query($sql)) )
				{
					return false;
				}
			}
		}
		else if ($op == 'add')	// We should not get anything other than 'add' here but it is a good idea to check it
		{
			$diff = $rating2 - $rating;
			$sql = <<<EOF
INSERT INTO vogoo_links(item_id1,item_id2,category,cnt,diff_slope)
VALUES ({$id1},{$id2},{$cat},1,{$diff})
EOF;
			if ( !($res4 = $vogoo->db->sql_query($sql)) )
			{
				return false;
			}
			$diff = -$diff;
			$sql = <<<EOF
INSERT INTO vogoo_links(item_id1,item_id2,category,cnt,diff_slope)
VALUES ({$id2},{$id1},{$cat},1,{$diff})
EOF;
			if ( !($res4 = $vogoo->db->sql_query($sql)) )
			{
				return false;
			}
		}
		$vogoo->db->sql_freeresult($res3);
	}
	$vogoo->db->sql_freeresult($result);

	$sql = <<<EOF
DELETE FROM vogoo_links
WHERE cnt=0
EOF;
	$vogoo->db->sql_query($sql);
}
?>
