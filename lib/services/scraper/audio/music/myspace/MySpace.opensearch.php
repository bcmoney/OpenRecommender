<?php

//Search API Script 
$q=$_GET['q'];
 
if($_GET['q']==''){
 
$q = 'coldplay';
 
}
 
if($_GET['pics']==1){
 
$pics[0] = '&hasPhoto=on';
$pics[1] = 'checked';
 
}
 
if(!empty($_GET['location'])){
 
$pics[0] = '&location='.urlencode($_GET['location']).'';
 
}
 
$search = "http://api.myspace.com/opensearch/people?searchTerms=".urlencode($q)."&format=xml".$pics[0]."";
 
$cu = curl_init();
 
curl_setopt($cu, CURLOPT_URL, $search);
curl_setopt($cu, CURLOPT_RETURNTRANSFER, TRUE);
$result = curl_exec($cu);
$search_res = new SimpleXMLElement($result);
 
echo "<h3>".$search_res->totalResults." MySpace results for '".$q."'</h3>";
 
// Echo the Search Data
 
foreach ($search_res->entry as $user) {
 
$officialprofile = null;
 
if($user->isOfficial==1){
 
$officialprofile = 'official';
 
}
 
echo "<div class='user ".$officialprofile."'><a href=\"".$user->profileUrl."\" target=\"_blank\"><img border=\"0\" width=\"68\" class=\"user_image\" src=\"".$user->thumbnailUrl."\" title=\"".$user->displayName."\" /></a>";
echo "<div class='text'>".$user->displayName."</div> <strong>".$user->location."</strong><br/><a href='".$user->profileUrl."' >Visit ".$officialprofile." profile</a><div class='clear'></div></div>";
 
}
 
curl_close($cu);

?>
<style type="text/css">
body {
    margin:0px;
	background-color:#FFF;
	font-size:18px;
	font-family:Arial, Helvetica, sans-serif;
}

#header{
background-color:#FFFF99;
border-bottom: #FFFF66 3px solid;

}

.clear{
clear:both;}

#footer{
border-top:3px #CCCCCC solid; 
background-color:#efefef;
padding:10px 10px 10px 20px;
}

#container{
padding:20px;}

#noscript{
	background-color:#F90;
	padding:10px;
	font-size:12px;
	color:#fff;
	border-bottom:#F60 1px solid;
}
<style>