<?php

require_once 'Torrent.class.php';

// create torrent
$torrent = new Torrent('./torrents', 'http://torrent.tracker/annonce');
if (!$error = $torrent->error()) {
	$torrent->save('test.torrent'); // save to disk
}
else {
	echo '<br>DEBUG: ',$error; // error method return the last error message
}

// print torrent info
$torrent = new Torrent( './test.torrent' );
echo '<pre>private: ', ($torrent->is_private()?'yes':'no'),
	 '<br>annonce: ';
var_dump( $torrent->announce() );
echo '<br>name: ', $torrent->name(),
	 '<br>comment: ', $torrent->comment(),
	 '<br>piece_length: ', $torrent->piece_length(),
	 '<br>size: ', $torrent->size( 2 ),
	 '<br>hash info: ', $torrent->hash_info(),
	 '<br>stats: ';
var_dump( $torrent->scrape() );
echo '<br>content: ';
var_dump( $torrent->content() );
echo '<br>source: ',
	 $torrent;

// modify torrent
$torrent->announce('http://alternate-torrent.tracker/annonce'); // add a tracker
$torrent->announce(false); // reset announce trackers
$torrent->announce(array('http://torrent.tracker/annonce', 'http://alternate-torrent.tracker/annonce')); // set tracker(s), it also works with a 'one tracker' array...
$torrent->announce(array(array('http://torrent.tracker/annonce', 'http://alternate-torrent.tracker/annonce'), 'http://another-torrent.tracker/annonce')); // set tiered trackers
$torrent->comment('hello world');
$torrent->name('test torrent');
$torrent->is_private(true);
$torrent->httpseeds('http://file-hosting.domain/path/'); // Bittornado implementation
$torrent->url_list(array('http://file-hosting.domain/path/','http://another-file-hosting.domain/path/')); // GetRight implementation

// print errors
if ($errors = $torrent->errors()) {
	var_dump( '<br>DEBUG: ', $errors ); // errors method return the error stack
}

// send to user
$torrent->send();



///////////////////////////////////////////////////////////////////////////////
//parse Torrent
require 'TorrentReader.php';
$debug = 0;

if ( !empty($_FILES['torrent']) ) {
	if ( !empty($_FILES['torrent']) && empty($_FILES['torrent']['error']) && file_exists($_FILES['torrent']['tmp_name']) ) {
		$szTorrentFile = $_FILES['torrent']['tmp_name'];
		$szTorrentFileName = $_FILES['torrent']['name'];
		$szTorrentContent = file_get_contents($szTorrentFile);
	}
}

if ( !empty($_POST['content']) ) {
	if ( !empty($_POST['content']) ) {
		$szTorrentFile = $szTorrentFileName = 'custom';
		$szTorrentContent = $_POST['content'];
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<title>OpenRecommender - Torrent protocol handler</title>
<style>
::selection { background-color:#000; color:#fff; }
.debug { color:red; opacity:0.3; }
</style>
</head>
<body>
<form method="post" action="" enctype="multipart/form-data">
	<fieldset>
		<legend>Upload .torrent</legend>
		<p>Torrent: <input type=file name=torrent></p>
		<p>or Data: <textarea name=content rows="2" cols="90"><?=isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''?></textarea></p>
		<p><input type=submit value=Upload></p>
	</fieldset>
</form>
<?php isset($szTorrentContent, $szTorrentFileName) or exit; ?>
<h1><?=$szTorrentFileName?></h1>
<!--
	See http://www.answers.com/topic/bencode for encoding algorithm

	Example input:
	d3:inti5000e5:floatf10.12e6:string4:oele10:dictionaryd3:kut6:jammie4:cock3:bahe4:listl4:val14:val24:val3ee
	Output:
	Array
	(
		[int] => 5000
		[float] => 10.12
		[string] => oele
		[dictionary] => Array
			(
				[kut] => jammie
				[cock] => bah
			)

		[list] => Array
			(
				[0] => val1
				[1] => val2
				[2] => val3
			)

	)
-->
<pre>
<?php
  $output = TorrentReader::parse($szTorrentContent, $reader);
  echo 'Iterations: '.$reader->iterations."\n\n"; 
  print_r($output);
?>
</pre>
</body>
</html>