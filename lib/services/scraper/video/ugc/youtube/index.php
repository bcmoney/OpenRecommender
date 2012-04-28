<!DOCTYPE html 
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>OpenRecommender - YouTube API</title>
    <style>
    img {
      padding: 2px; 
      margin-bottom: 15px;
      border: solid 1px silver; 
    }
    td {
      vertical-align: top;
    }
    td.line {
      border-bottom: solid 1px black;  
    }
    </style>
  </head>
  <body><!-- Searching for videos by keyword -->
    <?php
    // if form not submitted
    // display search box
    if (!isset($_GET['submit'])) {
    ?>
    <h1>Keyword search</h1>  
    <form method="get" action="<?php echo 
     htmlentities($_SERVER['PHP_SELF']); ?>">
      Keywords: <br/>
      <input type="text" name="vq" />
      <p/>
      Items sorted by: <br/>
      <select name="s">
        <option value="viewCount">User views</option>
        <option value="rating">User rating</option>
        <option value="published">Publication time</option>
      </select>
      <p/>
      Items per page: <br/>
      <select name="i">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
      <p/>
      <input type="submit" name="submit" value="Search"/>  
    </form>
    <?php      
    // if form submitted
    } else {
      // check for search keywords
      // trim whitespace
      // encode search string
      if (!isset($_GET['vq']) || empty($_GET['vq'])) {
        die ('ERROR: Please enter one or more search keywords');
      } else {
        $vq = $_GET['vq'];
        $vq = ereg_replace('[[:space:]]+', ' ', trim($vq));
        $vq = urlencode($vq);
      }
      
      // set max results per page
      if (!isset($_GET['i']) || empty($_GET['i'])) {
        $i = 25;
      } else {
        $i = htmlentities($_GET['i']);
      }
      
      // set sort critera
      if (!isset($_GET['s']) || empty($_GET['s'])) {
        $s = 'viewCount';
      } else {
        $s = htmlentities($_GET['s']);
      }
      
      // set start index
      if (!isset($_GET['pageID']) || $_GET['pageID'] <= 0) {
        $o = 1;  
      } else {        
        $pageID = htmlentities($_GET['pageID']);
        $o = (($pageID-1) * $i)+1;  
      }
      
      // generate feed URL
      $feedURL = "http://gdata.youtube.com/feeds/api/videos?vq={$vq}&orderby={$s}&max-results={$i}&start-index={$o}";
      
      // read feed into SimpleXML object
      $sxml = simplexml_load_file($feedURL);
      
      // get summary counts from opensearch: namespace
      $counts = $sxml->children('http://a9.com/-/spec/opensearchrss/1.0/');
      $total = $counts->totalResults; 
      $startOffset = $counts->startIndex; 
      $endOffset = ($startOffset-1) + $counts->itemsPerPage;       
      
      // include Pager class
      require_once 'Pager/Pager.php';
      $params = array(
          'mode'       => 'Jumping',
          'perPage'    => $i,
          'delta'      => 5,
          'totalItems' => $total,
      );
      $pager = & Pager::factory($params);
      $links = $pager->getLinks();     
      ?>
      
      <h1>Search results</h1>
      <?php echo $total; ?> items found. 
      Showing items <?php echo $startOffset; ?> to 
      <?php echo $endOffset; ?>:
      <p/>
      
      <?php
      // print page links
      echo $links['all'];
      ?>
      
      <table>
      <?php    
      // iterate over entries in resultset
      // print each entry's details
      foreach ($sxml->entry as $entry) {
        // get nodes in media: namespace for media information
        $media = $entry->children('http://search.yahoo.com/mrss/');
        
        // get video player URL
        $attrs = $media->group->player->attributes();
        $watch = $attrs['url']; 
        
        // get video thumbnail
        $attrs = $media->group->thumbnail[0]->attributes();
        $thumbnail = $attrs['url']; 
        
        // get <yt:duration> node for video length
        $yt = $media->children('http://gdata.youtube.com/schemas/2007');
        $attrs = $yt->duration->attributes();
        $length = $attrs['seconds']; 
        
        // get <yt:stats> node for viewer statistics
        $yt = $entry->children('http://gdata.youtube.com/schemas/2007');
        $attrs = $yt->statistics->attributes();
        $viewCount = $attrs['viewCount']; 
      
        // get <gd:rating> node for video ratings
        $gd = $entry->children('http://schemas.google.com/g/2005'); 
        if ($gd->rating) {
          $attrs = $gd->rating->attributes();
          $rating = $attrs['average']; 
        } else {
          $rating = 0; 
        }

        // get video ID
        $arr = explode('/',$entry->id);
        $id = $arr[count($arr)-1];
             
        // print record
        echo "<tr><td colspan=\"2\" class=\"line\"></td></tr>\n";
        echo "<tr>\n";
        echo "<td><a href=\"{$watch}\">
        <img src=\"$thumbnail\"/></a></td>\n";
        echo "<td><a href=\"{$watch}\">
        {$media->group->title}</a><br/>\n";
        echo sprintf("%0.2f", $length/60) . " min. | {$rating} user rating | 
        {$viewCount} views<br/>\n";
        echo $media->group->description . "<br/>\n";
        echo "<a href=\"details.php?id=$id\">More information</a>
        </td>\n"; 
        echo "</tr>\n";
      }
    }
    ?>
    </table>
  </body>
</html>