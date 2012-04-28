<?php

/**
* Returns conditional probability of $A given $B and $Data.
* $Data is an indexed array.  Each element of the $Data array 
* consists of an A measurement and B measurment on a sample 
* item.
*/
function getConditionalProbabilty($A, $B, $Data) {
  $NumAB   = 0;
  $NumB    = 0;
  $NumData = count($Data);
  for ($i=0; $i < $NumData; $i++) {
    if (in_array($B, $Data[$i])) {
      $NumB++;
      if (in_array($A, $Data[$i])) {
        $NumAB++;
      }
    }
  }
  return $NumAB / $NumB;
}

?>


<?php

/**
* The elements of the $Data array use this coding convention:
*
* +cancer - patient has cancer
* -cancer - patient does not have cancer
* +test   - patient tested positive on cancer test
* -test   - patient tested negative on cancer test
*/

$Data[0] = array("+cancer", "+test");
$Data[1] = array("-cancer", "-test");
$Data[2] = array("+cancer", "+test");
$Data[3] = array("-cancer", "+test");

// specify query variable $A and conditioning variable $B
$A = "+cancer";
$B = "+test";

// compute the conditional probability of having cancer given 1) 
// a positive test and 2) a sample of covariation data
$probability = getConditionalProbabilty($A, $B, $Data);

echo "P($A|$B) = $probability";

// P(+cancer|+test) = 0.66666666666667

?>