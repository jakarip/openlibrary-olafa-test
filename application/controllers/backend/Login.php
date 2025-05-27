<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	
	function __construct() {
        parent::__construct();  
		$this->load->model('UserModel', 'um', TRUE);
		$this->load->model('CustomModel', 'cm', TRUE);
    }
	
	public function index()
	{
		if($this->session->userdata('login')) {
			$defaultMenu = $this->cm->getUserGroupDefaultMenu()->row();
			if ($defaultMenu) redirect($defaultMenu->menu_url);
			else {
				$data['menu'] = "backend/error";
				$this->load->view('backend/theme',$data);
			}
		}
		else $this->load->view('backend/login');
	}
	
	public function loginProcess()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$pass 	  = md5($password);
		$result = $this->um->getby($username,$pass); 
		if ($result->num_rows() > 0) 
		{ 
			$row  	= $result->row(); 
			$data 	= array('usergroup' => $row->ug_name,'usergroupid' => $row->ug_id, 'username' => $row->user_username,'user_id' => $row->user_id,'login' => TRUE,'language' => 'ina');
			$this->session->set_userdata($data);
			$data = array ("status"=>"success","url"=>url_admin());
		} 
		else $data = array ("status"=>"failed","info"=>"Username or password invalid");
		echo json_encode($data);
	}
	
	
	function logout()
	{
		$this->session->sess_destroy();
		redirect('backend');	
	}
}
