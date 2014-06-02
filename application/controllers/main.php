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
		
		$this->load->model('project_model');
		$this->load->model('vfeed_model');
	}

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index(){

		$this->load->view('header');
		$this->load->view('navigation');
		
		$described_feed = array();
		$standard_feed = array();
		$index_standard = 0;


		//get video feed from model
		$feed = $this->vfeed_model->getTopRated();

		//display the feed
		foreach ($feed as $key => $value){
			$thumbnails = $value->getVideoThumbnails();
			$standard_feed[$index_standard++] = array(
											'videoId' => $value->getVideoId(),
											'duration' => $value->getVideoDuration(),
											'title' => (string)$value->getVideoTitle(),
											'description' => (string)$value->getVideoDescription(),
											'thumbnail' => $thumbnails[0]['url']
										);
		}
		
		//get described projects
		$described_feed = $this->project_model->getHighestRatedProjects();
		//now need to add video thumbnails to each project
		foreach ($described_feed as $key => $value) {
			$tn = $this->vfeed_model->getThumbnail($value['vID']);
			$described_feed[$key]['thumbnail'] = $tn;	
		}

		$data['standard_feed'] = $standard_feed;
		$data['described_feed'] = $described_feed;
		$this->load->view('main/home_video_feed', $data);

		$this->load->view('footer');
	}

	/**
	*	Loads the view for the two video feeds and displays the 
	*	search results.
	*	Currently getting the searchBar field using POST
	*/
	public function videoFeed(){
		
		$keyword = $_POST['searchBar'];
		$described_feed = array();
		$standard_feed = array();
		$index_described = 0;
		$index_standard = 0;

		//load navigation bar
		$this->load->view('header');
		$this->load->view('navigation');

		//get video feed from model
		$feed = $this->vfeed_model->getFeed($keyword, 'viewCount');

		//display the feed
		foreach ($feed as $key => $value){

			//find described projects for each video in the standard feed
			$related = $this->project_model->getRelatedProjects( $value->getVideoId() );
			if( $related != NULL ){
				$thumbnails = $value->getVideoThumbnails();

				foreach($related as $k => $v){
					$described_feed[$index_described++] =  array(
													'vID'                 => $value->getVideoId(),
													'duration'            => $value->getVideoDuration(),
													'user_id'             => $v['user_id'],
													'project_name'        => $v['project_name'],
													'project_description' => $v['project_description'],
													'thumbnail'           => $thumbnails[0]['url'],
													'username'            => $v['username'],
													'rating'			  => $v['rating']
												);
				}
			}

			$thumbnails = $value->getVideoThumbnails();
			$standard_feed[$index_standard++] = array(
											'videoId' => $value->getVideoId(),
											'duration' => $value->getVideoDuration(),
											'title' => (string)$value->getVideoTitle(),
											'description' => (string)$value->getVideoDescription(),
											'thumbnail' => $thumbnails[0]['url']
										);
		}

		$data['standard_feed'] = $standard_feed;
		$data['described_feed'] = $described_feed;
		$this->load->view('main/video_feed', $data);
		$this->load->view('footer');
	}
}

/* End of file main.php */
/* Location: ./application/controllers/main.php */