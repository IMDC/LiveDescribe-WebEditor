<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player extends CI_Controller {


	public function __construct(){
		parent::__construct();
		$this->load->model("vfeed_model");
		$this->load->database();
	}

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index(){
		$data = null; //resest data
		$get  = $this->input->get("vID",TRUE);
		$data["vID"] = $get;

		if(!$this->vfeed_model->checkvalidID($get) || $get == ""){ //invalid YT ID
			redirect(base_url(), 'refresh');
		}
		else{
			$data['title'] = $this->vfeed_model->getTitle($get);
			$this->load->view('player/player_header',$data);
			$this->load->view('navigation');
			$this->load->view('player/player_main',$data);
			$this->load->view('footer');
		}
	}
}

/* End of file player.php */
/* Location: ./application/controllers/player.php */