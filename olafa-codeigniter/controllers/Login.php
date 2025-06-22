<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	
	function __construct() {
        parent::__construct();  
		$this->load->model('Usermodel', 'um', TRUE);
		$this->load->model('CustomModel', 'cm', TRUE);  
    } 
	
	public function index()
	{  
		if (!empty($username)){ 
			$username = $this->input->post('username');
			$password = md5($this->input->post('password')); 
			$a = $this->um->getby($username,$password)->num_rows();
			if($a!=0) {
				$dt = $this->um->checkUser($username)->row();
				if($this->um->checkUser($username)->num_rows()!=0) { 
					if ($dt->member_type_id=='1') $session = array('usergroup' => 'superadmin', 'username' => $username,'user_id' => '1','login' => TRUE,'language' => 'ina');
					else  $session = array('usergroup' => 'member', 'username' => $username,'user_id' => '2','login' => TRUE,'language' => 'ina');
					$this->session->set_userdata($session);
					
					$defaultMenu = $this->cm->getUserGroupDefaultMenu()->row();
					if ($defaultMenu) redirect('index.php/'.$defaultMenu->menu_url);
					else {
						$data['menu'] = "error";
						$this->load->view('theme',$data);
					}
				}
				else $this->load->view('login');
			} 
				else $this->load->view('login');
		}
		else {
			if($this->session->userdata('login')) {
				if ($this->session->userdata('usergroup')!='public'){
					$defaultMenu = $this->cm->getUserGroupDefaultMenu()->row();
					if ($defaultMenu) redirect('index.php/'.$defaultMenu->menu_url);
					else {
						$data['menu'] = "error";
						$this->load->view('theme',$data);
					}
				}
				else $this->load->view('login');
			}
			else $this->load->view('login');
		}
	} 
	
	public function loginProcess()
	{ 
		$username = strtolower($this->input->post('username'));
		$password = $this->input->post('password');
			
		$sso 	  = SSO($username,$password);  
		if($sso=='false'){
			$dt = $this->um->getbymember($username,md5($password))->row();
			if($dt){ 
				if ($dt->member_type_id=='1') { 
					$session = array('usergroup' => 'superadmin', 'username' => $username,'user_id' => '1','login' => true,'language' => 'ina');
				} else { 
					$session = array('usergroup' => 'member', 'username' => $username,'user_id' => '2','login' => true,'language' => 'ina');
				}
				$this->session->set_userdata($session); 
				
				$defaultmenu = $this->cm->getusergroupdefaultmenu()->row();
				if ($defaultmenu) redirect('index.php/'.$defaultmenu->menu_url);
				else {
					$data['menu'] = "error";
					$this->load->view('theme',$data);
				} 
			}
			else {
				$this->session->set_flashdata('error', 'username or password invalid'); 
				redirect('index.php/login');
			}
		}
		else {  
			$dt = $this->um->checkuser($username)->row();
			if($this->um->checkuser($username)->num_rows()!=0) {
				if ($dt->member_type_id=='1') { 
					$session = array('usergroup' => 'superadmin', 'username' => $username,'user_id' => '1','login' => true,'language' => 'ina');
				} else { 
					$session = array('usergroup' => 'member', 'username' => $username,'user_id' => '2','login' => true,'language' => 'ina');
				}
				$this->session->set_userdata($session); 
		 
				$defaultmenu = $this->cm->getusergroupdefaultmenu()->row();
				if ($defaultmenu) redirect('index.php/'.$defaultmenu->menu_url);
				else {
					$data['menu'] = "error";
					$this->load->view('theme',$data);
				} 
			}
			else {
				$this->session->set_flashdata('error', 'username or password invalid'); 
				redirect('index.php/login');
			}
		}
	} 
	
	// public function loginProcess()
	// { 
		// $username = $this->input->post('username');
		// $password = md5($this->input->post('password')); 
		// $a = $this->um->getby($username,$password)->num_rows();
		// if($a!=0) {
			// $dt = $this->um->checkUser($username)->row();
			// if($this->um->checkUser($username)->num_rows()!=0) {
				// if ($dt->member_type_id=='1') {
					// echo "1";
					// $session = array('usergroup' => 'superadmin', 'username' => $username,'user_id' => '1','login' => TRUE,'language' => 'ina');
				// }else  {
					// echo "2";
					// $session = array('usergroup' => 'member', 'username' => $username,'user_id' => '2','login' => TRUE,'language' => 'ina');
				// }
				// $this->session->set_userdata($session);
				
		
				// // $my_session_variable = $this->session->all_userdata();
				// // print_r($my_session_variable);
				// $defaultMenu = $this->cm->getUserGroupDefaultMenu()->row();
					// if ($defaultMenu) redirect('index.php/'.$defaultMenu->menu_url);
					// else {
						// $data['menu'] = "error";
						// $this->load->view('theme',$data);
					// }
				
			// }
			// else {
				// $this->session->set_flashdata('error', 'Username or password invalid'); 
				// redirect('index.php/login');
			// }
		// } 
		// else {
			// $this->session->set_flashdata('error', 'Username or password invalid'); 
			// redirect('index.php/login');
		// }
	// }
	
	
	function logout()
	{
		$this->session->sess_destroy();
		redirect('');	
	}
}
?>