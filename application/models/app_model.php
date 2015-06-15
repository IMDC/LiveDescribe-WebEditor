<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class App_Model extends CI_Model {


	public function __construct(){
		// Call the Model constructor
		parent::__construct();
		$this->load->library('zend');
		$this->zend->load('Zend/Loader');

		require_once ('google-api-php-client/src/Google_Client.php');
                require_once ('google-api-php-client/src/contrib/Google_YouTubeService.php');

		$this->load->model('audio_model'); //for reading audio data
	}

	public function getDuration($video_id){

		$DEVELOPER_KEY = 'AIzaSyBBR3xd3H9inw1UqdbywBvKvBOTESMHBm4';

                $client = new Google_Client();
                $client->setDeveloperKey($DEVELOPER_KEY);

                $youtube = new Google_YoutubeService($client);

		$videoResponse = $youtube->videos->listVideos($video_id, 'snippet,contentDetails');
		$videoResult = $videoResponse['items'][0];
		
		$totalSeconds=0;
		$duration = new DateTime('@0'); // Unix epoca
                $duration->add(new DateInterval($videoResult['contentDetails']['duration']));
                $totalSeconds += ($duration->format('d')-1)*24*60*60;
                $totalSeconds += $duration->format('H')*60*60;
                $totalSeconds += $duration->format('i')*60;
                $totalSeconds += $duration->format('s');
	
		return $totalSeconds;
	}


	/**
	 *	Uses the Audio_model to extract the audio data.
	 *	The audio data is saved in a json file so that once the 
	 *	audio data has been read from the wav file, it does not need to happen 
	 *	when another user tries to access the same data
	 *	
	 *	@param $video_id : String
	 *	@return $audioData : array
	 */
	public function stripAudio($video_id){
		$audio_data = null;
		$arg        = escapeshellarg($video_id);
		$json_data  = "/media/storage/projects/livedescribe/public_html/res-www/yt/downloads/$video_id.json";

		if(file_exists($json_data)){ //read in json data
			$audio_data = file_get_contents($json_data);
		}
		else{
			$cmd = "/media/storage/projects/livedescribe/public_html/res-www/yt/youtube-dl -o \"%(id)s.%(ext)s\" $arg --extract-audio";

			if(getcwd() != "/media/storage/projects/livedescribe/public_html/res-www/yt/downloads/"){
				chdir("/media/storage/projects/livedescribe/public_html/res-www/yt/downloads/");
			}

			$ret = shell_exec($cmd);

			if( $ret == null ) return;

			$inFile     = $video_id . '.m4a';
			$outFile    = $video_id . '.wav';
			$ffmpeg_cmd ="/usr/local/bin/ffmpeg -i " . escapeshellarg($inFile) . " " . escapeshellarg($outFile);
			$val        = shell_exec($ffmpeg_cmd);

			unlink("/media/storage/projects/livedescribe/public_html/res-www/yt/downloads/$inFile"); //remove the video file

			$audioFile = "/media/storage/projects/livedescribe/public_html/res-www/yt/downloads/$outFile";

			$this->audio_model->initialise($audioFile);
			$response = $this->audio_model->readData();

			file_put_contents($json_data, json_encode($response), LOCK_EX);

			$audio_data = json_encode($response); //send back a json object that will be used in the javascript file
		}

		return $audio_data;	
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
						'extended'  => $rows->extended
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
			$this->db->where($condition);
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
					'extended'  => $obj->extended,
					'video_id'  => $vID
				     );

			$condition = array("user_id" => $uID , "video_id" => $vID, "desc_id" => $obj->id);
			$query = $this->db->get_where("descriptions", $condition);

			if($query->num_rows() > 0){ //need to update record
				$this->db->where($condition);
				$this->db->update('descriptions',$data);
			}
			else{ //insert new record
				$this->db->insert('descriptions',$data);
			}
		}
	}


}


