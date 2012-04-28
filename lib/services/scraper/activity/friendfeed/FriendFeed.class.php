<?php

/**
 * Initally created by Nikolai Kordulla <kordulla@googlemail.com>.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

/**
 * This module requires oAuth library
 */
//include_once(dirname(__FILE__) . '/../oauth/oauth.php');
include_once('../../../../api/authorization/oauth/oauth1a.php');
/**
 * This module requires the Curl PHP module, available in PHP 4 and 5
 */
assert(function_exists("curl_init"));

/**
 * Initally created by Nikolai Kordulla <kordulla@googlemail.com>.
 * 
 * To make authenticated requests to FriendFeed, which is required for
 * some feeds and to post messages, you must provide both
 * 
 * oauth_consumer_token and oauth_access_token. They should both be
 * dictionaries of the form array("key" => "...", "secret" => "..."). Learn
 * more about OAuth at http://friendfeed.com/api/oauth.
 * 
 * You can register your application to receive your FriendFeed OAuth
 * Consumer Key at http://friendfeed.com/api/register. To fetch request
 * tokens and access tokens, see fetch_oauth_request_token and
 * fetch_oauth_access_token below.
 */
class Friendfeed
{
    /**
     * The current version of the class
     */
    const VERSION = "0.1";
	/**
	 * the consumer
	 */
	protected $consumer = null;
	/**
	 * the consumer key
	 */
	protected $key = null;
	/**
	 * the consumer secret
	 */
	protected $secret = null;
	/**
	 * the access token
	 */
	protected $access_token = null;	
	/**
	 * the method
	 */
	const method = "GET";
	/**
	 * the auth base
	 */
	const _FRIENDFEED_OAUTH_BASE = "https://friendfeed.com/account/oauth";
	/**
	 * the api bas
	 */
	const _FRIENDFEED_API_BASE = "http://friendfeed-api.com/v2";
	
	/**
	 * the constructor
	 * 
	 * @param $key - the consumer key
	 * @param $secret - the consumer secret key
	 * @param $access_token - the access_token array('oauth_token' => , 'oauth_token_secret' =>)
	 */
	public function __construct($key, $secret, $access_token=null)
	{
		$this->key = $key;	
		$this->secret = $secret;
		$this->consumer = new OAuthConsumer($key, $secret, null);
		$this->_set_access_token($access_token);
	}
	
	/**
	 * Posts an entry
	 * 
	 * @param $body - message
	 * @param $link - a link
	 * @param $to - post to could be an array of all to addresses
	 * @param $args - additional args like e.g. image_url
	 * 
	 * @see http://friendfeed.com/api/documentation#write_entry.
	 * @_authenticated
	 */
	public function post_entry($body, $link=null, $to=null, $args=array())
	{
		$args['body'] = $body;
		if ($link != null)
			$args['link'] = $link;	
		if ($to != null)
			$args['to'] = $to;
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/entry';
		return json_decode($this->_url_open_access($url, $args));
	}
	
	/**
	 * Edit an entry
	 * 
	 * @param $id - id of the entry
	 * @param $body - message
	 * @param $link - a link
	 * @param $to - post to could be an array of all to addresses
	 * @param $args - additional args like e.g. image_url
	 * 
	 * @see http://friendfeed.com/api/documentation#write_entry.
	 * @_authenticated
	 */
	public function edit_entry($id, $body, $link=null, $to=null, $args=array())
	{
		$args['id'] = $id;
		return post_entry($body, $link, $to, $args); 
	}	
	
	/**
	 * Deletes an entry
	 * 
	 * @param id of the entry
	 * @param $args - additional arguments
	 * 
	 * @see http://friendfeed.com/api/documentation#write_entry.
	 * @_authenticated
	 */
	public function delete_entry($id, $args=array())
	{
		$args['id'] = $id;
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/entry/delete';
		return json_decode($this->_url_open_access($url, $args));
	}
	
	/**
	 * Posts the given comment to FriendFeed.
	 * 
	 * @param $entry 
	 * @param $body 
	 * @param $args - additional arguments
	 * 
	 * @see http://friendfeed.com/api/documentation#write_comment.
	 * @_authenticated
	 */
	public function post_comment($entry, $body, $args = array())
	{
		$args['entry'] = $entry;
		$args['body'] = $body;
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/comment';
		return json_decode($this->_url_open_access($url, $args));
	}	

