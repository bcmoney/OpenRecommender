<?php 
	/*
	 * loadJSONDoc
	 *   loads a JSON document from physical memory, or, as a well-formed
	 *   JSON Document Stream from a server-side script or another server
	 * NOTE:
	 *   JSON from another server may be subject to the "Same-Origin"
	 *   policy which limits a client's communication to the server it is
	 *   being run on (except if the remote server supports JSONp callbacks,
	 *   and, you trust them to inject data to your javascript)
	 * EXAMPLE USAGE:
	 *    loadJSONDoc("data.json"); //data.json is on the same server
	 *    loadJSONDoc("getData.jsp"); //getData.jsp (.asp or .php for that matter) could connect to a database on the same server, or, contact another server (or web service) and send back the data as JSON
	 *@param file String   the JSON Document to be loaded
	 *@return jsonDoc the JSON String formatted as parse-able XML
	 */
	function loadJSONDoc($file)
	{
		$json = file_get_contents($file);
		$jsonDoc = json_decode($json, true);
		return $jsonDoc;
	}
	/*
	 * loadJSONString
	 *   loads JSON from a regular string of text (i.e. a var of JSON text
	 *   in this script, or, the result of pulling a string of text from a
	 *   Web Service somewhere)
	 * @param jsonString string of text to parse as JSON
	 * @return jsonString (returns exact string passed into it, for testing)
	 */        
	function loadJSONString($jsonString)
	{
		return json_decode($jsonString, true);
	}
	/*
	 * loadJSONObject
	 *   loads JSON from an object (i.e. one in this script file itself, or,
	 *   one that gets injected from a JSONp Web Service callback)
	 * @return default JSON object (example/testing purposes only)
	 */
	function loadJSONObject($json)
	{
		$jsonObject = json_decode($json, false);
		return $jsonObject;
	}
	/*
	 * loadJSONCookie
	 *   loads JSON form a regular var string of text      
	 * @param sID session ID which acts as the key for the JSON cookie SharedMappingList
	 */
	function loadJSONCookie($sessionID)
	{
		return $_COOKIE[$sessionID];
	}


	$accounts_json = '{ "service" : [ /* Book */ { "name" : "goodreads", "url" : "http://goodreads.com", "icon" : "/images/icons/goodreads.gif", "account" : { "username" : "bcmoney", "profile" : "http://www.goodreads.com/bcmoney", "feed" : "http://www.goodreads.com/user/updates_rss/1711344?key=9ec7708446cc20b18659d4a782924bfbc9ef6046" } }, /* Text */ { "name" : "blog", "url" : "http://bcmoney-mobiletv.com/blog", "icon" : "/images/icons/blog.gif", "account" : { "username" : "bcmoney", "profile" : "http://bcmoney-mobiletv.com/blog/?author=1", "feed" : "http://bcmoney-mobiletv.com/blog/?feed=rss2" } }, { "name" : "twitter", "url" : "http://twitter.com", "icon" : "/images/icons/twitter.gif", "account" : { "username" : "bcmoney", "profile" : "http://www.twitter.com/bcmoney", "feed" : "http://twitter.com/statuses/user_timeline/bcmoney.rss?page=1&count=20" } }, /* Image */ { "name" : "flickr", "url" : "http://flickr.com", "icon" : "/images/icons/flickr.gif", "account" : { "username" : "bcmoney", "profile" : "http://www.flickr.com/photos/bcmoney", "feed" : "http://api.flickr.com/services/feeds/photos_public.gne?id=97951665@N00&lang=en-us&format=rss_200" } }, /* Audio */ { "name" : "last.fm", "url" : "http://last.fm", "icon" : "/images/icons/last.fm.gif", "account" : { "username" : "bcmoney", "profile" : "http://www.last.fm/user/bcmoney", "feed" : "http://ws.audioscrobbler.com/1.0/user/bcmoney/recenttracks.rss" } }, /* Video */ { "name" : "bcmoney", "url" : "http://bcmoney-mobiletv.com", "icon" : "/images/icons/bcmoney.gif", "account" : { "username" : "bryan", "profile" : "http://bcmoney-mobiletv.com/bryan", "feed" : "http://bcmoney-mobiletv.com/bryan/uploads/mrss/" } }, { "name" : "youtube", "url" : "http://youtube.com", "icon" : "/images/icons/youtube.gif", "account" : { "username" : "bcmoney", "profile" : "http://youtube.com/bcmoney", "feed" : "http://gdata.youtube.com/feeds/base/users/BCmoney/uploads?alt=rss&v=2&orderby=published&client=ytapi-youtube-profile" } }, /* News */ { "name" : "delicious", "url" : "http://delicious.com", "icon" : "/images/icons/delicious.gif", "account" : { "username" : "bcmoney", "profile" : "http://www.delicious.com/bcmoney", "feed" : "http://feeds.delicious.com/v2/rss/bcmoney?count=100" } } ] }';	
	$accounts = loadJSONString($accounts_json);
	echo $accounts;
	
	
?>