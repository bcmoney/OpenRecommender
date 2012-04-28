<!DOCTYPE html 
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Retrieving video details</title>
    <style>
    img {
      padding: 2px; 
      margin-bottom: 15px;
      border: solid 1px silver; 
    }
    td {
      vertical-align: top;
    }
    </style>
  </head>
  <body>
    <?php
    // function to parse a video <entry>
    function parseVideoEntry($entry) {      
      $obj= new stdClass;
      
      // get author name and feed URL
      $obj->author = $entry->author->name;
      $obj->authorURL = $entry->author->uri;
      
      // get nodes in media: namespace for media information
      $media = $entry->children('http://search.yahoo.com/mrss/');
      $obj->title = $media->group->title;
      $obj->description = $media->group->description;
      
      // get video player URL
      $attrs = $media->group->player->attributes();
      $obj->watchURL = $attrs['url']; 
      
      // get video thumbnail
      $attrs = $media->group->thumbnail[0]->attributes();
      $obj->thumbnailURL = $attrs['url']; 
            
      // get <yt:duration> node for video length
      $yt = $media->children('http://gdata.youtube.com/schemas/2007');
      $attrs = $yt->duration->attributes();
      $obj->length = $attrs['seconds']; 
      
      // get <yt:stats> node for viewer statistics
      $yt = $entry->children('http://gdata.youtube.com/schemas/2007');
      $attrs = $yt->statistics->attributes();
      $obj->viewCount = $attrs['viewCount']; 
      
      // get <gd:rating> node for video ratings
      $gd = $entry->children('http://schemas.google.com/g/2005'); 
      if ($gd->rating) { 
        $attrs = $gd->rating->attributes();
        $obj->rating = $attrs['average']; 
      } else {
        $obj->rating = 0;         
      }
        
      // get <gd:comments> node for video comments
      $gd = $entry->children('http://schemas.google.com/g/2005');
      if ($gd->comments->feedLink) { 
        $attrs = $gd->comments->feedLink->attributes();
        $obj->commentsURL = $attrs['href']; 
        $obj->commentsCount = $attrs['countHint']; 
      }
      
      // get feed URL for video responses
      $entry->registerXPathNamespace('feed', 'http://www.w3.org/2005/Atom');
      $nodeset = $entry->xpath("feed:link[@rel='http://gdata.youtube.com/schemas/2007#video.responses']"); 
      if (count($nodeset) > 0) {
        $obj->responsesURL = $nodeset[0]['href'];      
      }
         
      // get feed URL for related videos
      $entry->registerXPathNamespace('feed', 'http://www.w3.org/2005/Atom');
      $nodeset = $entry->xpath("feed:link[@rel='http://gdata.youtube.com/schemas/2007#video.related']"); 
      if (count($nodeset) > 0) {
        $obj->relatedURL = $nodeset[0]['href'];      
      }
    
      // return object to caller  
      return $obj;      
    }   
    
    // get video ID from $_GET 
    if (!isset($_GET['id'])) {
      die ('ERROR: Missing video ID');  
    } else {
      $vid = $_GET['id'];
    }

    // set video data feed URL
    $feedURL = 'http://gdata.youtube.com/feeds/api/videos/' . $vid;

    // read feed into SimpleXML object
    $entry = simplexml_load_file($feedURL);
    
    // parse video entry
    $video = parseVideoEntry($entry);
       
    // display main video record
    echo "<table>\n";
    echo "<tr>\n";
    echo "<td><a href=\"{$video->watchURL}\">
    <img src=\"$video->thumbnailURL\"/></a></td>\n";
    echo "<td><a href=\"{$video->watchURL}\">{$video->title}</a>
    <br/>\n";
    echo sprintf("%0.2f", $video->length/60) . " min. 
    | {$video->rating} user rating | {$video->viewCount} views<br/>\n";
    echo $video->description . "</td>\n";
    echo "</tr>\n";
    
    // read 'author profile feed' into SimpleXML object
    // parse and display author bio
    $authorFeed = simplexml_load_file($video->authorURL);    
    echo "<tr><td colspan=\"2\"><h3>Author information</h3>
    </td></tr>\n";
    $authorData = $authorFeed->children('http://gdata.youtube.com/schemas/2007');
    echo "<tr><td>Username:</td><td>" . $video->author . 
    "</td></tr>\n";
    echo "<tr><td>Age:</td><td>" . $authorData->age . 
    "</td></tr>\n";
    echo "<tr><td>Gender:</td><td>" . 
    strtoupper($authorData->gender) . "</td></tr>\n";
    echo "<tr><td>Location:</td><td>" . $authorData->location
     . "</td></tr>\n";    
    
    // read 'video comments' feed into SimpleXML object
    // parse and display each comment
    if ($video->commentsURL && $video->commentsCount > 0) {
      $commentsFeed = simplexml_load_file($video->commentsURL);    
      echo "<tr><td colspan=\"2\"><h3>" . 
      $commentsFeed->title . "</h3></td></tr>\n";
      echo "<tr><td colspan=\"2\"><ol>\n";
      foreach ($commentsFeed->entry as $comment) {
        echo "<li>" . $comment->content . "</li>\n";
      }
      echo "</ol></td></tr>\n";
    }
    
    // read 'video responses' feed into SimpleXML object
    // parse and display each video entry
    if ($video->responsesURL) {
      $responseFeed = simplexml_load_file($video->responsesURL);    
      echo "<tr><td colspan=\"2\"><h3>" . 
      $responseFeed->title . "</h3></td></tr>\n";
      foreach ($responseFeed->entry as $response) {
        $responseVideo = parseVideoEntry($response);
        echo "<tr>\n";
        echo "<td><a href=\"{$responseVideo->watchURL}\">
        <img src=\"$responseVideo->thumbnailURL\"/></a></td>\n";
        echo "<td><a href=\"{$responseVideo->watchURL}\">
        {$responseVideo->title}</a><br/>\n";
        echo sprintf("%0.2f", $responseVideo->length/60) . " min. |
         {$responseVideo->rating} user rating | {$responseVideo->viewCount} 
         views<br/>\n";
        echo $responseVideo->description . "</td>\n";
        echo "</tr>\n";      
      }
    }
    
    // read 'related videos' feed into SimpleXML object
    // parse and display each video entry
    if ($video->relatedURL) {
      $relatedFeed = simplexml_load_file($video->relatedURL);    
      echo "<tr><td colspan=\"2\"><h3>" . 
      $relatedFeed->title . "</h3></td></tr>\n";
      foreach ($relatedFeed->entry as $related) {
        $relatedVideo = parseVideoEntry($related);
        echo "<tr>\n";
        echo "<td><a href=\"{$relatedVideo->watchURL}\">
        <img src=\"$relatedVideo->thumbnailURL\"/></a></td>\n";
        echo "<td><a href=\"{$relatedVideo->watchURL}\">
        {$relatedVideo->title}</a><br/>\n";
        echo sprintf("%0.2f", $relatedVideo->length/60) . " min. | 
        {$relatedVideo->rating} user rating | {$relatedVideo->viewCount} 
        views<br/>\n";
        echo $relatedVideo->description . "</td>\n";
        echo "</tr>\n";      
      }
    }
    echo "</table>\n";    
    ?>
  </body>
</html>     