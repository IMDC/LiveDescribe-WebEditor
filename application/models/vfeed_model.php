<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class VFeed_Model extends CI_Model {

	public function __construct(){
		// Call the Model constructor
		parent::__construct();
		$this->load->library('zend');
		$this->zend->load('Zend/Loader');

		//load yt related libraries
		Zend_Loader::loadClass("Zend_Gdata");
		Zend_Loader::loadClass("Zend_Gdata_YouTube");
		Zend_Loader::loadClass("Zend_Gdata_YouTube_VideoFeed");
		Zend_Loader::loadClass("Zend_Gdata_YouTube_VideoEntry");
		Zend_Loader::loadClass("Zend_Gdata_YouTube_VideoQuery");
		Zend_Loader::loadClass("Zend_Gdata_YouTube_Extension_MediaGroup");
	}

	/**
	*	Gets the video feed using the Zend Gdata
	*	@param $keyword : the input the user is searching for
	*	@param $order : how we want to display the feed (mostViews, top rated etc.)
	*	@return $feed
	*/
	public function getFeed($keyword, $order){
		$yt = new Zend_Gdata_YouTube();
		$q = $yt->newVideoQuery();
		$q->setQuery($keyword);
		$q->orderBy = $order;
		$feed = $yt->getVideoFeed($q);

		return $feed;
	}

	/**
	*	Checks if the given id is valid or not
	*	@param $id : the video id
	*	@return $valid : true if id is valid, false otherwise
	*/
	public function checkValidID($id){
		$valid = FALSE;
		$feed  = null;
		
		try{
			$yt    = new Zend_Gdata_YouTube();
			$feed  = $yt->getVideoEntry($id);
			$valid = ($feed != null) ? TRUE : FALSE;
		}
		catch(Exception $e){}

		return $valid;
	}
 }

?>

