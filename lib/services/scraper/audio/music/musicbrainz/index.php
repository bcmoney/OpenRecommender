<?php
require("phpBrainz.class.php");
$DEBUG = false; //"true" to display full search array, "false" to display normal (human-readable) results list

$a = $_REQUEST['artist'];
$artist = (!empty($a)) ? $a : "Buddy Holly";
$s = $_REQUEST['song'];
$song = (!empty($s)) ? $s : "Weezer";

$mb = new phpBrainz();
$mb_rf = new phpBrainz_TrackFilter(
    array(
        "title"=>$song,
        "artist"=>$artist
        ));

$time1 = microtime(true);
//  print_r($mb->getASINFromTrackMBID("7a408099-5c69-4f53-8050-6b15837398d1"));
//  print_r($mb->getTrack());
$time2 = microtime(true);
$runtime = "\n".($time2-$time1)."\n";

$result = '';
?>
<form>
  <input type="text" name="artist" value="<?php echo $artist; ?>" />
  <input type="text" name="song" value="<?php echo $song; ?>" />
  <input type="submit" name="search" value="Search" />
</form>
<?php 
  if($DEBUG) { echo $runtime."<pre>"; print_r($mb->findTrack($mb_rf)); echo "</pre>"; } 
  else {
    echo "<ul>";
    foreach($mb->findTrack($mb_rf) as $track) { 
        $releases = $track->getReleases();        
        foreach ($releases as $release) {
          echo "<li><a href=\"http://musicbrainz.org/release/".$release->getId()."\">".$release->getTitle()."</a> (".$release->getTracksCount()." <em>tracks</em>)</li>";
        }
    }
    echo "</ul>";
  }
?>