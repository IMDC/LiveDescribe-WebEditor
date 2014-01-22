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
		$this->load->view('_navigation');
		$this->load->view('footer');
	}

	/**
	*	Loads the view for the two video feeds and displays the 
	*	search results.
	*	Currently getting the searchBar field using POST
	*/
	public function videoFeed(){
		//load navigation bar
		$this->load->view('header');
		$this->load->view('navigation');

		$this->load->model('vfeed_model');
		$keyword = $_POST['searchBar'];

		$this->load->view('main/vfeed_top');
		//get described video feed --> future

		$this->load->view('main/vfeed_mid');
		//get standard video feed

		//get video feed from model
		$feed = $this->vfeed_model->getFeed($keyword, 'viewCount');

		// //display the feed, maybe load multiple views?
		foreach ($feed as $key => $value){
			$data = null;
 			$data['videoId']     = $value->getVideoId();
		    $data['duration']    = $value->getVideoDuration();
			$data['title']       = (string)$value->getVideoTitle();
		    $data['description'] = (string)$value->getVideoDescription();
		    $thumbnails          = $value->getVideoThumbnails();
			$data['thumbnail']   = $thumbnails[0]['url'];
 
			$this->load->view('main/feedResult', $data);
		}
		$this->load->view('main/vfeed_bottom');
		$this->load->view('footer');
	}
}

/* End of file main.php */
/* Location: ./application/controllers/main.php */