	/**
	 * Edits the given properties on the comment with the given ID.
	 * 
	 * @param id of the commment
	 * @param $body 
	 * @param $args - additional arguments
	 * 
	 * @see http://friendfeed.com/api/documentation#write_comment.
	 * @_authenticated
	 */
	public function edit_comment($id, $body, $args = array())
	{
		$args['id'] = $id;
		$args['body'] = $body;
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/comment';
		return json_decode($this->_url_open_access($url, $args));
	}	
	
	/**
	 * Deletes the given comment from FriendFeed.
	 * 
	 * @param id of the entry
	 * @param $args - additional arguments
	 * 
	 * @see http://friendfeed.com/api/documentation#write_comment.
	 * @_authenticated
	 */
	public function delete_comment($id, $args = array())
	{
		$args['id'] = $id;
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/comment/delete';
		return json_decode($this->_url_open_access($url, $args, true));
	}		
	
	/**
	 * Posts the given like to FriendFeed.
	 * 
	 * @param $entry
	 * @param $args - additional arguments
	 * 
	 * @see http://friendfeed.com/api/documentation#write_like.
	 * @_authenticated
	 */
	public function post_like($entry, $args = array())
	{
		$args['entry'] = $entry;
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/like';
		return json_decode($this->_url_open_access($url, $args));
	}
	
	/**
	 * Deletes the given like from FriendFeed.
	 * 
	 * @param $entry
	 * @param $args - additional arguments
	 * 
	 * @see http://friendfeed.com/api/documentation#write_like.
	 * @_authenticated
	 */
	public function delete_like($entry, $args = array())
	{
		$args['entry'] = $entry;
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/like/delete';
		return json_decode($this->_url_open_access($url, $args));
	}	

	/**
	 * Hides the given entry from the authenticated user's FriendFeed.
	 * 
	 * @param $entry
	 * @param $args - additional arguments
	 * 
	 * @see http://friendfeed.com/api/documentation#write_hide.
	 * @_authenticated
	 */
	public function hide_entry($entry, $args = array())
	{
		$args['entry'] = $entry;
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/hide';
		return json_decode($this->_url_open_access($url, $args));
	}	
	
	/**
	 * Un-hides the given entry from the authenticated user's FriendFeed.
	 * 
	 * @param $entry
	 * @param $args - additional arguments
	 * 
	 * @see http://friendfeed.com/api/documentation#write_hide.
	 * @_authenticated
	 */
	public function unhide_entry($entry, $args = array())
	{	
		$args['unhide'] = 1;	
		return json_decode($this->hide_entry($entry, $args));
	}		

	/**
	 * Subscribes the authenticated user to the given feed.
	 * 
	 * @param $feed
	 * @param $args - additional arguments
	 * 
	 * @see http://friendfeed.com/api/documentation#write_subscribe.
	 * @_authenticated
	 */
	public function subscribe($feed, $args)
	{
		$args['feed'] = $feed;
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/subscribe';
		return json_decode($this->_url_open_access($url, $args));
	}

	/**
	 * Unsubscribes the authenticated user from the given feed.
	 * 
	 * @param $feed
	 * @param $args - additional arguments
	 * 
	 * @see http://friendfeed.com/api/documentation#write_subscribe.
	 * @_authenticated
	 */
	public function unsubscribe($feed, $args)
	{
		$args['feed'] = $feed;
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/unsubscribe';
		return json_decode($this->_url_open_access($url, $args));
	}
	
	/**
	 * Updates the name and/or description of the given feed.
	 * 
	 * @param $feed
	 * @param $name
	 * @param $description
	 * @param $args - additional arguments
	 * 
	 * @see http://friendfeed.com/api/documentation#write_feedinfo.
	 * @_authenticated
	 */
	public function edit_feed_info($feed=null, $name=null, $description=null, $args = array())
	{
		if ($feed != null)
			$args['feed'] = $feed;	
		if ($name != null)
			$args['name'] = $name;	
		if ($description != null)
			$args['description'] = $description;	
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/feedinfo';
		return json_decode($this->_url_open_access($url, $args));
	}

	/**
	 * Return the feeds displayed on the right hand side of 
	 * the FriendFeed website for the authenticated user.
	 * 
	 * @param $args additional arguments
	 * @return the response
	 * 
	 * @_authenticate
	 * @see http://friendfeed.com/api/documentation#read_feedlist.
	 */	
	public function fetch_feed_list($args = array())
	{
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/feedlist';
		return json_decode($this->_url_open_access($url, $args, true, "GET"));
	}
	
