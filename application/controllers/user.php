<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct(){
		parent::__construct();
		//$this->load->library('session'); //start CI seesion
	}

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index()
	{
		
	}

	public function login($data = null){
		$this->load->view('header');
		$this->load->view('navigation');
		$this->load->view('login' , $data);
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
				$data['error'] = 'The credentials you have supplied were invalid. Please try again.';
				$this->login($data);
			}
			else{//login successfull
				
				print_r($ret);
				//add session info here
				//redirect(base_url(), 'refresh');
				
			}
		}
		catch(Exception $ex){
			$ret['error'] = $ex;
			$this->login($data);
		}	
		
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */