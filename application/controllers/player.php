<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player extends CI_Controller {


	public function __construct(){
		parent::__construct();
		$this->load->model("vfeed_model");
		$this->load->model("project_model");
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

			$uID = $this->input->get("uID", TRUE) ? $this->input->get("uID", TRUE) : $this->project_model->highestRating($vID);
			if($uID != NULL){
				//info of the related projects
				//print_r($this->project_model->getRelatedProjects($vID, $uID));
				$data['thumbnail'] = $this->vfeed_model->getThumbnail($vID);
				$data['related_projects'] = $this->project_model->getRelatedProjects($vID, $uID);
			}
			else{ //there are no related videos
				$data['thumbnail'] = NULL;
				$data['related_projects'] = NULL;
			}
			
			$this->load->view('player/player_main',$data);
			$this->load->view('footer');
		}
	}


	/**
	*	Get the descriptions and return the 
	*	info as a json object. Called from ajax
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
		
		$project_info = $this->project_model->getProjectData($vID, $uID);

		//print_r($project_info);

		if($project_info != NULL){//project exists
			$description_data = $this->project_model->getDescriptionData($vID, $project_info["user_id"]);
			$result = array("project_info" => $project_info, "description_data" => $description_data);
		}
		echo(json_encode($result));
		return;
	}


	/**
	*	update or insert rating for a user on a 
	*	project. This will only occur if the user is logged in.
	*	Called from rating.js
	*
	*/
	public function addRating(){
		$result          = null;
		$vID             = $this->input->post("vID");
		$project_user_id = $this->input->post("user_id");
		$rating          = $this->input->post("rating");

		if($this->session->userdata('logged_in')){
			$result = $this->project_model->rateProject($vID, $this->session->userdata('userID'), $rating, $project_user_id);
		}

		echo(json_encode($result));
		return;
	}
}

/* End of file player.php */
/* Location: ./application/controllers/player.php */