	/**
	 * Fetches the meta data about the feed with the given ID.
	 * 
	 * @param $feed_id the feed id
	 * @param $args additional arguments
	 * @return the response
	 * 
	 * @see http://friendfeed.com/api/documentation#read_feedinfo.
	 */
	public function fetch_feed_info($feed_id, $args = array())
	{
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/feedinfo/' . $feed_id;
		return json_decode($this->_url_open_access($url, $args, false, "GET"));
	}
	
	/**
	 * Fetches the entry with the given ID.
	 * 
	 * @param $entry_id 
	 * @param $args additional arguments
	 * @return the response
	 * 
	 * @see http://friendfeed.com/api/documentation#read_entry.
	 */
	public function fetch_entry($entry_id, $args = array())
	{
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/entry/' . $entry_id;
		return json_decode($this->_url_open_access($url, $args, false, "GET"));
	}	
	
	/**
	 * Fetches the comment with the given ID.
	 * 
	 * @param $comment_id comment id
	 * @param $args additional arguments
	 * @return the response
	 * 
	 * @see http://friendfeed.com/api/documentation#read_comment.
	 */
	public function fetch_comment($comment_id, $args = array())
	{
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/comment/' . $comment_id;
		return json_decode($this->_url_open_access($url, $args, false, "GET"));
	}	
	
	/**
	 * Fetches the entries that link to the given URL.
	 * 
	 * @param $url 
	 * @param $args additional arguments
	 * @return the response
	 * 
	 * @see http://friendfeed.com/api/documentation#read_url.
	 */
	public function fetch_url_feed($url, $args = array())
	{
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/url';
		$args['url'] = $url;
		return json_decode($this->_url_open_access($url, $args, false, "GET"));
	}	
	
	/**
	 * Fetches the entries with links from the given host.
	 * 
	 * @param $host 
	 * @param $args additional arguments
	 * @return the response
	 * 
	 * @see http://friendfeed.com/api/documentation#read_url.
	 */
	public function fetch_host_feed($host, $args = array())
	{
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/host';
		$args['host'] = $host;
		return json_decode($this->_url_open_access($url, $args, false, "GET"));
	}		
	
	/**
	 * Fetches the feed with the given ID, e.g., "bret" or "home"
	 * 
	 * @param $feed_id feed id
	 * @param $args additional arguments
	 * @return the response
	 * 
	 * @see http://friendfeed.com/api/documentation#read_feed.
	 */
	public function fetch_feed($feed_id, $args = array())
	{
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/feed/' . $feed_id;
		return json_decode($this->_url_open_access($url, $args, false, "GET"));
	}	
	
	/**
	 * Fetches the feed with the given ID, e.g., "bret" or "home"
	 * 
	 * @param $q 
	 * @param $args additional arguments
	 * @return the response
	 * 
	 * @see http://friendfeed.com/api/documentation#read_search.
	 */
	public function fetch_search_feed($q, $args = array())
	{
		$url = FriendFeed::_FRIENDFEED_API_BASE . '/search';
		$args['q'] = $q;
		return json_decode($this->_url_open_access($url, $args, false, "GET"));
	}

	/**
	 * Get the authentication url
	 * 
	 * @param $request_token - the request token
	 * @return the authentication_url
	 */
	public function get_oauth_authentication_url($request_token)
	{
		return FriendFeed::_FRIENDFEED_OAUTH_BASE . '/authenticate?oauth_token=' . $request_token['oauth_token'];		
	}
	
	/**
	 * Get the access token and set it for the class
	 * 
	 * @param $request_token - the request token
	 * @return the access token array('oauth_token' => , 'oauth_token_secret' => )
	 */	
	public function fetch_oauth_access_token($request_token)
	{
		$url = $this->_get_oauth_access_token_url($request_token);
		$request = $this->_url_open($url, Friendfeed::method);
		$access_token = $this->_oauth_parse_response($request);
		$this->_set_access_token($access_token);
		return $access_token;
	}
	
	
	/**
	 * fetch the auth request token
	 * 
	 * @return the token array('oauth_token' => , 'oauth_token_secret' => )
	 */	
	public function fetch_oauth_request_token()
	{
		$url = $this->_get_oauth_request_token_url();
		$request = $this->_url_open($url, Friendfeed::method);
		return $this->_oauth_parse_response($request);
	}	

	/**
	 * set the access token
	 * 
	 * @param $access_token - the access token
	 */
	protected function _set_access_token($access_token)
	{
		$this->access_token = $access_token;
	}		
	
