<?php

define('DEFAULT_CONFIG', dirname(__FILE__) . '/' . basename(__FILE__, '.php') . '.ini');
define('INI_PROCESS_SECTIONS', true);

/**
 * Load configuration from INI file
 *
 * @param string $configFile Path to configuration INI file
 * @return array
 */
function getConfig($configFile) {
	$result = array();

	if (is_file($configFile) && is_readable($configFile)) {
		$result = parse_ini_file($configFile, INI_PROCESS_SECTIONS);
	}

	return $result;
}

/**
 * Get the list of shows to download
 *
 * @param array $config Configuration array
 * @return string
 */
function getShows($config) {
	$result = '';

	if (!empty($config['shows']['show'])) {
		$content = implode('|', $config['shows']['show']);
		$result = '(' . $content . ')';
	}

	return $result;
}

/**
 * Get the list of excludes
 *
 * @param array $config Configuration array
 * @return string
 */
function getExcludes($config) {
	$result = '';

	if (!empty($config['excludes']['exclude'])) {
		$content = implode('|', $config['excludes']['exclude']);
		$result = '(' . $content . ')';
	}

	return $result;
}

/**
 * Get the list of feeds to process
 *
 * @param array $config Configuration array
 * @return array
 */
function getFeeds($config) {
	$result = array();

	if (!empty($config['feeds']['feed'])) {
		$result = $config['feeds']['feed'];
	}

	return $result;
}

/**
 * Get content of specified URL
 *
 * @param string $url URL to fetch
 * @return string
 */
function getUrlContent($url) {
	$result = '';

	$ch = curl_init();
	if ($ch) {
		curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
	}

	return $result;	
}	

/**
 * Find URLs and titles of items in a given feed
 *
 * Since there are several ways a feed can provide URL
 * to the item, we'll look for 'link' attribute first.
 * If it wasn't found, we'll settle for 'guid'.
 *
 * @param string $url URL of the feed
 * @return array Associative array: URL=>Title
 */
function getFeedItems($url) {
	$result = array();

	libxml_use_internal_errors(true);
	$xml = simplexml_load_string(getUrlContent($url));
	if ($xml) {
		foreach ($xml->channel->item as $item) {
			$link = ($item->link) ? $item->link : $item->guid;
			$result[ (string) $link ] = (string) $item->title;
		}
	}

	return $result;
}

/**
 * Get only items of interest from all available feed items
 *
 * @param array $feedItems Associative array URL=>Title of items in the feed
 * @param string $shows Pattern to look for in the titles
 * @param string $exclude Pattern to ignore in the titles
 * @return array
 */
function cleanFeedItems($feedItems, $shows, $exclude) {
	$result = array();

	foreach ($feedItems as $link => $title) {
		// Ignore exludes
		if (preg_match("/$exclude/is", $title)) {
			continue;
		}

		if (preg_match("/$shows\s(.*?)S([0-9]+?)E([0-9]+?)\s/is", $title, $m)){
			$episode = "S".$m[3]."E".$m[4];
			preg_match("/(.*?)$episode(.*?)/is", $title, $cleanTitle);
			$result[] = array(
				'show' => ucfirst(trim($cleanTitle[1])),
				'url' => $link,
				'episode' => $episode,
			);
		}			
	}

	return $result;
}

/**
 * Download torrent file
 *
 * @param string $url URL of the file to download
 * @param array $config Configuration array
 * @return boolean True on success, false otherwise
 */
function downloadTorrent($url, $config){
	$result = false;

	$fileName = basename($url);
	$fileExtension = $config['paths']['torrents_extension'];
	$fileFolder = $config['paths']['torrents_folder'];

	if (!preg_match("/$fileExtension$/", $fileName)) {
		$fileName .= $fileExtension;
	}

	if (!file_exists($fileFolder)) {
		mkdir($fileFolder);
	}

	if (file_exists($fileFolder) && is_dir($fileFolder) && is_writable($fileFolder)) {
		$fileName = $fileFolder . $fileName;
	}

	$ch = curl_init($url);
	if ($ch) {
		curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
		$fp = fopen($fileName, 'w+');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$result = curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}

	return $result;
}

/**
 * Get history of downloaded files
 *
 * @param array $config Configuration array
 * @return array
 */
function getHistory($config) {
	$result = array();

	$historyFile = $config['paths']['history'];
	if (is_file($historyFile) && file_exists($historyFile) && is_readable($historyFile)) {
		$result = file($historyFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	}

	return $result;
}

/**
 * Update history of downloaded files
 *
 * @param array $history History
 * @param array $config Configuration array
 * @return numeric Number of bytes written to file
 */
function saveHistory($history, $config) {
	$historyFile = $config['paths']['history'];
	return file_put_contents($historyFile, implode("\n", $history));
}


/**
 * Process feeds
 *
 * @param array $feeds List of feeds to process
 * @param array $config Configuration array
 * @return array Processing stats
 */
function processFeeds($feeds, $config) {
	$result = array();

	$shows = getShows($config);
	$excludes = getExcludes($config);
	foreach ($feeds as $feed) {
		$feedItems = getFeedItems($feed);
		$cleanItems = cleanFeedItems($feedItems, $shows, $excludes);
		$history = getHistory($config);
		
		foreach($cleanItems as $d){
			$entry = $d['show']." ".$d['episode'];
			if (!in_array($entry,$history)) {
				downloadTorrent($d['url'], $config);
				$history[] = $entry;
				$result[$feed][] = $entry;
			}
		}

		saveHistory($history, $config);
	}

	return $result;
}

/**
 * Print report
 *
 * @param array $stats Array with data
 */
function printReport($stats) {
	if (!empty($stats)) {
		print "Downloads report\n";
		print "================\n";
		foreach ($stats as $url => $files) {
			print $url . "\n";
			sort($files);
			foreach ($files as $file) {
				print "\t$file\n";
			}
			print "\n";
		}
	}
}

// If no config specified, use the default one
$config = empty($argv[1]) ? getConfig(DEFAULT_CONFIG) : getConfig($argv[1]);

if (empty($config)) {
	die("Empty config. Nothing to do. Work on your " . DEFAULT_CONFIG);
}

$feeds = getFeeds($config);
$stats = processFeeds($feeds, $config);
printReport($stats);

?>
