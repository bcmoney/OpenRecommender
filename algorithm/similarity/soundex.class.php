<?php

$word2find = 'stupid';
 
$words = array(
    'stupid',
    'stu and pid',
    'hello',
    'foobar',
    'stpid',
    'supid',
    'stuuupid',
    'sstuuupiiid',
);
 
while(list($id, $str) = each($words)) {
     
    $soundex_code = soundex($str);
     
    if (soundex($word2find) == $soundex_code){
        echo '"' . $word2find . '" sounds like ' . $str . "<br/>";
    }
    else {
        echo '"' . $word2find . '" does not sound like ' . $str . "<br/>";
    }
     
    echo "\n";
}


?>