	/**
	 * Get the auth request token url
	 */
	protected function _get_oauth_request_token_url()
	{
		$url = FriendFeed::_FRIENDFEED_OAUTH_BASE . '/request_token';
		$request = OAuthRequest::from_consumer_and_token($this->consumer, false, Friendfeed::method, $url, array());
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->consumer, false);
		return $request->to_url();
	}
	
	/**
	 * Get the auth request token url
	 */
	protected function _get_oauth_access_token_url($request_token)
	{
		$token = new OAuthToken($request_token['oauth_token'], $request_token['oauth_token_secret']);
		$url = FriendFeed::_FRIENDFEED_OAUTH_BASE . '/access_token';
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $token, Friendfeed::method, $url, array());
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->consumer, $token);
		return $request->to_url();
	}	
	
	/**
	 * just open the url and get back the content
	 */
	protected function _url_open($url, $method, $post_args = null )
	{
		$req = new FriendfeedRequest();
		return $req->get_response($url, $method, $post_args);
	}

	/**
	 * open url with args with access token and post request
	 * 
	 * @param $url - the url to open
	 * @param $args - the arguments
	 */	
	protected function _url_open_access($url, $args, $authenticate = true, $method = "POST")
	{
		$token = false;
		// add token if authenticate required or if not required but access_token set
		if ($authenticate || (!$authenticate && isset($this->access_token['oauth_token'])))		
			$token = new OAuthToken($this->access_token['oauth_token'], $this->access_token['oauth_token_secret']);
		$request = OAuthRequest::from_consumer_and_token($this->consumer,$token, $method, $url, $args);
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->consumer, $token);
		$args = array_merge($args, $request->get_parameters());
		return $this->_url_open($url, $method, $args);
	}
	
	
	
	/**
	 * parse the oauth response
	 * @param the request
	 * @return the parsed response as associated array
	 */
	protected function _oauth_parse_response($response)
	{
		$params = array();
		parse_str($response, $params);
		return $params;
	}
	
}

/**
 * Private Class FriendFeed the request
 */
class FriendfeedRequest
{
	
	/**
	 * Get the reponse
	 * @param the url
	 */
	public function get_response($url, $method  = "GET", $post_args = null)
	{
		// Basic setup
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_USERAGENT, 'Friendfeed/php');
		if ($method == "POST")
		{
			$data  = array();
			curl_setopt($curl, CURLOPT_POST,count($post_args));	
			$data_string = OAuthUtil::build_http_query($post_args);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
		}
		else if ($method == "GET" && $post_args != null)
		{
			$url .= "?" . OAuthUtil::build_http_query($post_args);	
		}
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($curl, CURLOPT_WRITEFUNCTION, array(&$this, '__responseWriteCallback'));
		curl_setopt($curl, CURLOPT_HEADERFUNCTION, array(&$this, '__responseHeaderCallback'));
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		
		// Execute, grab errors
		if (curl_exec($curl))
			$this->response->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		else
			$this->response->error = array(
				'code' => curl_errno($curl),
				'message' => curl_error($curl)
			);

		@curl_close($curl);
		
		if (isset($this->response->error))
			throw new Exception($this->response->error['code'] . ': ' . $this->response->error['message']);
		else if (!in_array($this->response->code, array(200, 204)))
			throw new Exception(json_decode($this->response->body)->{'errorCode'});		
		
		return $this->response->body;
	}

	/**
	* CURL write callback
	*
	* @param resource &$curl CURL resource
	* @param string &$data Data
	* @return integer
	*/
	private function __responseWriteCallback(&$curl, &$data) {
		$this->response->body .= $data;
		return strlen($data);
	}	
	
	
	/**
	* CURL header callback
	*
	* @param resource &$curl CURL resource
	* @param string &$data Data
	* @return integer
	*/
	private function __responseHeaderCallback(&$curl, &$data) {
		if (($strlen = strlen($data)) <= 2) return $strlen;
		if (substr($data, 0, 4) == 'HTTP')
			$this->response->code = (int)substr($data, 9, 3);
		else {
			list($header, $value) = explode(': ', trim($data), 2);
			if ($header == 'Last-Modified')
				$this->response->headers['time'] = strtotime($value);
			elseif ($header == 'Content-Length')
				$this->response->headers['size'] = (int)$value;
			elseif ($header == 'Content-Type')
				$this->response->headers['type'] = $value;
			elseif ($header == 'ETag')
				$this->response->headers['hash'] = $value{0} == '"' ? substr($value, 1, -1) : $value;
			elseif (preg_match('/^x-amz-meta-.*$/', $header))
				$this->response->headers[$header] = is_numeric($value) ? (int)$value : $value;
		}
		return $strlen;
	}		
}
?>