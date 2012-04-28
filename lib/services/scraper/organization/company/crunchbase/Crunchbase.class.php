<?php








/**********************************************************************/
/*
Plugin Name: CrunchBase Widget
Version: 1.1
Plugin URI: http://yoast.com/wordpress/crunchbase/
Description: Provides easy shortcode access to inserting Crunchbase widgets
Author: Joost de Valk
Author URI: http://yoast.com/

Copyright 2008 Joost de Valk (email: joost@joostdevalk.nl)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function cb_determine_type($input) {
	$input = strtolower($input);
	switch($input) {
		case 'product':
			return "product";
		case 'person':
			return "person";
		case 'financial':
		case 'financial organization':
		case 'financial-organization':
			return "financial-organization";
		case 'company':
		default:
			return "company";
	}
}

function crunchbase_widget($atts, $content) {
	$type = cb_determine_type($atts['type']);
	$info = strtolower(str_replace(" ","-",$content));
	$name = ucwords(str_replace("-"," ",$content));

	if (is_feed()) {
		$output = '<a href="http://www.crunchbase.com/'.$type.'/'.$info.'">CrunchBase Information on '.$name.'</a><br/>';
	} 
  else {
		$output = '<div class="cbw snap_nopreview"><div class="cbw_header">'
					.'<script src="http://www.crunchbase.com/javascripts/widget.js" type="text/javascript"></script>'
					.'<div class="cbw_header_text">'
					.'<a href="http://www.crunchbase.com/'.$type.'/'.$info.'">'
					.'CrunchBase Information on '.$name.'</a>'
					.'</div></div><div class="cbw_content">'
					.'<div class="cbw_subheader">'
					.'<a href="http://www.crunchbase.com/'.$type.'/'.$info.'">'.$name.'</a></div>'
					.'<div class="cbw_subcontent">'
					.'<script src="http://www.crunchbase.com/cbw/'.$type.'/'.$info.'.js" type="text/javascript">'
					.'</script></div>'
					.'<div class="cbw_footer">Information provided by <a href="http://www.crunchbase.com/">CrunchBase</a>'
					.'</div></div></div>';		
	}
	return $output;
}

?>