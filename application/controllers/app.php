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
		$data = null; //resest data
		$get = $this->input->get("vID",TRUE);
		$data["vID"] = isset($get) ? $get : null;
		$data["userID"] = $this->session->userdata("userID");

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
	*	called from ajax call in editorSetup.js via GET method
	*/
	public function getAudioInfo(){
		$id = $this->input->get("vID", TRUE);
		echo $this->app_model->stripAudio($id);
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
		
		$dirname = "/media/storage/projects/livedescribe/public_html/res-www/uploads/user" . $userID; //the directory we want to write our descriptions in
		//check if the user directory has already been created
		if(!is_dir($dirname)){
		    mkdir ($dirname, 0766);
		}

		$dirname = $dirname . "/" . $vID;
		//check if the video directory has already been created
		if(!is_dir($dirname)){
		    mkdir ($dirname, 0766);
		}

		$destination = $dirname . "/description_" . $userID . "_" . $vID . "_" . $descID . ".wav";
		move_uploaded_file($temp_name, $destination);
		chmod($destination, 0766);

		echo $destination . " recorded on server.";
		/* end of JS Recoder save */
		
		

		/* send info to DB */
	}

	/**
	*	called from WAMI recorder (recordOperations.js) 
	*/
	public function recordAudioFLASH(){
		parse_str($_SERVER['QUERY_STRING'], $params);
		$vID = isset($params['id']) ? $params['id'] : null;
		$name = isset($params['name']) ? $params['name'] : 'temp.wav';
		$descID = isset($params['descID'])? $params['descID'] : null;
		$userID = isset($params['userID'])? $params['userID'] : null;
		
		$dirname = "/media/storage/projects/livedescribe/public_html/res-www/uploads/user" . $userID; //the directory we want to write our descriptions in
		//check if the user directory has already been created
		if(!is_dir($dirname)){
		    mkdir ($dirname, 0766);
		}

		$dirname = $dirname . "/" . $vID;
		//check if the video directory has already been created
		if(!is_dir($dirname)){
		    mkdir ($dirname, 0766);
		}

		chdir($dirname);
		
		$name = basename($name,'.wav') . '_' . $userID . "_" . $vID . "_" . $descID . ".wav";
		$content = file_get_contents('php://input');
		$fh = fopen($name ,'w') or die("can't open file");
		fwrite($fh,$content);
		fclose($fh);
		chmod($name, 0766);
	}


	/**
	*	delete the file associated with the description ID.
	*	Called from ajax call in editorOperations.js
	*/
	public function removeFile(){
		$vID    = $this->input->post("vID");
		$descID = $this->input->post("descID");
		$userID = $this->userID;
		$file   = "/media/storage/projects/livedescribe/public_html/res-www/uploads/user" . $userID . "/" . $vID . "/"
					. "description_" . $userID . "_" . $vID . "_" . $descID . ".wav";
		if(unlink($file)){
			echo $file;
		}
		else{
			echo "File was not removed: " . $file;
		}
	}



	/**
	*
	*/
	public function saveProject(){
		$json = $this->input->post("saveData");
		$data = json_decode($json);
		print_r($data);
	}

}

/* End of file app.php */
/* Location: ./application/controllers/app.php */