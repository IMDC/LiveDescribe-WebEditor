<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('session'); //start CI seesion
	}

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index()
	{
		
	}

	public function login(){
		$this->load->view('header');
		$this->load->view('navigation');
		$this->load->view('login');
		$this->load->view('footer');
	}

	public function register(){
		$this->load->view('header');
		$this->load->view('navigation');
		$this->load->view('register');
		$this->load->view('footer');
	}

	public function login_user(){
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
				$ret['status'] = 'failure';
				$ret['value'] = $invalidLogin;
				echo $invalidLogin;
			}
			else{
				
				$_SESSION['userId'] = $userId;
				$_SESSION['token'] = $token;
				$_SESSION['userName'] = $userName;
				echo json_encode($ret);
				
			}
		}
		catch(Exception $ex){
			$ret['status'] = 'failure';
			$ret['value'] = $connectionError;
			echo json_encode($ret);
		}	
		
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */