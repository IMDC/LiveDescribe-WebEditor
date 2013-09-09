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
		
	}
}

/* End of file main.php */
/* Location: ./application/controllers/main.php */