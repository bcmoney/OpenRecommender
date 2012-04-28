<?php
    // Demonstrate iterating over a response set, checking for an error & working with the result data. 
    foreach ($result as $key => $result_item) {
        if ($result_item instanceof osapiError) {
          echo "<h2>There was a <em>".$result_item->getErrorCode()."</em> error with the <em>$key</em> request:</h2>";
          echo "<pre>".htmlentities($result_item->getErrorMessage())."</pre>";
        } else {
          echo "<h2>Response for the <em>$key</em> request:</h2>";
          echo "<pre>".htmlentities(print_r($result_item, True))."</pre>";
        }
    }
?>