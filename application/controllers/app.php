<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model("vfeed_model");
		$this->load->model("app_model");
		// if(!($this->session->userdata("logged_in"))){ //dont load the app
		// 	redirect(base_url(), 'refresh');
		// }
		// else{
		// 	$this->load->database();
		// }
	}

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index(){
		//dont do anything if no method is selected...for now
		redirect(base_url(), 'refresh');
	}

	/**
	*	Loads the editor view of the app
	*/
	public function editor(){
		$data = null; //resest data
		$get = $this->input->get("vID",TRUE);
		$data["vID"] = isset($get)? $get : null;

		//print_r($this->vfeed_model->checkvalidID($get));
		if(!$this->vfeed_model->checkvalidID($get) || $get == ""){ //invalid YT ID
			redirect(base_url(), 'refresh');
		}
		else{
			$this->load->view('app/app_header', $data);
			$this->load->view('navigation');
			$this->load->view('app/app_main');
			$this->load->view('footer');
		}
	}

	/**
	*	called from ajax call in editorSetup.js via GET method
	*/
	public function getDuration(){
		$id = $this->input->get("vID",TRUE);
		echo $this->app_model->getDuration($id);
	}

	/**
	*	called from ajax call in editorSetup.js via POST method
	*/
	public function getAudioInfo(){
		$id = $this->input->get("vID", TRUE);
		echo $this->app_model->stripAudio($id);
	}

}

/* End of file app.php */
/* Location: ./application/controllers/app.php */