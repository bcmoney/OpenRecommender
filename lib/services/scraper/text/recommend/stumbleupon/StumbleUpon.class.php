<?php

class StumbleUpon {

  

  function stumbleCountByURL($url) {
    $userid    = 'bcmoney';
    $authtoken = 'put your auth code here';
 
    $fetch = 'http://www.stumbleupon.com/links.php?username='.$userid.'&password='.$authtoken.'&u='.rawurlencode($url);
    $resp = file_get_contents($fetch);
 
    if(trim($resp) == '') { 
      return false; // URL wasn't stumbled yet
    }
    if(preg_match('/ERROR/i',$resp)) {
      return $resp; // some error occured
    }
 
    // parse answer in array
    $data = explode("\t",$resp);
    $info['comments'] = $data[0];
    $info['thumbed']  = $data[1];
    $info['score']    = $data[2];
    $info['topic']    = $data[3];
 
    return $info;
  }

  
}
  
?>