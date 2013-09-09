<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index()
	{
		$this->load->view('header');
		$this->load->view('navigation');
		$this->load->view('footer');
	}

	public function test(){
		echo ("this is a test: " . (isset($_POST['searchBar']) ? $_POST['searchBar'] : 'nothing set'));
	}
}

/* End of file main.php */
/* Location: ./application/controllers/main.php */