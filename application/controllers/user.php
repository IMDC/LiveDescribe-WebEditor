<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

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
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */