<?php

function pass(a, N, dig)  // e.g. in JavaScript              //A C 1
// pre:  a[1..N] is sorted on digits [dig-1..0]                l o 9
// post: a[1..N] is sorted on digits [dig..0]                  g m 9
 { var counter = new Array(11); // for digit occurrences         p 9
   var temp = new Array();                                   //D .
   var i, d;                                                 //S S
                                                             //  c
   for( d = 0; d <= 9; d++ ) counter[d] = 0;                 //  i
   for( i = 1; i <= N; i++ ) counter[ digit(a[i], dig) ] ++;
   for( d = 1; d <= 9; d++ ) counter[d] += counter[d-1];

   for( i = N; i >= 1; i-- )
    { temp[ counter[ digit(a[i], dig) ] -- ] = a[i]; }

   for( i = 1; i <= N; i++ ) a[i] = temp[i];
 }//pass

function radixSort(a, N)
 { var p;
   for( p=0; p < NumDigits; p++ )
      pass(a, N, p);
 }//radixSort

// e.g. number = 1066
//        digit 3^  ^digit 0

?>