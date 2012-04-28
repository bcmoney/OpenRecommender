<?php

  public function Sort()
  {
        for ($i = $this->data->count() - 1; $i >= 0; $i--)
        {
              $flipped = false;
              for ($j = 0; $j < $i; $j++)
              {
                    if (strcmp($this->data[$j]->GetSortKey(), 
                          $this->data[$j + 1]->GetSortKey()) > 0)
                    { 
                          $tmp = $this->data[$j];
                          $this->data->offsetSet($j, $this->data
[$j + 1]);
                          $this->data->offsetSet($j + 1, $tmp);
                          $flipped = true;
                    }
              }
              if (!$flipped)
                    return;
        }
  }
  
  function checkSort($array)
{
 if (!$length = count($array)) {
  return true;
 }
 for ($i = 0; $i < $length; $i++) {
  if (isset($array[$i+1])) {
   if ($array[$i]>$array[$i+1]) {
    return false;
   }
  }
 }
 return true;
}


function getMicrotime($t){
  list($usec, $sec) = explode(" ",$t);
  return ((float)$usec + (float)$sec);  
}



$start = microtime();
$a = array();
for($i=0;$i<10000;$i++) {
  $a[] = $i;
}
$end = microtime();
$time = (getMicrotime($end) - getMicrotime($start));

?>