<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class VFeed_Model extends CI_Model {

	public function __construct(){
		// Call the Model constructor
		parent::__construct();
		$this->load->library('zend');
		$this->zend->load('Zend/Loader');

		require_once ('google-api-php-client/src/Google_Client.php');
		require_once ('google-api-php-client/src/contrib/Google_YouTubeService.php');
	}

	public function loadYoutube(){
		/* Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
		   Google APIs Console <http://code.google.com/apis/console#access>
		   Please ensure that you have enabled the YouTube Data API for your project. */
		$DEVELOPER_KEY = 'AIzaSyBBR3xd3H9inw1UqdbywBvKvBOTESMHBm4';

		$client = new Google_Client();
		$client->setDeveloperKey($DEVELOPER_KEY);

		$youtube = new Google_YoutubeService($client);
		return $youtube;	
	}
	/**
	 *	Gets the video feed using the Zend Gdata
	 *	@param $keyword : the input the user is searching for
	 *	@param $order : how we want to display the feed (mostViews, top rated etc.)
	 *	@return $feed
	 */
	public function getFeed($keyword, $order){

		$youtube = $this->loadYoutube();
		//get video feed from model
		try {
			$searchResponse = $youtube->search->listSearch('id,snippet', array(
						'q' => $keyword,
						'order' => $order,
						));
			$videoResults = array();
			//Merge video ids
			foreach ($searchResponse['items'] as $searchResult) {
				if(isset($searchResult['id']['videoId']))
					array_push($videoResults, $searchResult['id']['videoId']);
			}
			$videoIds = join(',', $videoResults);

			//Call the videos.list method to retrieve location details for each video.
			$videosResponse = $youtube->videos->listVideos('','snippet, contentDetails', array(
						'id' => $videoIds,
						));

			return $videosResponse;


		} catch (google_serviceexception $e) {
			$htmlbody .= sprintf('<p>a service error occurred: <code>%s</code></p>',
					htmlspecialchars($e->getmessage()));
		} catch (google_exception $e) {
			$htmlbody .= sprintf('<p>an client error occurred: <code>%s</code></p>',
					htmlspecialchars($e->getmessage()));
		}
	}

	/**
	 *	Gets the video feed of top rated 
	 *	videos from 'today'
	 */
	public function getTopRated(){
		$youtube = $this->loadYoutube();
		//use yesterdays date to search for recent top rated videos
		$date = new DateTime('yesterday');
		//$date = new DateTime('7 days ago');
		try {
			$searchResponse = $youtube->search->listSearch('id,snippet', array(
						'order' => 'viewCount',
						'publishedAfter' => $date->format(DateTime::RFC3339),
						'regionCode' => 'ca',
						'type' => 'video',
						'maxResults' => '25',
						));
			$videoResults = array();
			// Merge video ids
			foreach ($searchResponse['items'] as $searchResult) {
				array_push($videoResults, $searchResult['id']['videoId']);
			}
			$videoIds = join(',', $videoResults);

			// Call the videos.list method to retrieve location details for each video.
			$videosResponse = $youtube->videos->listVideos('','snippet, contentDetails', array(
						'id' => $videoIds,
						));

			return $videosResponse;

		} catch (google_serviceexception $e) {
			$htmlbody .= sprintf('<p>a service error occurred: <code>%s</code></p>',
					htmlspecialchars($e->getmessage()));
		} catch (google_exception $e) {
			$htmlbody .= sprintf('<p>an client error occurred: <code>%s</code></p>',
					htmlspecialchars($e->getmessage()));
		}

	}

	public function getVideoDuration($videoResult){
		$totalSeconds =0;

		$duration = new DateTime('@0'); // Unix epoca
		$duration->add(new DateInterval($videoResult['contentDetails']['duration']));
		$totalSeconds += ($duration->format('d')-1)*24*60*60;
		$totalSeconds += $duration->format('H')*60*60;
		$totalSeconds += $duration->format('i')*60;
		$totalSeconds += $duration->format('s');

		return $totalSeconds;
	}


	/**
	 *	Checks if the given id is valid or not
	 *	@param $id : the video id
	 *	@return $valid : true if id is valid, false otherwise
	 */
	public function checkValidID($id){
		$youtube = $this->loadYoutube();
		$videoResponse = $youtube->videos->listVideos($id, 'snippet');

		if(isset($videoResponse))
			return TRUE;
		else
			return FALSE;
	}

	/**
	 *	Gets the title of the given video id
	 *	
	 *	@param $id : video id
	 *	@return $title : videos title
	 */
	public function getTitle($id){
		$title = null;

		$youtube = $this->loadYoutube();
		$videoResponse = $youtube->videos->listVideos($id, 'snippet');
		$videoResult = $videoResponse['items'][0];
		$title =$videoResult['snippet']['title'];

		return $title;
	}

	/**
	 *	Gets the thumbnail of the given video id
	 *	
	 *	@param string $id : video id
	 *	@return string $thumbnail 
	 */
	public function getThumbnail($id){
		$thumbnail = null;

		$youtube = $this->loadYoutube();
		$videoResponse = $youtube->videos->listVideos($id, 'snippet');
		$videoResult = $videoResponse['items'][0];
		$thumbnail =$videoResult['snippet']['thumbnails']['default']['url'];

		return $thumbnail;
	}

}

?>

