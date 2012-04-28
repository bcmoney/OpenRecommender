<?php

include "Diff.class.php"

?>

<html> 
<head><title>diff example</title></head> 
<body> 
<h3>Implementation of DIFF in pure-php</h3> 
this outputs the difference in gnu diff(1) syntax.  
<br /> 
<? 
    #example usage: 
     
     $f1_arr=Array(  "<html>", 
                "<head><title>Text</title></head>", 
                "<body>", 
                "code a", 
                "code b", 
                "code c", 
                "code d", 
                "code e", 

                "code g", 
                "</body>", 
                "</html>" ); 

    $f2_arr=Array(  "<html>", 
                "<head><title>Text2</title></head>", 
                "<body>", 
                "code a", 
                "code a", 

                "code c", 
                "code d", 
                "code e", 


                "code g", 
                "code f", 
                "</body>", 
                "</html>" ); 

    #you can use files as input and compare them simply with, this gives you simple diff in your webserver. 
    # $f3 = file("path to file"); 
     
    $f1 = implode( "\n", $f1_arr );  
    $f2 = implode( "\n", $f2_arr );  

    print "<pre>"; 
    print "Input-Data: <xmp>"; 
    print_r( $f1_arr ); 
    print_r( $f2_arr ); 
    print "</xmp>"; 

    print "<hr />new, old <br />";  
    print PHPDiff( $f1, $f2 ); 

    print "<hr />old, new <br />";  
    print PHPDiff( $f2, $f1 ); 


    #comparing with array_diff() 

    print "<hr>Compared: array_diff( \$f1_arr, \$f2_arr );<br> "; 
    print "<xmp>"; 
    print_r ( array_diff( $f1_arr, $f2_arr ) ); 
    print "</xmp>"; 
     
    print "<hr>Compared: array_diff( \$f2_arr, \$f1_arr );<br> "; 
    print "<xmp>"; 
    print_r ( array_diff( $f2_arr, $f1_arr ) ); 
    print "</xmp>"; 
    print "</pre>"; 

    print "<hr>"; 

?></body></html>