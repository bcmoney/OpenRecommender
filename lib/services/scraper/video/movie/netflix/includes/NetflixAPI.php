<?php
/**
 * This is the main loader object for netflix API
 *
 * For more detailed function information view documentation.html
 *
 * License: LGPL
 *
 * @copyright  2011 Aziz Hussain
 * @version    1.0
 * @link       http://azizsaleh.com
 * @email      azizsaleh@gmail.com
 */ 

class NetflixAPI
{	
	/* Variables */
	public $configs;
	public $request;
	public $storage;
	
	public $output = 'json';
	
	/* Call types */
	public $open_calls;
	public $signed_calls;
	public $protected_calls;
	
	/* Construct object */
	public function __construct($api_file = NULL, $output = NULL)
	{
		// Load configurations
		$this->configs 			= new Configuration($api_file);
		
		// Load HTTP Request object
		$this->request 			= new Request($this->configs);
				
		// Database handler
		$this->storage 			= new storageObject($this->configs);
		
		// Assign calls handlers
		$this->open_calls		= new nonAuthenticatedCall($this->request, $this->configs);
		$this->signed_calls		= new signedCall($this->request, $this->configs);
		$this->protected_calls	= new protectedCall($this->request, $this->configs, $this->storage);
		
		if (!empty($output)) {
			$this->output = $output;	
		}
	}
	
	/*******************NON AUTHENTICATED CALLS***************************/
	
	/*
	Catalog Titles Autocomplete
	
	@param		string		term
	
	@return		array
	*/
	public function getCatalogTitlesAutoComplete($term)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/catalog/titles/autocomplete';
		
		$this->open_calls->makeCall($api_url, 
								array('term' 		=> $term,
									  'output'		=> $this->output));
		
