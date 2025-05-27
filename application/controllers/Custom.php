<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom extends CI_Controller {
	
	function __construct() {
        parent::__construct(); 
		//if(!$this->session->userdata('login')) redirect('backend', 'refresh');
		//$this->load->model('CustomModel', 'cm', TRUE);
    }
	
	public function usergroup()
	{	
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		
		$data = array('usergroup' => $this->input->post('usergroup'));
		$this->session->set_userdata($data); 
	}
	
	public function language()
	{	
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		
		$data = array('language' => ($this->input->post('language')=='ID'?'ina':'eng'));
		$this->session->set_userdata($data);
		
		echo "success";
	}
	
	public function dynamic_menu()
	{	
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		echo dynamic_menu($this->input->post('url'));
	}
	
}
