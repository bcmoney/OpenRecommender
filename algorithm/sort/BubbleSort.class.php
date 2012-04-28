function bubbleSort($array)
{
 if (!$length = count($array)) {
  return $array;
 }  
 for ($outer = 0; $outer < $length; $outer++) {
  for ($inner = 0; $inner < $length; $inner++) {
   if ($array[$outer] < $array[$inner]) {
    $tmp = $array[$outer];
    $array[$outer] = $array[$inner];
    $array[$inner] = $tmp;
   }
  }
 }
}