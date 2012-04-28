function bidirectionalBubbleSort($array){
 if(!$length = count($array)){
  return $array;
 }
 $start = -1;
 while($start <  $length){
  ++$start;
  --$length;
  for($i= $start; $i <  $length; ++$i){
   if($array[$i] > $array[$i + 1]){
    $temp = $array[$i];
    $array[$i] = $array[$i + 1];
    $array[$i + 1] = $temp;
   }
  }
  for($i = $length; --$i >= $start;){
   if($array[$i] > $array[$i + 1]){
    $temp = $array[$i];
    $array[$i] = $array[$i + 1];
    $array[$i + 1] = $temp;
   }
  }
 }
}