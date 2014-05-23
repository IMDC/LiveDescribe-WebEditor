<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class User_Model extends CI_Model {

	public function __construct(){
		// Call the Model constructor
		parent::__construct();
		$this->load->database();
	}

	/**
	*	login as IMDC user
	*	@param $user
	*	@param $password
	*	@return returns true is the users exists in the db, false otherwise
	*/
	public function login($user,$password){
		$condition = array("username" => $user , "password" => $password);
		$query=$this->db->get_where("users", $condition);

		if($query->num_rows() > 0){
			foreach($query->result() as $rows){
				//add all data to session
				$newdata = array(
					'userID'     => $rows->id,
					'userName'   => $rows->username,
					'user_email' => $rows->email,
					'logged_in'  => TRUE,
				);
			}
			$this->session->set_userdata($newdata);
			return true;
		}
		return false;
	}

 
 	/**
 	*	adds a new user into the "users" 
 	*	table in the IMDC database
 	*/
	public function add_user(){

		$data=array(
			'name'     =>$this->input->post('name'),
			'username' =>$this->input->post('user_name'),
			'email'    =>$this->input->post('email_address'),
			'password' =>md5($this->input->post('password')),
			'question' =>$this->input->post('question'),
			'answer'   =>$this->input->post('answer'),
		);
		$this->db->insert('users',$data);
	}

	/**
	*	Gets the previous projects that the user has
	*	created
	*
	*	@param string : $uID
	*	@return array
	*/
	public function getUserProjects($uID){
		$this->load->model('vfeed_model'); //for getting thumbnails
		$result = NULL;
		$condition = array(
						'user_id' => $uID
					);
		$this->db->order_by("date_modified", "desc");
		$query = $this->db->get_where('projects', $condition);

		if($query->num_rows() > 0){
			$index = 0;
			foreach($query->result() as $rows){
				//add all data to session
				$newdata = array(
					'userID'      => $uID,
					'projectID'   => $rows->id,
					'videoID'     => $rows->video_id,
					'thumbnail'   => $this->vfeed_model->getThumbnail($rows->video_id),
					'title'       => $rows->project_name,
					'description' => $rows->project_description,
					'rating'      => $rows->rating,
					'date'        => $rows->date_modified,
				);
				$result[$index] = $newdata;
				$index++;
			}
		}
		return $result;
	}

	/**
	*	Remove the project with the given project ID
	*
	*	@param string : $projectID
	*	@return $query : boolean
	*/
	public function deleteProject($projectID){
		$condition = array(
						'id' => $projectID
					);
		$query = $this->db->delete('projects', $condition);
		return $query;
	}
 }

?>