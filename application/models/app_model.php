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
	*	
	*/
	public function save($saveData, $uID){
		/*
			stdClass Object
			(
			    [formData] => [{"name":"projName","value":"testPorj"},{"name":"projDesc","value":"helohellohelllo"},{"name":"vID","value":"T7HXli3cBOw"}]
			    [descriptionData] => [{"filename":"description_7_T7HXli3cBOw_b4fb260dab2.wav","startTime":0,"endTime":5.539,"textDescription":"  ","id":"b4fb260dab2"},{"filename":"description_7_T7HXli3cBOw_66188901ef0.wav","startTime":7.409,"endTime":11.008,"textDescription":"","id":"66188901ef0"}]
			)
			 
		*/

		$formData = json_decode($saveData->formData);
		$this->saveProjectData($formData, $uID);

		$descriptionData = json_decode($saveData->descriptionData);

	}

	/**
	*	
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
			return $data;
		}
	}

	/**
	*
	*/
	private function saveDescriptionData($descriptionData, $uID){

	}
	
 }


