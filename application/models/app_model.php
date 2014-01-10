<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class App_Model extends CI_Model {

	private $yt; //youTube object

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

		$this->yt = new Zend_Gdata_YouTube();
		$this->yt->setMajorProtocolVersion(2);

		$this->load->model('audio_model'); //for reading audio data
	}

	public function getDuration($video_id){
		$q = $this->yt->getVideoEntry($video_id);
		$duration = (int) $q->getVideoDuration();
		return $duration;
	}


	public function stripAudio($video_id){
		$arg = escapeshellarg($video_id);
		$cmd = "/media/storage/projects/livedescribe/public_html/res-www/yt/youtube-dl " . $arg .
		" --restrict-filenames";
		
		if(getcwd() != "/media/storage/projects/livedescribe/public_html/res-www/yt/downloads/"){
			chdir("/media/storage/projects/livedescribe/public_html/res-www/yt/downloads/");
		}

		$ret = shell_exec($cmd);

		if( $ret != null){
			//parse the shell output to obtain the video filename
			$title = explode("\n", $ret);
			$title = explode(" " , $title[4]);
			$title = count($title) > 3 ? $title[1] : $title[2];
			
			// $title = shell_exec("ls | grep " . $arg);
			// $title = str_replace("\n", "", $title);
			//echo("title: " . $title . "\n");

			$outFile = $video_id . '.wav';
			$ffmpeg_cmd ="/usr/local/bin/ffmpeg -y -i $title -f wav $outFile";
			$arg2 = escapeshellcmd($ffmpeg_cmd);
			$val = shell_exec($arg2);
			
		}
		else{
			echo("failure");
		}

		unlink("./$title"); //remove the video file
		
		$audioFile = "/media/storage/projects/livedescribe/public_html/res-www/yt/downloads/" . $video_id . ".wav";
		
		$this->audio_model->initialise($audioFile);
		$response = $this->audio_model->readData();
		unlink($audioFile);

		return(json_encode($response)); //send back a json object that will be used in the javascript file		
	}

	/**
	*	Saves both project data and description data
	*   by calling saveProjectData() and saveDescriptionData()
	*
	*	@param $saveData : data to be inserted / updated to DB
	*	@param $uID : user id 
	*/
	public function save($saveData, $uID){
		/*  $saveData example:
			stdClass Object
			(
			    [formData] => [{"name":"projName","value":"testPorj"},{"name":"projDesc","value":"helohellohelllo"},{"name":"vID","value":"T7HXli3cBOw"}]
			    [descriptionData] => [{"filename":"description_7_T7HXli3cBOw_b4fb260dab2.wav","startTime":0,"endTime":5.539,"textDescription":"  ","id":"b4fb260dab2"}]
			)	 
		*/

		$formData = json_decode($saveData->formData);
		$this->saveProjectData($formData, $uID);
		
		if(property_exists($saveData, "descriptionData")){ //desc. data exists
			$descriptionData = json_decode($saveData->descriptionData);
			$this->saveDescriptionData($descriptionData, $uID, $formData[2]->value);
		}
	}

	/**
	*	Checks if the video has previously been saved by the user
	*
	*	@param $vID : video id
	*	@param $userID : user id
	*/
	public function checkProject($vID, $userID){
		$exists    = FALSE;
		$condition = array("user_id" => $userID , "video_id" => $vID);
		$query     = $this->db->get_where("projects", $condition);
		
		if($query->num_rows() > 0){
			$exists = TRUE;
		}
		return $exists;
	}

	/**
	*	Gets the form data from the db, the project name and
	*	the description the user has previously entered
	*
	*	@param $vID : video id
	*	@param $userID : user id
	*	@return $result : array(project_name, project_description)
	*/
	public function getFormData($vID, $userID){
		$condition = array("user_id" => $userID , "video_id" => $vID);
		$query     = $this->db->get_where("projects", $condition);
		$result    = NULL;
		
		if($query->num_rows() > 0){
			foreach($query->result() as $rows){
				//add all data to session
				$result = array(
					'project_name'        => $rows->project_name,
					'project_description' => $rows->project_description,
				);
			}
		}
		return $result;
	}

	/**
	*	Gets the description data from the db
	*
	*	@param $vID : video id
	*	@param $userID : user id
	*	@return $result : array of description data
	*/
	public function getDescriptionData($vID, $userID){
		$condition = array("user_id" => $userID , "video_id" => $vID);
		$query     = $this->db->get_where("descriptions", $condition);
		$result    = NULL;
		
		if($query->num_rows() > 0){
			$index = 0;
			foreach($query->result() as $rows){
				//add all data to $result
				$newData = array(
				  	'desc_id'   => $rows->desc_id,
					'start' 	=> $rows->start,
					'end'   	=> $rows->end,
					'filename'  => $rows->filename,
					'desc_text' => $rows->desc_text,
				);
				$result[$index] = $newData;
				$index++;
			}
		}
		return $result;
	}

	/**
	*	Removes the file on the server and from the DB
	*
	*	@param $vID : video id
	*	@param $descID : unique id of the description
	*	@param $userID : id of the user
	*/
	public function removeFile($vID, $descID, $userID){
		$file   = "/media/storage/projects/livedescribe/public_html/res-www/uploads/user" . $userID . "/" . $vID . "/"
					. "description_" . $userID . "_" . $vID . "_" . $descID . ".wav";

		if(unlink($file)){
			$condition = array("user_id" => $userID , "desc_id" => $descID);
			$query = $this->db->delete("descriptions", $condition);
			echo "Removed: " . $file;
		}
		else{
			echo "File was not removed: " . $file;
		}
	}


	/**
	*	Saves project data the user supplies, inserts the 
	*   data into the database if the project is new to the 
	*   user, or updates the data otherwise
	*
	*	@param $formData : (project name, project description, video id)
	*	@param $uID : user id
	*/
	private function saveProjectData($formData, $uID){

		$projName = $formData[0]->value;
		$projDesc = $formData[1]->value;
		$vID      = $formData[2]->value;
		
		$data = array(
				'user_id'             => $uID,
				'video_id'            => $vID,
				'project_name'        => $projName,
				'project_description' => $projDesc,
				);

		$condition = array("user_id" => $uID , "video_id" => $vID);
		$query = $this->db->get_where("projects", $condition);
		
		if($query->num_rows() > 0){ //need to update record
			$this->db->update('projects',$data);
		}
		else{ //insert new record
			$this->db->insert('projects',$data);
		}
	}

	/**
	*	Saves description data, inserts the 
	*   data into the database if the description doesn't exist
	*   or updates the data otherwise
	*
	*	@param $descriptionData
	*	@param $uID : user id
	*/
	private function saveDescriptionData($descriptionData, $uID, $vID){

		foreach($descriptionData as $obj){
			$data = array(
					'user_id'   => $uID,
					'desc_id'   => $obj->id,
					'start' 	=> $obj->startTime,
					'end'   	=> $obj->endTime,
					'filename'  => $obj->filename,
					'desc_text' => $obj->textDescription,
					'video_id'  => $vID
					);

			$condition = array("desc_id" => $obj->id);//array("user_id" => $uID , "video_id" => $vID, "desc_id" => $obj->id);
			$query = $this->db->get_where("descriptions", $condition);
		
			if($query->num_rows() > 0){ //need to update record
				$this->db->update('descriptions',$data);
			}
			else{ //insert new record
				$this->db->insert('descriptions',$data);
			}
		}
	}

	
 }


