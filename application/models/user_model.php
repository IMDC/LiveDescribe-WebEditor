<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class User_Model extends CI_Model {

	public function __construct(){
		// Call the Model constructor
		parent::__construct();
		$this->load->database();
	}

	/**
	*	login as IMDC user
	*/
	public function login($user,$password){
		$condition = array("username" => $user , "password" => $password);
		$query=$this->db->get_where("users", $condition);

		if($query->num_rows()>0){
			foreach($query->result() as $rows){
				//add all data to session
				$newdata = array(
				  'userID'  => $rows->id,
				  'userName'  => $rows->username,
				  'user_email'    => $rows->email,
				  'logged_in'  => TRUE,
				);
			}
			$this->session->set_userdata($newdata);
			return true;
		}
		return false;
	}
 
 	/**
 	*
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
 }

?>