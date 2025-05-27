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
			if ($defaultMenu) redirect('index.php/'.$defaultMenu->menu_url);
			else {
				$data['menu'] = "error";
				$this->load->view('theme',$data);
			}
		}
		else $this->load->view('login');
	}
	
	// public function loginProcess()
	// {
		// if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		// $username = $this->input->post('username');
		// $password = $this->input->post('password');
		// $pass 	  = md5($password);
		// $result = $this->um->getby($username,$pass); 
		// if ($result->num_rows() > 0) 
		// { 
			// $row  	= $result->row(); 
			// $data 	= array('usergroup' => $row->ug_name, 'username' => $row->user_username,'user_id' => $row->user_id,'login' => TRUE,'language' => 'ina');
			// $this->session->set_userdata($data);
			// $data = array ("status"=>"success","url"=>url_admin());
		// } 
		// else $data = array ("status"=>"failed","info"=>"Username or password invalid");
		// echo json_encode($data);
	// }
	
	public function loginProcess()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$username = $this->input->post('username');
		$password = md5($this->input->post('password'));
		//print_r($_POST);
		$a = $this->um->getby($username,$password)->num_rows();
		if($a!=0) {
			$dt = $this->um->checkUser($username)->row();
			if($this->um->checkUser($username)->num_rows()!=0) {
				//print_r($data);
				if ($dt->member_type_id=='1') $session = array('usergroup' => 'superadmin', 'username' => $username,'user_id' => '1','login' => TRUE,'language' => 'ina');
				else  $session = array('usergroup' => 'member', 'username' => $username,'user_id' => '2','login' => TRUE,'language' => 'ina');
				$this->session->set_userdata($session);
				$data = array ("status"=>"success","url"=>url_admin());
				
			}
			else $data = array ("status"=>"failed","info"=>"Username or password invalid");
		} 
		else $data = array ("status"=>"failed","info"=>"Username or password invalid");
		echo json_encode($data);
	}
	
	
	function logout()
	{
		$this->session->sess_destroy();
		redirect('');	
	}
}
