<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		//$this->load->model('Setor_Komersial_Model', 'dm', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}

	public function index()
	{
		$data['view'] = 'backend/static/home';
		$data['title']	= 'Home';		
		$data['icon']	= 'fa-home';
		
		$this->load->view('backend/tpl', $data);
	}
}
