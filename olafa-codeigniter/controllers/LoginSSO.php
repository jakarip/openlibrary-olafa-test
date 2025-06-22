<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Common_Model', 'cm', TRUE);
		$this->load->model('Member_Model', 'dm', TRUE);
		$this->load->model('Referral_Model', '', TRUE);
		$this->load->model('Usermodel', 'um', TRUE);
		$this->load->model('ApiModel', '', TRUE);  
		$this->load->library("Encrypt");
		$this->load->helper('form');
	}
	
	public function index()
	{
	
	$server="ldaps.bppt.go.id";    
	$basedn="dc=bppt, dc=go, dc=id";
	$username_ldap= 'fulan.fulankak';
	$username='fulan.fulankak';
	$password		= 'pqbawNxG';
	$hsPassword =  "{MD5}" . base64_encode(pack('H*',md5($password)));
	echo $hsPassword;
	$ds=ldap_connect( "ldap://ldaps.bppt.go.id");
	ldap_set_option($ds,LDAP_OPT_PROTOCOL_VERSION,3);
	ldap_set_option($ds,LDAP_OPT_REFERRALS,0);
	if ($ds) {
					$r=ldap_bind($ds, "uid=ejurnal,ou=Application,dc=bppt,dc=go,dc=id", "ejurnal@2018");
					$sr=ldap_search($ds, "ou=People,dc=bppt,dc=go,dc=id", "uid=$username_ldap");
					$info = ldap_get_entries($ds, $sr);
                			$hasil = "no";
					
					echo "<pre>";
					print_r($info);
					echo "</pre>";
					if($info['count']==0) echo "g ada";
					else echo "yes";
					for ($i=0; $i<$info["count"]; $i++) {
	
						$hsNama =  $info[$i]["displayname"][0];
						$hsMail =  $info[$i]["mail"][0] ;
						$hsNip  =  $info[$i]["employeenumber"][0];
						$hsNipBaru=  $info[$i]["internationalisdnnumber"][0];
						$hsUnit =  "";
						$password_default = "1234";

						if($info[$i]["uid"][0] == $username_ldap AND $info[$i]["userpassword"][0] == $hsPassword )  {
					 
							$sqluser	= mysql_query("SELECT * FROM brin_old.empr WHERE empr_cb='".$hsNipBaru ."'");
							$fetch_user = mysql_fetch_array($sqluser);
							$count_user = mysql_num_rows($sqluser);

							
							if ($count_user>0){
								$_SESSION['id_user']		= $fetch_user['id_empr'];
							}
							else{
								$_SESSION['id_user']='0';
							}
							$_SESSION['user_reserve']	= $hsNipBaru;
							$_SESSION['name']			= $hsNama;
							$_SESSION['mail']=$username;
							ldap_close($ds);
							echo "success;".$_SESSION['id_user'];							
							//break;
						}
					}	  
					
					
				} 
	else {
					echo "error";
				}

	//if ($this->session->userdata('student_login')) redirect('Pendaftaran/home');
		// $data['jenis_anggota'] 	= $this->cm->form_jenis_anggota();
		// $data['ptasuh'] 				= $this->cm->form_ptasuh();
		// $data['lemdikti'] 			= $this->cm->form_lemdikti(); 
		// $this->load->view('frontend/login',$data);
	}

    public function embed()
    {
        //if ($this->session->userdata('student_login')) redirect('Pendaftaran/home');
        $this->load->view('frontend/login_embed');
    }
	
	// public function exe($embed='false')
	// {
	// 	$username = $this->input->post('username');
	// 	$password = $this->input->post('password');
	// 	$pass 	  = '$PMB$'.substr(sha1(md5(md5($password))), 0, 50);
		
	// 	$db = $this->dm->getby(array('par_participantnumber' => $username, 'par_password' => $pass, 'par_active' => '1'))->row();
		
	// 	if (!empty($db)) 
	// 	{ 
	// 		$data = array('participant_login' => TRUE, 'participant_login_info' => $db);
			
	// 		$this->session->set_userdata($data);
	// 		//$this->dm->edit($this->session->userdata('info_login_student')->student_id,array('student_status'=>'Y'));

  //           if($embed == 'embed') {
  //               redirect('login/openwindow');
  //           } else {
  //               redirect('');
  //           }
	// 	} 
	// 	else 
	// 	{
  //           $callback = array('status' => 'danger', 'text' => 'Username Password Salah<br>Pastikan anda sudah aktivasi akun, cek email anda untuk aktivasi');
  //           $this->session->set_flashdata('login_log', $callback);

  //           if($embed == 'embed') {
  //               redirect('login/openwindow');
  //           } else {
  //               redirect('login');
  //           }

	// 	}
	// }

	public function exe()
	{ 
		$username = strtolower($this->input->post('username'));
		$password = $this->input->post('password');
			 
		// $dt = $this->um->getbymember($username,md5($password))->row();

		
		$dt = $this->um->getbyone(array('master_data_user' => $username, 'master_data_password' => md5($password), 'status' => '1'))->row(); 

		if($dt){ 
			// if ($dt->member_type_id=='19') { 
			// 	if($dt->master_data_type=="alumni")
			// 			$data = array('user_login' => TRUE, 'usergroup' => 'alumni', 'username' => $username,'fullname'=> $dt->master_data_fullname,'member_id'=> $dt->id); 
			// 	else	if($dt->master_data_type=="ptasuh")
			// 		$data = array('user_login' => TRUE, 'usergroup' => 'ptasuh', 'username' => $username,'fullname'=> $dt->master_data_fullname,'member_id'=> $dt->id); 
			// 	else if($dt->master_data_type=="lemdikti")
			// 		$data = array('user_login' => TRUE, 'usergroup' => 'lemdikti', 'username' => $username,'fullname'=> $dt->master_data_fullname,'member_id'=> $dt->id); 
			// 	else  
			// 		$data = array('user_login' => TRUE, 'usergroup' => 'umum', 'username' => $username,'fullname'=> $dt->master_data_fullname,'member_id'=> $dt->id); 

			// 	if($dt->master_data_type!="umum")
			// 		$this->session->set_userdata($data);  
			// 		redirect('');

			// } else { 
				// $data = array('user_login' => TRUE, 'usergroup' => $mmbrtype[$dt->member_type_id], 'username' => $username,'fullname'=> $dt->master_data_fullname,'member_id'=> $dt->id); 
				// $this->session->set_userdata($data);  
				// redirect('');
			$data = array('user_login' => TRUE, 'usergroup' => $dt->master_data_type, 'username' => $username,'fullname'=> $dt->master_data_fullname,'member_id'=> $dt->id); 
			$this->session->set_userdata($data);  
			redirect('');
	}
	else {
		$this->session->set_flashdata('error', 'Username Password Salah'); 
		redirect('login'); 
		
	} 
}

	public function openwindow()
    {
        echo "<script>window.top.location.href = 'https://pmb.ittelkom-sby.ac.id';</script>";
    }  
	
	
	public function reg()
	{
		if(!$this->input->post('inp')) return false; 


			$captcha  	= $this->input->post('g-recaptcha-response');
			$response 	= file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LcImxYaAAAAAPrArEmjLpxvs8kMr6KoYxE8ZCNb&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
			$obj 	  	= json_decode($response);
			if($obj->success == false)
			{
					$callback = array('status' => 'danger', 'text' => 'Captcha Error. Silakan klik "I\'m not a robot"');
					$this->session->set_flashdata('login_log', $callback);
					redirect('login'); 
			}
  
		$inp = $this->input->post('inp'); 
		$type = $this->input->post('type');  

		$institution = "";
		$address     = $inp['address'];
		if($type=='umum'){
			$email = $inp['email_umum'];
			$institution = $inp['institution_umum'];
		}
		else if($type=='alumni'){
			$email = $inp['email_alumni'];  
		}
		else if($type=='lemdikti'){
			$lemdikti = $this->cm->form_lemdikti(); 
			$email = $inp['email_lemdikti']; 
			$institution = $lemdikti[$inp['institution_lemdikti']];

		}
		else if($type=='ptasuh'){ 
			
			$ptasuh = $this->cm->form_ptasuh();
			$email = $inp['email_ptasuh']; 
			$institution = $ptasuh[$inp['institution_ptasuh']];
		}
		else if($type=='internasional'){  
			$email = $inp['email_internasional'];
			$institution = $inp['institution_internasional'];
		}

		 
		$lemdikti = $this->cm->form_lemdikti_email(); 
		$ptasuh 	= $this->cm->form_ptasuh_email(); 
         
		$user 		= strtolower($email);
		$data 		= $this->ApiModel->checkUsername($user)->row();
		// if($type=='umum' )$status 	= $this->blacklistMail($user);
	 
		if($type=='ptasuh' or $type=='lemdikti')$status 	= $this->checkEmail($type,$inp); 
		else $status = 'true';

		if($type=='internasional')$C_KODE_STATUS_PEGAWAI = 'internasional'; 
		else $C_KODE_STATUS_PEGAWAI = 'P'; 

		if($type=='internasional')$member_type_id = '24'; 
		else $member_type_id = '19'; 
		
	 
		if (!($data)) {
			if ($status!="false"){ 
				$item['C_NIP'] 												= $email;
				$item['NAMA_PEGAWAI'] 								= $inp['name'];
				$item['NO_HP'] 												= preg_replace('~\D~', '', $inp['phone']);
				$item['ALAMAT'] 											= $address; 
				$item['EMAIL'] 												= $email;
				$item['C_KODE_STATUS_PEGAWAI'] 				= $C_KODE_STATUS_PEGAWAI;
				$item['C_KODE_STATUS_AKTIF_PEGAWAI'] 	= 'A';
				$item['F_AKTIF'] 											= '1';
				$item['C_DATE']												= date('Y-m-d H:i:s');
				// $string 	  													= $this->encrypt->mcrypt_encode($email,"Telkom University Open Library");
				// $encode 	  													= $this->url_query_encode($string);
				
				
				$item2['C_USERNAME'] 				= $item['C_NIP'];
				$item2['PASSWORD'] 					= md5($_POST['password']);
				$item2['PASSWORD_X'] 				= $_POST['password'];
				$item2['C_KODE_JENIS_USER'] = 'pegawai';
				$item2['USR_SHARING'] 			= '1';
				$item2['USR_THEME'] 				= '1';
				$item2['USR_EXPIRED'] 			= '2025-01-01 00:00:00';
				$item2['USR_MDD'] 					= 'simak';
				//$item2['STATUS_USER'] 		= '1';
				$item2['STATUS_USER'] 			= '0';
				$item2['F_AKTIF'] 					= '1';
				$item2['C_DATE']						= date('Y-m-d H:i:s');
				
				$item3['USR'] 											= $item['C_NIP'];
				$item3['USR_FLG'] 									= '1';
				$item3['USR_SHR'] 									= '1';
				$item3['USR_UXP'] 									= '2025-01-01 00:00:00';
				$item3['USR_MDD'] 									= 'simak';
				$item3['USR_NAME'] 									= ucwords(strtolower($inp['name']));
				$item3['USR_PASS'] 									= md5($_POST['password']);
				$item3['THE'] 											= '1';
				$item3['USR_C_DATE']								= date('Y-m-d H:i:s'); 
				$item4['member_class_id'] 					= '2';
				$item4['member_type_id'] 						= $member_type_id;
				$item4['member_class_id'] 					= '2';
				$item4['master_data_user'] 					= $item['C_NIP'];
				$item4['master_data_password'] 			= md5($_POST['password']);
				$item4['master_data_email'] 				= $item['C_NIP'];
				$item4['master_data_mobile_phone'] 	= $item['NO_HP'];
				$item4['master_data_address'] 			= $address;
				$item4['master_data_type'] 					= $type;
				$item4['master_data_fullname'] 			= ucwords(strtolower($inp['name']));
				$item4['master_data_institution'] 	= ucwords(strtolower($institution));
				$item4['created_at'] 								= date('Y-m-d H:i:s');
				$item4['status'] 									= '2';
				//$item4['status'] 					= '1';

				
				if($type=='umum'){  
				
					$number 		= $this->ApiModel->getMasterDataNumber("23")->row(); 
					if($number){
						$temp = substr($number->max,-4,4);
						$temp = (int)$temp;
						$temp = $temp+1; 
						$item4['master_data_number'] 	= date('ymd')."23".sprintf("%04d", $temp);
					}
					else 	$item4['master_data_number'] 	= date('ymd')."230001";

					//gambar & file
					$upload_path = "cdn";
					$upPath		 = $upload_path."/".$email.'/';
					if(!file_exists($upPath))
					{
						mkdir($upPath, 0777, true);
					} 

					if(isset($_FILES['ktp_umum']) && $_FILES['ktp_umum']['name'] != '' && $_FILES['ktp_umum']['error'] != UPLOAD_ERR_NO_FILE)
					{
						$config = array(
							'file_name'		=> 'ktp',
							'upload_path' 	=> $upPath,
							'allowed_types' => "jpg|png|jpeg|pdf",
							'overwrite'		=> TRUE
						);

						$this->load->library('upload');
						$this->upload->initialize($config);
						if(!$this->upload->do_upload('ktp_umum'))
						{
							$error = strip_tags($this->upload->display_errors())."\n";
							echo json_encode(array('status'=> 'failed', 'error' => $error));
							return false;
						}
						else
						{ 
							$ud  	 = $this->upload->data();
							// var_dump($ud);
							$ext 	 = str_replace('.', '', $ud['file_ext']); 
							$item4['master_data_ktp'] 	= $config['upload_path'].$ud['file_name'];
						}
					}
					
					if(isset($_FILES['idcard_umum']) && $_FILES['idcard_umum']['name'] != '' && $_FILES['idcard_lemdikti']['error'] != UPLOAD_ERR_NO_FILE)
					{
						$config = array(
							'file_name'		=> 'idcard',
							'upload_path' 	=> $upPath,
							'allowed_types' => "jpg|png|jpeg|pdf",
							'overwrite'		=> TRUE
						);

						$this->load->library('upload');
						$this->upload->initialize($config);
						if(!$this->upload->do_upload('idcard_umum'))
						{
							$error = strip_tags($this->upload->display_errors())."\n";
							echo json_encode(array('status'=> 'failed', 'error' => $error));
							return false;
						}
						else
						{ 
							$ud  	 = $this->upload->data();
							// var_dump($ud);
							$ext 	 = str_replace('.', '', $ud['file_ext']); 
							$item4['master_data_idcard'] 	= $config['upload_path'].$ud['file_name'];
						}
					}   
				}
				else if($type=='internasional'){   
					$number 		= $this->ApiModel->getMasterDataNumber("24")->row(); 
					if($number){
						$temp = substr($number->max,-4,4);
						$temp = (int)$temp;
						$temp = $temp+1; 
						$item4['master_data_number'] 	= date('ymd')."24".sprintf("%04d", $temp);
					}
					else 	$item4['master_data_number'] 	= date('ymd')."240001"; 
				}
				else if($type=='alumni'){   
					$number 		= $this->ApiModel->getMasterDataNumber("20")->row(); 
					if($number){
						$temp = substr($number->max,-4,4);
						$temp = (int)$temp;
						$temp = $temp+1; 
						$item4['master_data_number'] 	= date('ymd')."20".sprintf("%04d", $temp);
					}
					else 	$item4['master_data_number'] 	= date('ymd')."200001";

					//gambar & file
					$upload_path = "cdn";
					$upPath		 = $upload_path."/".$email.'/';
					if(!file_exists($upPath))
					{
						mkdir($upPath, 0777, true);
					} 

					if(isset($_FILES['ktp_alumni']) && $_FILES['ktp_alumni']['name'] != '' && $_FILES['ktp_alumni']['error'] != UPLOAD_ERR_NO_FILE)
					{
						$config = array(
							'file_name'		=> 'ktp',
							'upload_path' 	=> $upPath,
							'allowed_types' => "jpg|png|jpeg|pdf",
							'overwrite'		=> TRUE
						);

						$this->load->library('upload');
						$this->upload->initialize($config);
						if(!$this->upload->do_upload('ktp_alumni'))
						{
							$error = strip_tags($this->upload->display_errors())."\n";
							echo json_encode(array('status'=> 'failed', 'error' => $error));
							return false;
						}
						else
						{ 
							$ud  	 = $this->upload->data();
							// var_dump($ud);
							$ext 	 = str_replace('.', '', $ud['file_ext']); 
							$item4['master_data_ktp'] 	= $config['upload_path'].$ud['file_name'];
						}
					}
					
					if(isset($_FILES['ijasah']) && $_FILES['ijasah']['name'] != '' && $_FILES['ijasah']['error'] != UPLOAD_ERR_NO_FILE)
					{
						$config = array(
							'file_name'		=> 'ijasah',
							'upload_path' 	=> $upPath,
							'allowed_types' => "jpg|png|jpeg|pdf",
							'overwrite'		=> TRUE
						);

						$this->load->library('upload');
						$this->upload->initialize($config);
						if(!$this->upload->do_upload('ijasah'))
						{
							$error = strip_tags($this->upload->display_errors())."\n";
							echo json_encode(array('status'=> 'failed', 'error' => $error));
							return false;
						}
						else
						{ 
							$ud  	 = $this->upload->data();
							// var_dump($ud);
							$ext 	 = str_replace('.', '', $ud['file_ext']); 
							$item4['master_data_ijasah'] 	= $config['upload_path'].$ud['file_name'];
						}
					}    
				}
				else if($type=='lemdikti'){   
					$number 		= $this->ApiModel->getMasterDataNumber("21")->row(); 
					if($number){
						$temp = substr($number->max,-4,4);
						$temp = (int)$temp;
						$temp = $temp+1; 
						$item4['master_data_number'] 	= date('ymd')."21".sprintf("%04d", $temp);
					}
					else 	$item4['master_data_number'] 	= date('ymd')."210001";

					//gambar & file
					$upload_path = "cdn";
					$upPath		 = $upload_path."/".$email.'/';
					if(!file_exists($upPath))
					{
						mkdir($upPath, 0777, true);
					} 

					if(isset($_FILES['ktp_lemdikti']) && $_FILES['ktp_lemdikti']['name'] != '' && $_FILES['ktp_lemdikti']['error'] != UPLOAD_ERR_NO_FILE)
					{
						$config = array(
							'file_name'		=> 'ktp',
							'upload_path' 	=> $upPath,
							'allowed_types' => "jpg|png|jpeg|pdf",
							'overwrite'		=> TRUE
						);

						$this->load->library('upload');
						$this->upload->initialize($config);
						if(!$this->upload->do_upload('ktp_lemdikti'))
						{
							$error = strip_tags($this->upload->display_errors())."\n";
							echo json_encode(array('status'=> 'failed', 'error' => $error));
							return false;
						}
						else
						{ 
							$ud  	 = $this->upload->data();
							// var_dump($ud);
							$ext 	 = str_replace('.', '', $ud['file_ext']); 
							$item4['master_data_ktp'] 	= $config['upload_path'].$ud['file_name'];
						}
					}
					
					if(isset($_FILES['idcard_lemdikti']) && $_FILES['idcard_lemdikti']['name'] != '' && $_FILES['idcard_lemdikti']['error'] != UPLOAD_ERR_NO_FILE)
					{
						$config = array(
							'file_name'		=> 'idcard',
							'upload_path' 	=> $upPath,
							'allowed_types' => "jpg|png|jpeg|pdf",
							'overwrite'		=> TRUE
						);

						$this->load->library('upload');
						$this->upload->initialize($config);
						if(!$this->upload->do_upload('idcard_lemdikti'))
						{
							$error = strip_tags($this->upload->display_errors())."\n";
							echo json_encode(array('status'=> 'failed', 'error' => $error));
							return false;
						}
						else
						{ 
							$ud  	 = $this->upload->data();
							// var_dump($ud);
							$ext 	 = str_replace('.', '', $ud['file_ext']); 
							$item4['master_data_idcard'] 	= $config['upload_path'].$ud['file_name'];
						}
					}    
				}
				else if($type=='ptasuh'){   
					$number 		= $this->ApiModel->getMasterDataNumber("22")->row(); 
					if($number){
						$temp = substr($number->max,-4,4);
						$temp = (int)$temp;
						$temp = $temp+1; 
						$item4['master_data_number'] 	= date('ymd')."22".sprintf("%04d", $temp);
					}
					else 	$item4['master_data_number'] 	= date('ymd')."220001";

					//gambar & file
					$upload_path = "cdn";
					$upPath		 = $upload_path."/".$email.'/';
					if(!file_exists($upPath))
					{
						mkdir($upPath, 0777, true);
					} 

					if(isset($_FILES['ktp_ptasuh']) && $_FILES['ktp_ptasuh']['name'] != '' && $_FILES['ktp_ptasuh']['error'] != UPLOAD_ERR_NO_FILE)
					{
						$config = array(
							'file_name'		=> 'ktp',
							'upload_path' 	=> $upPath,
							'allowed_types' => "jpg|png|jpeg|pdf",
							'overwrite'		=> TRUE
						);

						$this->load->library('upload');
						$this->upload->initialize($config);
						if(!$this->upload->do_upload('ktp_ptasuh'))
						{
							$error = strip_tags($this->upload->display_errors())."\n";
							echo json_encode(array('status'=> 'failed', 'error' => $error));
							return false;
						}
						else
						{ 
							$ud  	 = $this->upload->data();
							// var_dump($ud);
							$ext 	 = str_replace('.', '', $ud['file_ext']); 
							$item4['master_data_ktp'] 	= $config['upload_path'].$ud['file_name'];
						}
					}
					
					if(isset($_FILES['idcard_ptasuh']) && $_FILES['idcard_ptasuh']['name'] != '' && $_FILES['idcard_ptasuh']['error'] != UPLOAD_ERR_NO_FILE)
					{
						$config = array(
							'file_name'		=> 'idcard',
							'upload_path' 	=> $upPath,
							'allowed_types' => "jpg|png|jpeg|pdf",
							'overwrite'		=> TRUE
						);

						$this->load->library('upload');
						$this->upload->initialize($config);
						if(!$this->upload->do_upload('idcard_ptasuh'))
						{
							$error = strip_tags($this->upload->display_errors())."\n";
							echo json_encode(array('status'=> 'failed', 'error' => $error));
							return false;
						}
						else
						{ 
							$ud  	 = $this->upload->data();
							// var_dump($ud);
							$ext 	 = str_replace('.', '', $ud['file_ext']); 
							$item4['master_data_idcard'] 	= $config['upload_path'].$ud['file_name'];
						}
					}    
				}
				
				$this->ApiModel->addMember('member',$item4);
					 
				if ($this->ApiModel->add('t_mst_pegawai',$item) && $this->ApiModel->add('vfs_users',$item3) && $this->ApiModel->add('t_mst_user_login',$item2)) {
					$datas['email'] 		= $email;
					$datas['password'] 		= $item2['PASSWORD_X'];
					$datas['encode']	    = $encode;  

					$jns_anggota = $this->cm->form_jenis_anggota(); 

					$content	= $this->load->view('email_template_all', $datas, true);
					$subject 	= "Register Telkom University Open Library";
					$callback = array('status' => 'success', 'text' => 'Terimakasih sudah mendaftar Open Library Telkom University. <br /> 
					Kami akan verifikasi data Anda terlebih dahulu dan akan konfirmasi via email.<br><br>Thank You for Register in Open Library Telkom University. <br /> 
					We will verify your data first and will confirm via email');  
					$state = SendEmail('library@telkomuniversity.ac.id','Register '.$jns_anggota[$type],'Register '.$jns_anggota[$type].' '.$item['NAMA_PEGAWAI'],'Telkom University Open Library','');

					$state = SendEmail($email,$subject,$content,'Telkom University Open Library',ucwords(strtolower($item['NAMA_PEGAWAI'])));
 
				
					$state = json_decode($status);
					if($state['status']=='SUCCESS') 
						$status = true;
				} 
			}
			else {
				$callback = array('status' => 'danger', 'text' => 'Email yang digunakan bukan email institusi'); 
				$callback2 = array('name'=>$inp['name'],'phone'=>$inp['phone'],'email_umum'=>$inp['email_umum'],'email_alumni'=>$inp['email_alumni'],'email_lemdikti'=>$inp['email_lemdikti'],'email_ptasuh'=>$inp['email_ptasuh'],'institution_umum'=>$inp['institution_umum'],'institution_lemdikti'=>$inp['institution_lemdikti'],'institution_ptasuh'=>$inp['institution_ptasuh'],'address'=>$inp['address']); 
				$this->session->set_flashdata('reg_log', $callback2);
			}
		}
		else {
			$callback = array('status' => 'danger', 'text' => 'Email anda sudah terdaftar di data kami'); 
			$callback2 = array('name'=>$inp['name'],'phone'=>$inp['phone'],'email_umum'=>$inp['email_umum'],'email_alumni'=>$inp['email_alumni'],'email_lemdikti'=>$inp['email_lemdikti'],'email_ptasuh'=>$inp['email_ptasuh'],'institution_umum'=>$inp['institution_umum'],'institution_lemdikti'=>$inp['institution_lemdikti'],'institution_ptasuh'=>$inp['institution_ptasuh'],'address'=>$inp['address']); 
			$this->session->set_flashdata('reg_log', $callback2);
		}
		 
		$this->session->set_flashdata('login_log', $callback);
    redirect('login'); 

            // $item['par_password_plain'] = $this->random_str(10);
            // $item['par_password'] = '$PMB$'.substr(sha1(md5(md5($item['par_password_plain']))), 0, 50);
            // $item['par_active_code'] = $this->generate_uuid();

            // if( $this->dm->add($item) )
            // {
                // $callback = array('status' => 'success', 'text' => 'Terima kasih telah melakukan registrasi akun PMB. Username dan Password telah kami kirimkan ke email: <strong>'.$item['par_email'].'</strong>.<br>Silakan cek email anda untuk verifikasi akun. Jika email tidak ada silakan cek folder SPAM.');

                // $this->session->set_flashdata('login_log', $callback);
                // $this->send_email($item);

                // redirect('login');
            // }
            // else
            // {
                // $callback = array('status' => 'success', 'text' => 'Gagal Menyimpan Data, silakan ulangi kembali');
                // $this->session->set_flashdata('login_log', $callback);
                // redirect('login');
            // }
        // }
        // else
        // {
            // $callback = array('status' => 'danger', 'text' => 'Isian anda kurang lengkap, silakan ulangi kembali');
            // $this->session->set_flashdata('login_log', $callback);
            // redirect('login');
        // }
    } 
	
	
	
	function url_query_encode($array = array())
	{
		return str_replace('/', '_', rtrim(base64_encode(gzcompress(serialize($array))), '='));
	}


	function url_query_decode($str = '')
	{
		return (is_string($str) && strlen($str)) ? @unserialize(gzuncompress(base64_decode(str_replace('_', '/', $str)))) : FALSE;
	}
	
	function blacklistMail($user){
		
		$blacklistMail[] = '@gmail.';
		$blacklistMail[] = '@yahoo.';
		$blacklistMail[] = '@hotmail.';
		$blacklistMail[] = '@rocketmail.';
		$blacklistMail[] = '@kompas.';
		$blacklistMail[] = '@facebook.';
		$blacklistMail[] = '@tandex.';
		$blacklistMail[] = '@fastmail.';
		$blacklistMail[] = '@ymail.';
	
		
		 $status = "true";
		foreach ($blacklistMail as $row){
			if (strpos($user,$row)!==false) {
				$status = "false";
			}
		} 
		
		return $status;
	}
	
	function checkEmail($type,$inp){

		$lemdikti = $this->cm->form_lemdikti_email(); 
		$ptasuh 	= $this->cm->form_ptasuh_email(); 
		
		
		if($type=='lemdikti') {
			$status = "false"; 
			if (strpos(strtolower($inp['email_lemdikti']),$lemdikti[$inp['institution_lemdikti']])!==false) {
				$status = "true";
			}
		}
		else {
			$status = "false"; 
			if (strpos(strtolower($inp['email_ptasuh']),$ptasuh[$inp['institution_ptasuh']])!==false) {
				$status = "true";
			}
		} 
		
		return $status;
	}

	
	
	function whitelistMail($user){
		
		$blacklistMail[] = '@gmail.';
	
		
		 $status = "false";
		foreach ($blacklistMail as $row){
			if (strpos($user,$row)!==false) {
				$status = "false";
			}
		} 
		
		return $status;
	}
	
	function logout()
	{
		$data = array('participant_login' => NULL, 'participant_login_info' => NULL, 'setting' => NULL);
		// $this->session->set_flashdata($data);
        // $this->session->sess_destroy();
        
        $this->session->unset_userdata('user_login'); 

		redirect('login');
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/

	private function random_str($length)
    {
        $keyspace = str_shuffle('ACDEFGHJKLMNPQRTUVWXY123456789');
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[mt_rand(0, $max)];
        }
        return implode('', $pieces);
    }

    private function generate_uuid()
    {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0C2f ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0x2Aff ), mt_rand( 0, 0xffD3 ), mt_rand( 0, 0xff4B )
        );
    }

    public function send_email($data)
    {
        $data['url'] = base64_encode("signature_pmb#_#".$data['par_active_code']."#_#".$data['par_email']);
        $data['setting'] = y_load_setting();

        $body = $this->load->view('frontend/login/email_register', $data, true);

        echo y_send_email($data['par_email'], '['.$data['setting']['website_name'].'] Pendaftaran Akun Baru PMB', $body);
    }
}

?>