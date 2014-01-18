<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player extends CI_Controller {


	public function __construct(){
		parent::__construct();
		$this->load->model("vfeed_model");
		$this->load->model("description_model");
		$this->load->database();
	}

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index(){
		$data = null; //resest data
		$vID  = $this->input->get("vID",TRUE);
		$data["vID"] = $vID;

		if(!$this->vfeed_model->checkvalidID($vID) || $vID == ""){ //invalid YT ID
			redirect(base_url(), 'refresh');
		}
		else{
			$data['title'] = $this->vfeed_model->getTitle($vID);

			$this->load->view('player/player_header',$data);
			$this->load->view('navigation');

			$uID = $this->description_model->highestRating($vID);
			if($uID != NULL){
				//info or the related projects
				$data['related_projects'] = $this->description_model->getRelatedProjects($vID, $uID);
			}
			
			$this->load->view('player/player_main',$data);
			$this->load->view('footer');
		}
	}


	/**
	*	Get the description with highest rating (default)
	*   and return the info as a json object. Called from ajax
	*	call from init.js
	*
	*	@param $vID : string
	*	@return json-object
	*/
	public function getDescriptions(){
		$result = NULL;
		$project_info = NULL;
		$vID = $this->input->post("vID");
		$uID = $this->input->post("uID");
		
		$project_info = $this->description_model->getProjectData($vID, $uID);

		if($project_info != NULL){//project exists
			$descriptino_data = $this->description_model->getDescriptionData($vID, $project_info["user_id"]);
			$result = array("project_info" => $project_info, "descriptino_data" => $descriptino_data);
		}
		echo(json_encode($result));
		return;
	}
}

/* End of file player.php */
/* Location: ./application/controllers/player.php */