<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index(){
		//dont do anything if no method is selected...for now
		redirect(base_url(), 'refresh');
	}

	/**
	*	About section
	*/
	public function about(){
		$this->load->view('header');
		$this->load->view('navigation');
		$this->load->view('pages/about');
		$this->load->view('footer');
	}
}