<?php
    /* Last updated with phpFlickr 1.4
     *
     * If you need your app to always login with the same user (to see your private
     * photos or photosets, for example), you can use this file to login and get a
     * token assigned so that you can hard code the token to be used.  To use this
     * use the phpFlickr::setToken() function whenever you create an instance of 
     * the class.
     */

    require_once("phpFlickr.php");
    
    $f = new phpFlickr("f270b1a114217e13f7269c0e90efc7a8", "b2c58ae4a63bf3cc");        
    $f->auth("read"); //change this to the permissions you will need
    
    echo "Copy this token into your code: " . $_SESSION['phpFlickr_auth_token'];
    
?>