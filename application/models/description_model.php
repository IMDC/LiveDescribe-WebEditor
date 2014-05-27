<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Description_Model extends CI_Model {

	public function __construct(){
		// Call the Model constructor
		parent::__construct();
		$this->load->database();
	}


	/**
	*	Returns the user id with the higest
	*	rated project
	*	
	*	@param string $vID
	*	@return string $uID 
	*/
	public function highestRating($vID){
		$uID = NULL;

		//get the project_id and the total likes associated with that project
		$this->db->select(array("project_id", "SUM(like_dislike) AS like_dislike"));
		$this->db->where(array("video_id" => $vID));
		$this->db->group_by("project_id");
		$this->db->order_by("like_dislike", "desc");
		$this->db->limit(1);
		$project_likes = $this->db->get('ratings');

		if($project_likes -> num_rows() > 0){
			$project_id_highest_rating = $project_likes->row()->project_id;
			$query = $this->db->get_where('projects', array('id' => $project_id_highest_rating));

			if($query->num_rows() > 0){
				$row = $query->result_array();
				$uID = $row[0]["user_id"];
			}
		}
		return $uID;
	}

	/**
	*	Gets the project data with the highest rating if
	*	no user id is given. Otherwise the project data
	*	corresponding to the parameters is obtained 
	*
	*	@param string $vID
	*	@param string $uID optional param
	*	@return array $result
	*/
	public function getProjectData($vID, $uID = NULL){
		$result = NULL;
		$rating = 0;

		//get the project_id and the total likes associated with that project
		$this->db->select(array("project_id", "SUM(like_dislike) AS like_dislike"));
		$this->db->where(array("video_id" => $vID));
		$this->db->group_by("project_id");
		$this->db->order_by("like_dislike", "desc");
		$project_likes = $this->db->get('ratings');

		if($uID != NULL){
			$condition = array('video_id' => $vID , 'user_id' => $uID);
			$query = $this->db->get_where("projects", $condition);

			if($query->num_rows() > 0){ //this means associated user and video exist
				foreach ($project_likes->result() as $likes) {
					if($likes->project_id == $query->row()->id){
						$rating = $likes->like_dislike;
						break;
					}
				}
			}
			else{
				return NULL;
			}
		}
		else{ //get highest rated project
			if( $project_likes->num_rows() > 0 ){ //get top element
				$rating = $project_likes->row()->like_dislike;
				$query = $this->db->get_where('projects', 
							array('id' => $project_likes->row()->project_id)
						);
			}
			else{//no project data exists
				return NULL; 
			}
		}
		
		if($query->num_rows() > 0){
			$row = $query->result_array();
			$result = array(
					"user_id"             => $row[0]["user_id"],
					"project_name"        => $row[0]["project_name"],
					"project_description" => $row[0]["project_description"],
					"rating"              => $rating
				);

			/* get the username */
			$this->db->select("username");
			$this->db->where("id", $row[0]["user_id"]);
			$name = $this->db->get("users")->result_array();
			$result["username"] = $name[0]["username"];
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
		$this->db->order_by("start");
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
	*	Finds the projects that have been described with
	*	the given video id. 
	*
	*	@param string $vID
	*	@param string $uID
	*	@return array $result
	*/
	public function getRelatedProjects($vID, $uID = NULL){
		$result = NULL;
		
		//get the project_id and the total likes associated with that project
		$this->db->select(array("project_id", "SUM(like_dislike) AS like_dislike"));
		$this->db->where(array("video_id" => $vID));
		$this->db->group_by("project_id");
		$this->db->order_by("like_dislike", "desc");
		$project_likes = $this->db->get('ratings');


		if($project_likes->num_rows() > 0){
			$index = 0;
			foreach($project_likes->result() as $rows){
				//project should only contain one such project since id is the primary, unique key
				 $project = $this->db->get_where('projects', array('id' => $rows->project_id))->row();
				//$result[$index] = $project;

				if($uID == $project->user_id){ //dont list the entry
					continue;
				}
				else{//add all data to $result

					$newData = array(
							'user_id'             => $project->user_id,
							'project_name'        => $project->project_name,
							'project_description' => $project->project_description,
							'rating'              => $rows->like_dislike
						);

					/* get the username */
					$this->db->select("username");
					$this->db->where("id", $project->user_id);
					$name = $this->db->get("users")->row();
					$newData["username"] = $name->username;

					$result[$index] = $newData;
				 	$index++;
				}
			}
		}
		return $result;
	}
	
 }

?>