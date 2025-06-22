<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Usermodel', 'um', TRUE);
		$this->load->model('CustomModel', 'cm', TRUE);  
	}
	
	public function index()
	{
		// $data['logo']		= $this->Settings_Model->getvalue('image_logo');
		// $data['background'] = $this->Settings_Model->getvalue('image_background');
		// print_r($data);
		$this->load->view('login');
	}
	
	// public function exe()
	// {
	// 	$username = $this->input->post('username');
	// 	$password = $this->input->post('password');
	// 	$pass 	  = '$SMBB$'.substr(sha1(md5(md5($password))), 0, 50);
		
	// 	$db = $this->dm->getlogin($username, $pass)->row();
	// 	$dbs = $this->rm->getlogin($username, $pass)->row();
		
	// 	if (!empty($db)) 
	// 	{ 
	// 		$data = array('login' => TRUE, 'usergroup' => 'admin', 'info_login' => $db);
			
	// 		$this->session->set_userdata($data);
			
	// 		redirect('');
	// 	} 
	// 	else if (!empty($dbs)) 
	// 	{ 
	// 		$data = array('referral_login' => TRUE,'usergroup' => 'referral', 'info_login' => $dbs);
			
	// 		$this->session->set_userdata($data); 
	// 		redirect('itpanel/referral');
	// 	} 
	// 	else 
	// 	{ 
	// 		$this->session->set_flashdata('errlog', true);
	// 		redirect(''.'/login');
	// 	}
	// }

	
	
	public function exe()
	{ 
		$username = strtolower($this->input->post('username'));
		$password = $this->input->post('password');
			
		$sso 	  = SSO($username,$password);   
		 
		$data['allow_usergroups']['submission'] = array('dosen','pegawai','superadmin'); 
		$this->session->set_userdata($data);  
 
		if($sso=='false'){
			echo "bb";
			$dt = $this->um->getbymember($username,md5($password))->row();
			if($dt){ 
				if ($dt->member_type_id=='1') { 
					$data = array('user_login_apps' => TRUE, 'usergroup' => 'superadmin', 'username' => $username,'user_id' =>  $dt->member_id,'fullname'=> $dt->master_data_fullname); 
					$this->session->set_userdata($data);  
					redirect(''); 
				} if ($dt->member_type_id=='4') { 
					$data = array('user_login_apps' => TRUE, 'usergroup' => 'dosen', 'username' => $username,'user_id' => $dt->memberid,'fullname'=> $dt->master_data_fullname); 
					$this->session->set_userdata($data);  
					redirect(''); 
				} if ($dt->member_type_id=='7') { 
					$data = array('user_login_apps' => TRUE, 'usergroup' => 'pegawai', 'username' => $username,'user_id' => $dt->memberid,'fullname'=> $dt->master_data_fullname); 
					$this->session->set_userdata($data);  
					redirect(''); 
				} else { 
					$this->session->set_flashdata('error', 'username or password invalid');  
					redirect('login');
				}
			}
			else {
				$this->session->set_flashdata('error', 'username or password invalid'); 
				redirect('login');
			}
		}
		else {  
			$dt = $this->um->checkuser($username)->row(); 
			if($this->um->checkuser($username)->num_rows()!=0) {
				if ($dt->member_type_id=='1') { 
					$data = array('user_login_apps' => TRUE, 'usergroup' => 'superadmin', 'username' => $username,'user_id' => '1','fullname'=> $dt->master_data_fullname); 
					$this->session->set_userdata($data);   
					redirect('');  
				} if ($dt->member_type_id=='4') { 
					$data = array('user_login_apps' => TRUE, 'usergroup' => 'dosen', 'username' => $username,'user_id' => $dt->memberid,'fullname'=> $dt->master_data_fullname); 
					$this->session->set_userdata($data);  
					redirect(''); 
				} if ($dt->member_type_id=='7') { 
					$data = array('user_login_apps' => TRUE, 'usergroup' => 'pegawai', 'username' => $username,'user_id' => $dt->memberid,'fullname'=> $dt->master_data_fullname); 
					$this->session->set_userdata($data);  
					redirect(''); 
				} else { 
					$this->session->set_flashdata('error', 'username or password invalid'); 
					redirect('login');
				}
			}
			else {
				$this->session->set_flashdata('error', 'username or password invalid'); 
				redirect('login');
			}
		} 
	}
	
	 
	
	function logout()
	{
		//$data = array('login' => NULL, 'info_login' => '');
		$this->session->unset_userdata('user_login_apps');
		$this->session->unset_userdata('user_login_apps');
		
		redirect(''.'/login');
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
}

?>