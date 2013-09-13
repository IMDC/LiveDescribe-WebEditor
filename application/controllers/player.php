<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index()
	{
		$this->load->view('header');
		//$this->load->view('');
		$this->load->view('footer');
	}
}

/* End of file player.php */
/* Location: ./application/controllers/player.php */