		return $this->_returnData();
	}
	
	/*************************SIGNED CALLS******************************/
	
	/*
	Catalog Titles
	
	@param		string		term
	@param		int			start_index = 0
	@param		int			max_result = 25
	
	@return		array
	*/
	public function getCatalogTitles($term, $start_index = 0, $max_result = 25)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/catalog/titles';
		
		$this->signed_calls->makeCall($api_url, 
								array('term' 		=> $term,
									  'start_index' => $start_index,
									  'max_result' 	=> $max_result,
									  'output'		=> $this->output));
		
		return $this->_returnData();
	}
	
	/*
	Catalog Titles Index
	
	@param		string		include_amg = null
	@param		string		include_tms = null
	
	@return		array
	*/
	public function getCatalogTitlesIndex($include_amg = '', $include_tms = '')
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/catalog/titles/index';
		
		$this->signed_calls->makeCall($api_url, 
								array('include_amg'	=> $include_amg,
									  'include_tms' => $include_tms,
									  'output'		=> 'xml'));
		
		return $this->_returnData();
	}
	
	/*
	Get Title Details
	
	@param		string		movie_id
	@param		string		type = movies (movies, series, programs, or discs)
	@param		int			season_id = null
	
	@return		array
	*/
	public function getTitleDetails($movie_id, $type = 'movies', $season_id = NULL)
	{
		// Set API URL & Vars
		$api_url = "http://api.netflix.com/catalog/titles/$type/$movie_id";
		
		if (!empty($season_id)) {
			$api_url .= "/seasons/$season_id";	
		}

		$this->signed_calls->makeCall($api_url, 
								array('output'	=> $this->output));
		
		return $this->_returnData();				
	}
	
	/*
	Titles Similars
	
	@param		string		title_id
	@param		int			start_index = 0
	@param		int			max_result = 25
	
	@return		array
	*/
	public function getTitlesSimilars($title_id, $start_index = 0, $max_result = 25)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/catalog/titles/' . $title_id . '/similars';

		$this->signed_calls->makeCall($api_url, 
								array('start_index' => $start_index,
									  'max_result' 	=> $max_result));
		
		return $this->_returnData();
	}

	/*
	Searching for People
	
	@param		string		term
	@param		int			start_index = 0
	@param		int			max_result = 25
	
	@return		array
	*/
	public function getSearchForPeople($term, $start_index = 0, $max_result = 25)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/catalog/people';
		
		$this->signed_calls->makeCall($api_url, 
								array('term'		=> $term,
									  'start_index' => $start_index,
									  'max_result' 	=> $max_result));
		
		return $this->_returnData();
	}
	
	/*
	Person Details
	
	@param		string		person_id
	
	@return		array
	*/
	public function getPersonDetails($person_id)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/catalog/people/' . $person_id;
		
		$this->signed_calls->makeCall($api_url, 
								array('output'		=> $this->output));
		
		return $this->_returnData();
	}
					
	/*************************PROTECTED CALLS******************************/
	
	/*
	Users, Feeds and Title States
	
	@return		array
	*/
	public function getUsersInfo()
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/users/%user_id%/';
		
		$this->protected_calls->makeCall($api_url, 
								array('output'		=> $this->output));

		return $this->_returnData();	
	}
	
	/*
	Current User (reflection)
	
	@return		array
	*/
	public function getCurrentUser()
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/users/current';
		
		$this->protected_calls->makeCall($api_url, 
								array('output'		=> $this->output));
		
		return $this->_returnData();	
	}
	
	/*
	Get user specific list
	
	@param		string		list_Type [queues, rental_history, recommendations, title_states, ratings, reviews, at_home, feeds]
	
	@return		array
	*/
	public function getUserSpecificList($list_Type)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/users/%user_id%/' . $list_Type;
		
		$this->protected_calls->makeCall($api_url, 
								array('output'		=> $this->output));
		
		return $this->_returnData();	
	}
	
	/*
	Feeds
	
	@param		string		feed_url
	@param		string		feed_token
	Example: 
	
	@return		array
	*/
	public function getFeeds($feed_url, $feed_string, $sort = 'date_added')
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/users/%user_id%/' . $feed_url;
		
		$this->protected_calls->makeCall($api_url, 
								array('sort'		=> $sort,
									  'feed_token'		=> $feed_token,
									  'output'		=> $this->output));
		
		return $this->_returnData();	
	}
	
	/*
	Title States
	
	@param		array		title_refs
	
	@return		array
	*/
	public function getTitleStates($title_refs)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/users/' . $title_refs . '/title_states';
		
		$this->protected_calls->makeCall($api_url, 
								array('title_refs'	=> $title_refs,
									  'output'		=> $this->output));
		
		return $this->_returnData();	
	}
	
	/*
	Managing Queues

	@param		timestamp	updated_min
	
	@param		string		search_type 		[disc, instant]	
	@param		string		sort = alphabetical	[queue_sequence, date_added, alphabetical]
	@param		string		search_method 		[available, saved]
	@param		int			entry_id = null
	@param		int			start_index = 0
	@param		int			max_results = 25
	
	@return		array
	*/
	public function getQueues($updated_min, $search_type = 'disc', $sort = 'alphabetical',$search_method = NULL, 
								  $entry_id = NULL,$start_index = 0, $max_results = 25)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/users/%user_id%/queues/' . $search_type;
		
		if (!empty($search_method)) {
			$api_url .= '/' . $search_method;	
		}
		
		if (!empty($entry_id)) {
			$api_url .= '/' . $entry_id;	
		}
						
		$this->protected_calls->makeCall($api_url, 
								array('sort'		=> $sort,
									  'start_index'	=> $start_index,
									  'max_results' => $max_results,
									  'updated_min' => $updated_min,
									  'output'		=> $this->output));
		
		return $this->_returnData();
	}
	
	/*
	Updating a Queue
	
	@param		string		title_ref
	@param		string		format			[DVD, Blue-ray]
	@param		int			position
	@param		string		etag
	
	@param		string		search_type 	[disc, instant]
	@param		string		search_method 	[available, saved]
	@param		int			entry_id = null
			
	@return		array
	*/
	public function updatingAQueue($title_ref, $format, $position, $etag, $search_type = 'disc', 
								   $search_method = NULL, $entry_id = NULL)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/users/%user_id%/queues/' . $search_type;
		
		if (!empty($search_method)) {
			$api_url .= '/' . $search_method;	
		}
		
		if (!empty($entry_id)) {
			$api_url .= '/' . $entry_id;	
		}
						
		$this->protected_calls->makeCall($api_url, 
								array('title_ref'	=> $title_ref,
									  'format'		=> $format,
									  'position' 	=> $position,
									  'etag' 		=> $etag,
									  'output'		=> $this->output));
		
		return $this->_returnData();	
	}
	
	/*
	Tracking Discs
	
	@param		timestamp	updated_min	
	@param		int			start_index = 0
	@param		int			max_results = 25
	
	@return		array
	*/
	public function getTrackingDiscs($updated_min, $start_index = 0, $max_results = 25)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/users/%user_id%/at_home';
		
		$this->protected_calls->makeCall($api_url, 
								array('start_index'	=> $start_index,
									  'max_results' => $max_results,
									  'updated_min' => $updated_min,
									  'output'		=> $this->output));
		
		return $this->_returnData();
	}
	
	/*
	Managing Rental History

	@param		timestamp	updated_min
	
	@param		string		history_type = NULL	[shipped, returned, watched]
	@param		int			start_index
	@param		int			max_results	
	
	@return		array	
	*/
	
	public function managingRentalHistory($updated_min, $history_type = NULL, $start_index = 0, $max_results = 25)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/users/%user_id%/rental_history';
		
		if (!empty($history_type)) {
			$api_url .= '/' . $history_type;	
		}

		$this->protected_calls->makeCall($api_url, 
								array('start_index'	=> $start_index,
									  'max_results'	=> $max_results,
									  'updated_min'	=> $updated_min,
									  'output'		=> $this->output));
		
		return $this->_returnData();	
	}
	
	/*
	Get User Title Ratings
	
	@param		string		rating_type = NULL	[actual, predicted]
	@param		string		title_refs
	@param		int			rating_id = NULL
	
	@return		array	
	*/
	
	public function getUserRatings($rating_type, $title_refs, $rating_id = NULL)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/users/%user_id%/ratings/title';
		
		if (!empty($rating_type)) {
			$api_url .= '/' . $rating_type;	
		}
		
		if (!empty($rating_id)) {
			$api_url .= '/' . $rating_id;	
		}

		$this->protected_calls->makeCall($api_url, 
								array('title_refs'	=> $title_refs,
									  'output'		=> $this->output));
		
		return $this->_returnData();
	}
	
	/*
	Create User Title Rating
	
	@param		string		title_ref
	@param		string		rating [1,2,3,4,5,not_interested,no_opinion]
	
	@return		array	
	*/
	
	public function createUserRating($title_ref, $rating)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/users/%user_id%/ratings/title/actual';
		
		$this->protected_calls->makeCall($api_url, 
								array('title_refs'	=> $title_refs,
									  'rating'		=> $rating,
									  'output'		=> $this->output));
		
		return $this->_returnData();
	}
	
	/*
	Update User Title Rating
	
	@param		int			rating_id
	@param		string		rating [1,2,3,4,5,not_interested,no_opinion]
	
	@return		array	
	*/
	
	public function updateUserRating($rating_id, $rating)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/users/%user_id%/ratings/title/' . $rating_id;
		
		$this->protected_calls->makeCall($api_url, 
								array('title_refs'	=> $title_refs,
									  'rating'		=> $rating,
									  'output'		=> $this->output));
		
		return $this->_returnData();
	}
	
	/*
	Retrieving Reviews
	
	@param		int			title_refs
	@param		timestamp	updated_min
	@param		int			start_index = 0
	@param		int			max_results = 25

	
	@return		array
	*/
	public function retrieveReviews($title_refs, $updated_min, $start_index = 0, $max_results = 25)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/users/%user_id%/reviews';
		
		$this->protected_calls->makeCall($api_url, 
								array('title_refs'	=> $title_refs,
									  'start_index' => $start_index,
									  'max_results'	=> $max_results,
									  'updated_min'	=> $updated_min,
									  'output'		=> $this->output));
		
		return $this->_returnData();
	}
	
	/*
	Retrieving Recommendations
	
	@param		int		start_index = 0
	@param		int		max_results = 25
	
	@return		array
	*/
	public function retrieveRecommendations($start_index = 0, $max_results = 25)
	{
		// Set API URL & Vars
		$api_url = 'http://api.netflix.com/users/%user_id%/recommendations';
		
		$this->protected_calls->makeCall($api_url, 
								array('start_index' => $start_index,
									  'max_results'	=> $max_results,
									  'output'		=> $this->output));
		
		return $this->_returnData();
	}
	
	/************************ Helper Functions ***************************/
	
	/*
	This function will return the formatted response
	
	@return		array
	*/
	
	private function _returnData()
	{
		return $this->request->getResponse();	
	}
	
}
