<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Member_Model', 'dm', TRUE);
		$this->load->model('Referral_Model', '', TRUE);
		$this->load->model('Usermodel', 'um', TRUE);
		$this->load->model('ApiModel', '', TRUE);  
		$this->load->library("Encrypt");
	}
	
	public function index()
	{
		//if ($this->session->userdata('student_login')) redirect('Pendaftaran/home');
		$this->load->view('frontend/login');
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
			if ($dt->member_type_id=='19') { 
				if($dt->master_data_type=="alumni")
						$data = array('user_login' => TRUE, 'usergroup' => 'alumni', 'username' => $username,'fullname'=> $dt->master_data_fullname,'member_id'=> $dt->id); 
				else  
				$data = array('user_login' => TRUE, 'usergroup' => 'umum', 'username' => $username,'fullname'=> $dt->master_data_fullname,'member_id'=> $dt->id); 

				if($dt->master_data_type=="alumni")
					$this->session->set_userdata($data);  
					redirect('');

			} else	if ($dt->member_type_id=='20') { 
				$data = array('user_login' => TRUE, 'usergroup' => 'alumni', 'username' => $username,'fullname'=> $dt->master_data_fullname,'member_id'=> $dt->id); 
				$this->session->set_userdata($data);  
				redirect('');
		}else { 
			$this->session->set_flashdata('error', 'Username Password Salah');  
			redirect('login');
		}
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
		
		$email = ($type=='alumni'?$inp['email_alumni'] : $inp['email_umum']);
         
		$user 		= strtolower($email);
		$data 		= $this->ApiModel->checkUsername($user)->row();
		if($type=='umum' )$status 	= $this->blacklistMail($user);
		else $status = 'true';
		
	 
		if (!($data)) {
			if ($status!="false"){ 
				$item['C_NIP'] 							= $email;
				$item['NAMA_PEGAWAI'] 			= $inp['name'];
				$item['NO_HP'] 							= preg_replace('~\D~', '', $inp['phone']);
				$item['ALAMAT'] 						= $inp['institution']; 
				$item['EMAIL'] 							= $email;
				$item['C_KODE_STATUS_PEGAWAI'] 			= 'P';
				$item['C_KODE_STATUS_AKTIF_PEGAWAI'] 	= 'A';
				$item['F_AKTIF'] 						= '1';
				$item['C_DATE']							= date('Y-m-d H:i:s');
				$string 	  							= $this->encrypt->mcrypt_encode($email,"Telkom University Open Library");
				$encode 	  							= $this->url_query_encode($string);
				
				
				$item2['C_USERNAME'] 		= $item['C_NIP'];
				$item2['PASSWORD'] 			= md5($_POST['password']);
				$item2['PASSWORD_X'] 		= $_POST['password'];
				$item2['C_KODE_JENIS_USER'] = 'pegawai';
				$item2['USR_SHARING'] 		= '1';
				$item2['USR_THEME'] 		= '1';
				$item2['USR_EXPIRED'] 		= '2025-01-01 00:00:00';
				$item2['USR_MDD'] 			= 'simak';
				//$item2['STATUS_USER'] 		= '1';
				$item2['STATUS_USER'] 		= '0';
				$item2['F_AKTIF'] 			= '1';
				$item2['C_DATE']			= date('Y-m-d H:i:s');
				
				$item3['USR'] 				= $item['C_NIP'];
				$item3['USR_FLG'] 		= '1';
				$item3['USR_SHR'] 		= '1';
				$item3['USR_UXP'] 		= '2025-01-01 00:00:00';
				$item3['USR_MDD'] 		= 'simak';
				$item3['USR_NAME'] 		= ucwords(strtolower($inp['name']));
				$item3['USR_PASS'] 		= md5($_POST['password']);
				$item3['THE'] 			= '1';
				$item3['USR_C_DATE']	= date('Y-m-d H:i:s'); 
				$item4['member_class_id'] 			= '2';
				$item4['member_type_id'] 			= 19;
				$item4['member_class_id'] 			= '2';
				$item4['master_data_user'] 			= $item['C_NIP'];
				$item4['master_data_password'] 		= md5($_POST['password']);
				$item4['master_data_email'] 		= $item['C_NIP'];
				$item4['master_data_mobile_phone'] 	= $item['NO_HP'];
				$item4['master_data_address'] 	= $inp['address'];
				$item4['master_data_type'] 	= $type;
				$item4['master_data_fullname'] 		= ucwords(strtolower($inp['name']));
				$item4['master_data_institution'] 	= ucwords(strtolower($inp['institution']));
				$item4['created_at'] 				= date('Y-m-d H:i:s');
				$item4['status'] 					= '2';
				//$item4['status'] 					= '1';

				
				$number 		= $this->ApiModel->getMasterDataNumberPublic()->row(); 
				if($number){
					$temp = substr($number->max,-4,4);
					$temp = (int)$temp;
					$temp = $temp+1; 
					$item4['master_data_number'] 	= date('ymd')."19".sprintf("%04d", $temp);
				}
				else 	$item4['master_data_number'] 	= date('ymd')."190001";
				
				if($type=='alumni'){   
					$number 		= $this->ApiModel->getMasterDataNumber()->row(); 
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

					if(isset($_FILES['ktp']) && $_FILES['ktp']['name'] != '' && $_FILES['ktp']['error'] != UPLOAD_ERR_NO_FILE)
					{
						$config = array(
							'file_name'		=> 'ktp',
							'upload_path' 	=> $upPath,
							'allowed_types' => "jpg|png|jpeg|pdf",
							'overwrite'		=> TRUE
						);

						$this->load->library('upload');
						$this->upload->initialize($config);
						if(!$this->upload->do_upload('ktp'))
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
					// $item5['subscribe_id_member'] = $this->ApiModel->addMember('member',$item4);
					// $item5['subscribe_month'] 		= $waktu_biaya[0];
					// $item5['subscribe_biaya'] 		= $waktu_biaya[1];
					// $item5['subscribe_status'] 		= '0';
					// $item5['subscribe_date'] 			= date('Y-m-d H:i:s');
					
					// $this->ApiModel->addMember('member_subscribe',$item5);  
				} 
				
				$this->ApiModel->addMember('member',$item4);
					 
				if ($this->ApiModel->add('t_mst_pegawai',$item) && $this->ApiModel->add('vfs_users',$item3) && $this->ApiModel->add('t_mst_user_login',$item2)) {
					$datas['email'] 		= $email;
					$datas['password'] 		= $item2['PASSWORD_X'];
					$datas['encode']	    = $encode; 


					$content 				= $this->load->view('email_template', $datas, true);

					if($type=='alumni'){
						$content	= $this->load->view('email_template_alumni', $datas, true);
						$subject 	= "Register Telkom University Open Library";
						$callback = array('status' => 'success', 'text' => 'Terimakasih sudah mendaftar Open Library Telkom University. <br /> 
						Kami akan verifikasi data Anda terlebih dahulu dan akan konfirmasi via email.');  
            $state = SendEmail('library@telkomuniversity.ac.id','Register Alumni','Register Alumni '.$item['NAMA_PEGAWAI'],'Telkom University Open Library','');
					}
					else 	{
						$content 	= $this->load->view('email_template', $datas, true);
						$subject 	= "Aktivasi Akun Telkom University Open Library"; 
						$callback = array('status' => 'success', 'text' => 'Akun anda sudah berhasil dibuat. <br /> 
						Silahkan verifikasi akun anda dengan cara klik link aktivasi yang kami kirim pada email anda.'); 
					}

					$state = SendEmail($email,$subject,$content,'Telkom University Open Library',ucwords(strtolower($item['NAMA_PEGAWAI'])));

					// echo "aasdas";
					// print_r($state);
				
					$state = json_decode($status);
					if($state['status']=='SUCCESS') $status = true;
				} 
			}
			else {
				$callback = array('status' => 'danger', 'text' => 'Email yang digunakan bukan email institusi'); 
				$callback2 = array('name'=>$inp['name'],'phone'=>$inp['phone'],'email_umum'=>$inp['email_umum'],'email_alumni'=>$inp['email_alumni'],'institution'=>$inp['institution'],'address'=>$inp['address']); 
				$this->session->set_flashdata('reg_log', $callback2);
			}
		}
		else {
			$callback = array('status' => 'danger', 'text' => 'Email anda sudah terdaftar di data kami'); 
			$callback2 = array('name'=>$inp['name'],'phone'=>$inp['phone'],'email_umum'=>$inp['email_umum'],'email_alumni'=>$inp['email_alumni'],'institution'=>$inp['institution'],'address'=>$inp['address']); 
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