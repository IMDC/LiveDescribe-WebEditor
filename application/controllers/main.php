<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	public function __construct(){
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
	 * Index Page for this controller.
	 *
	 */
	public function index(){
		$this->load->view('header');
		$this->load->view('navigation');
		$this->load->view('footer');
	}

	/**
	*	Loads the view for the two video feeds and displays the 
	*	search results.
	*	Currently getting the searchBar field using POST
	*/
	public function videoFeed(){
		$this->load->model('description_model');
		$this->load->model('vfeed_model');
		
		$keyword = $_POST['searchBar'];
		$described_feed = array();
		$index = 0;
		
		//load navigation bar
		$this->load->view('header');
		$this->load->view('navigation');

		$this->load->view('main/vfeed_top');

		//get video feed from model
		$feed = $this->vfeed_model->getFeed($keyword, 'viewCount');

		//display the feed
		foreach ($feed as $key => $value){

			//find described projects for each video in the standard feed
			$related = $this->description_model->getRelatedProjects( $value->getVideoId() );
			if( $related != NULL ){
				$thumbnails = $value->getVideoThumbnails();

				foreach($related as $k => $v){
					$described_feed[$index++] =  array(
													'vID'                 => $value->getVideoId(),
													'duration'            => $value->getVideoDuration(),
													'user_id'             => $v['user_id'],
													'project_name'        => $v['project_name'],
													'project_description' => $v['project_description'],
													'rating'              => $v['rating'],
													'times_rated'         => $v['times_rated'],
													'thumbnail'           => $thumbnails[0]['url'],
													'username'            => $v['username'],
												);
				}
			}

			$data = null;
 			$data['videoId']     = $value->getVideoId();
		    $data['duration']    = $value->getVideoDuration();
			$data['title']       = (string)$value->getVideoTitle();
		    $data['description'] = (string)$value->getVideoDescription();
		    $thumbnails          = $value->getVideoThumbnails();
			$data['thumbnail']   = $thumbnails[0]['url'];
 
			$this->load->view('main/feedResult', $data);
		}

		$this->load->view('main/vfeed_mid');
		
		$data['described_feed'] = $described_feed;
		$this->load->view('main/descriptionResult', $data);
		
		$this->load->view('main/vfeed_bottom');
		$this->load->view('footer');
	}
}

/* End of file main.php */
/* Location: ./application/controllers/main.php */