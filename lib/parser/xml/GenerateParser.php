<?php
  $file = $_REQUEST['f']; 
 
  $xml = simplexml_load_file($file);  
 
  function classHeader() {
    echo "&lt;?php<br/>";
  }
 
  function rootGetter($xml) {  
    $root = $xml->getName();
	  $lcroot = lcfirst($root);
	  $ucroot = ucfirst($root);	
    echo "class ".$root."{<br/>&nbsp; private \$".$lcroot.";<br/><br/>&nbsp; public function __construct() {<br/><br/>&nbsp; }<br/><br/>&nbsp; get".$ucroot,"(\$".$root.") {<br/> &nbsp;&nbsp;&nbsp; return \$this->".$lcroot.";<br/>&nbsp; }<br/>";
  }
  
  function nodeGetter($xml) {
    //handle elements
	foreach ($xml->children() as $child) {
	  $node = $child->getName();
	    $lcnode = lcfirst($node);
	    $ucnode = ucfirst($node);
	  printf("&nbsp; public function get%s(\$%s) {<br/> &nbsp;&nbsp;&nbsp; return \$this->%s;<br/>&nbsp; }<br/>", $ucnode,$lcnode,$lcnode);
	  
	  //handle attributes
      foreach($child->attributes() as $attribute) {
	    $attr = $attribute->getName();
	      $lcattr = lcfirst($attr);
	      $ucattr = ucfirst($attr);
        printf("&nbsp; get%s%s(\$%s) {<br/> &nbsp;&nbsp;&nbsp; return \$this->%s;<br/>&nbsp; }<br/>", $ucnode,$ucattr,$lcattr,$lcattr);
      }
	  
	  //handle multiple sibling nodes
	  if (!is_null($child->children) && count($child->children()) > 0 && (!in_array($node,get_all_siblings($child->children())))) {
	    nodeGetter($child);
	  }
	}
  }
  
  //xpath magic to find all siblings (used to filter identically named nodes)
  function get_all_siblings(SimpleXMLElement $node) {
    return $node->xpath('preceding-sibling::* | following-sibling::*');
  }
  
  function classFooter() {
	echo "<br/>}<br/><br/>?&gt;<br/>";
  }  
  
    
  classHeader();
    rootGetter($xml);	
    nodeGetter($xml);
  classFooter();
  
?>