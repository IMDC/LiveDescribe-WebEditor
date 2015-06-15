<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends CI_Controller {

	private $userID;

	public function __construct(){
		parent::__construct();
		$this->load->model("vfeed_model");
		$this->load->model("app_model");

		if(!($this->session->userdata("logged_in"))){ //dont load the app
			redirect(base_url(), 'refresh');
		}
		else{
			$this->load->database();
			$this->userID = $this->session->userdata("userID");
		}
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
		$data           = null; //resest data
		$get            = $this->input->get("vID",TRUE);
		$data["vID"]    = isset($get) ? $get : null;
		$data["userID"] = $this->session->userdata("userID");

		//print_r($this->vfeed_model->checkvalidID($get));
		if(!$this->vfeed_model->checkValidID($get) || $get == ""){ //invalid YT ID
			redirect(base_url(), 'refresh');
		}
		else{

			if($this->app_model->checkProject($data["vID"], $data["userID"])){
				$formData = $this->app_model->getFormData($data["vID"], $data["userID"]);
				$data["project_name"] = $formData["project_name"];
				$data["project_description"] = $formData["project_description"];
			}

			$this->load->view('app/app_header', $data);
			$this->load->view('navigation');
			$this->load->view('app/app_main', $data);
			$this->load->view('footer');
		}
	}

	/**
	 *	called from ajax call in editorSetup.js via GET method
	 */
	public function getDuration(){
		$id = $this->input->get("vID",TRUE);

		echo $this->app_model->getDuration($id);
		return;
	}

	/**
	 *	called from ajax call in editorSetup.js via GET method
	 */
	public function getAudioInfo(){
		$id = $this->input->get("vID", TRUE);

		echo $this->app_model->stripAudio($id);
		return;
	}

	/**
	 *	called from an ajax call in ---- 
	 */
	public function recordAudio(){
		$vID    = $this->input->post("id");
		$descID = $this->input->post("descID");
		$userID = $this->input->post("userID");

		/* will recieve the .wav audio file Recorded from the JS audio recorder. */
		$temp_name = isset($_FILES['data']['tmp_name']) ? $_FILES['data']['tmp_name'] : null;

		$dirname = "/media/storage/projects/livedescribe/public_html/audioUploads/user" . $userID; //the directory we want to write our descriptions in
		//check if the user directory has already been created
		if(!is_dir($dirname)){
			mkdir ($dirname, 0773);
		}

		$dirname = $dirname . "/" . $vID;
		//check if the video directory has already been created
		if(!is_dir($dirname)){
			mkdir ($dirname, 0773);
		}

		$destination = $dirname . "/description_" . $userID . "_" . $vID . "_" . $descID . ".wav";
		move_uploaded_file($temp_name, $destination);
		chmod($destination, 0773);

		echo $destination . " recorded on server.";
		return;
		/* end of JS Recoder save */
	}



	/**
	 *	delete the file associated with the description ID.
	 *	Called from ajax call in "editorOperations.js"
	 */
	public function removeFile(){
		$vID    = $this->input->post("vID");
		$descID = $this->input->post("descID");
		$userID = $this->session->userdata("userID");
		$this->app_model->removeFile($vID, $descID, $userID);

		return;
	}



	/**
	 *	Saves the project, along with the recorded descriptions
	 *	in the db. Gets called from an AJAX call in "editorOperations.js"
	 */
	public function saveProject(){
		$json = $this->input->post("saveData");
		$data = json_decode($json);
		$result = $this->app_model->save($data, $this->userID);

		print_r($data);
		return;
	}

	/**
	 *	Gets description data from app_model and echo's
	 *	json representation of the data, if the data exists.
	 *	Called from AJAX call in "editorSetup.js"
	 */
	public function getDescriptionData(){
		$vID = $this->input->post("vID");
		$result = $this->app_model->getDescriptionData($vID, $this->userID);

		echo(json_encode($result));
		return;
	}

}

/* End of file app.php */
/* Location: ./application/controllers/app.php */
