<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Y404 extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		//$this->load->model('Posts_Model', 'dm', TRUE);
		//$this->load->model('Ms_Category_Model', '', TRUE);
	}

	public function index()
	{
		$this->output->set_status_header('404'); 
		
		$data['view']	= 'frontend/y404/index';
		
		$this->load->view('frontend/tpl', $data);
	}
}
