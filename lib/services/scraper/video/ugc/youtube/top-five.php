<!DOCTYPE html 
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Listing videos in different categories</title>
  </head>
  <body>
    <?php
    // set URL for XML feed containing category list
    $catURL = 'http://gdata.youtube.com/schemas/2007/categories.cat';
    
    // retrieve category list using atom: namespace
    // note: you can cache this list to improve performance, 
    // as it doesn't change very often!
    $cxml = simplexml_load_file($catURL);
    $cxml->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
    $categories = $cxml->xpath('//atom:category');
    
    // iterate over category list
    foreach ($categories as $c) {
      // for each category    
      // set feed URL
      $feedURL = "http://gdata.youtube.com/feeds/api/videos/-/{$c['term']}?max-results=5&orderby=viewCount";
      
      // read feed into SimpleXML object
      $sxml = simplexml_load_file($feedURL);
      
      // get summary counts from opensearch: namespace
      $counts = $sxml->children('http://a9.com/-/spec/opensearchrss/1.0/');
      $total = $counts->totalResults; 
      ?>
      
      <h1><?php echo $c['label']; ?></h1>
      <?php echo $total; ?> items found.
      <p/>
      
      <?php    
      echo "<ol>\n";
      // iterate over entries in category
      // print each entry's details
      foreach ($sxml->entry as $entry) {
        // get nodes in media: namespace for media information
        $media = $entry->children('http://search.yahoo.com/mrss/');
        
        // get video player URL
        $attrs = $media->group->player->attributes();
        $watch = $attrs['url']; 
        
        // get <yt:duration> node for video length
        $yt = $media->children('http://gdata.youtube.com/schemas/2007');
        $attrs = $yt->duration->attributes();
        $length = $attrs['seconds']; 
        
        // get <gd:rating> node for video ratings
        $gd = $entry->children('http://schemas.google.com/g/2005'); 
        if ($gd->rating) {
          $attrs = $gd->rating->attributes();
          $rating = $attrs['average']; 
        } else {
          $rating = 0; 
        }

        // print record
        echo "<li>\n";
        echo "<a href=\"{$watch}\">{$media->group->title}</a>
        <br/>\n";
        echo sprintf("%0.2f", $length/60) . " min. | {$rating} user rating
        <br/>\n";
        echo substr($media->group->description,0,50) . "...<p/>\n";
        echo "<p/></li>\n";
      }
      echo "</ol>\n";
    }
    ?>
  </body>
</html>     