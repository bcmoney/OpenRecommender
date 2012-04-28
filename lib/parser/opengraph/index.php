<?php

include "OpenGraphNode.php";

error_reporting(0);

# Fetch and parse a URL
$page = (!empty($_REQUEST['url'])) ? $_REQUEST['url'] : 'http://www.rottentomatoes.com/m/oceans_eleven/';
$node = new OpenGraphNode($page);

# Retrieve the title
print $node->title . "<br/>\n";    # like this
print $node->title() . "<br/>\n";  # or with parentheses

# And obviously the above works for other Open Graph Protocol
# properties like "image", "description", etc. For properties
# that contain a hyphen, you'll need to use underscore instead:
print $node->street_address . "<br/>\n";

# OpenGraphNode uses PHP5's Iterator feature, so you can
# loop through it like an array.
#
foreach ($node as $key => $value) {
	print "$key => $value<br/>\n";
}

?>