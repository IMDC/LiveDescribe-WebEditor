<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('user_model');
	}

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index(){
		redirect(base_url(), 'refresh');
	}

	/**
	*	Loads the login view
	*/
	public function login($data = null){
		$this->load->view('header');
		$this->load->view('navigation');
		$this->load->view('login' , $data);
		$this->load->view('footer');
	}

	/**
	*	Loads the register view
	*/
	public function register($data = null){
		$this->load->view('header');
		$this->load->view('navigation');
		$this->load->view('register' , $data);
		$this->load->view('footer');
	}
	

	/**
	*	Uses the user_model to check the credentials
	*	supplied for login
	*/
	public function login_user(){
		$username = $this->input->post('uname');
		$password = md5($this->input->post('pword'));
		$result   = $this->user_model->login($username,$password);
  		
  		if($result){
  			redirect(base_url(), 'refresh');
  		}
  		else{
  			$data['error'] = 'The credentials you have supplied were invalid. Please try again.';
			$this->login($data);
  		}
	}

	/**
	*	Performs form validation and registers the 
	*	user if the validation passes
	*/
	public function register_user(){
		$this->load->library('form_validation');
		// field name, error message, validation rules
		$this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]|xss_clean');
		$this->form_validation->set_rules('user_name', 'User Name', 'trim|required|min_length[4]|xss_clean|callback_username_check' );
		$this->form_validation->set_rules('email_address', 'Your Email', 'trim|required|valid_email|callback_email_check');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('con_password', 'Password Confirmation', 'trim|required|matches[password]');
		$this->form_validation->set_rules('question', 'Secret Question', 'trim|required|min_length[8]|xss_clean');
		$this->form_validation->set_rules('answer', 'Answer to Secrect Question', 'trim|required|min_length[1]|xss_clean');

		if($this->form_validation->run() == FALSE){
			$this->register();
		}
		else{
			 $this->user_model->add_user();
			 $this->login();
		}
	}

	/**
	*	Checks uniquness of username
	*	@param $user: the username to be checked
	*/
	public function username_check($user){
		
		$condition = array('username'=> $user);
	    $query = $this->db->get_where('users', $condition);

        if( $query->num_rows() > 0){//user already exists
        	$this->form_validation->set_message('username_check', "The user name '{$user}' is already registered.");
			return FALSE;
        }
        return TRUE;	
	}

	/**
	*	Checks uniquness of email
	*	@param $email: the email to be checked
	*/
	public function email_check($email){

		$condition = array('email'=> $email);
	    $query = $this->db->get_where('users', $condition);

        if( $query->num_rows() > 0){//user already exists
        	$this->form_validation->set_message('email_check', "The email '{$email}' is already registered.");
			return FALSE;
        }
        return TRUE;	
	}


	public function logout(){
		$newdata = array(
			'user_id'   =>'',
			'user_name'  =>'',
			'user_email'     => '',
			'logged_in' => FALSE,
		);

		$this->session->unset_userdata($newdata );
		$this->session->sess_destroy();
		$this->login();
	}


	/**
	*	Used to login to DVX server
	* 	
	*	Note: not currently used.
	*/
	public function login_user_dvx(){
		$this->load->model('dvx_model');
		$username = $this->input->post('uname');
		$password = $this->input->post('pword');

		try{
			$ret = $this->dvx_model-> login($username, $password);
			$regx = "/^[A-Za-z0-9]+$/";
			$token = (string)$ret['token'];
			$userId = (int) $ret['userID'];
			$userName = (string) $ret['userName'];

			if(preg_match($regx, $token) != 1){
				$data['error'] = 'The credentials you have supplied were invalid. Please try again.';
				$this->login($data);
			}
			else{//login successfull
				
				//add session info
				$this->session->set_userdata('userID', $userID);
				$this->session->set_userdata('token', $token);
				$this->session->set_userdata('userName', $userName);

				redirect(base_url(), 'refresh');				
			}
		}
		catch(Exception $ex){
			$data['error'] = 'The credentials you have supplied were invalid. Please try again.';
			$this->login($data);
		}	
		
	}



}

/* End of file user.php */
/* Location: ./application/controllers/user.php */