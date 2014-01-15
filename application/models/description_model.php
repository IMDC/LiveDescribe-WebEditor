<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Description_Model extends CI_Model {

	public function __construct(){
		// Call the Model constructor
		parent::__construct();
		$this->load->database();
	}

	/**
	*	Gets the project data with the highest rating
	*/
	public function getDefaultDescription($vID){
		$result = NULL;
		
		$this->db->select("*");
		$this->db->where("video_id", $vID);
		$this->db->order_by("rating", "desc");
		$this->db->limit(1);

		$query = $this->db->get("projects");

		if($query->num_rows() > 0){
			$row = $query->result_array();
			$result = array(
					"user_id"             => $row[0]["user_id"],
					"project_name"        => $row[0]["project_name"],
					"project_description" => $row[0]["project_description"],
					"rating"              => $row[0]["rating"],
					"times_rated"         => $row[0]["times_rated"]
				);
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
	
 }

?>