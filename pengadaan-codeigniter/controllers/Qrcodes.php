<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Qrcodes extends CI_Controller 
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
		$this->load->library('ciqrcode');
	}
	
	public function index()
	{
		//if ($this->session->userdata('student_login')) redirect('Pendaftaran/home'); 
		
		
		$params['data'] = 'https://103.224.136.211/sites/index.php/guestbook';
		$params['level'] = 'H';
		$params['size'] = 5;
		$params['savename'] = FCPATH.'guestbook.png';
		$this->ciqrcode->generate($params);

		$data['qrcode'] = '<img src="'.base_url().'guestbook.png" />';
		$this->load->view('frontend/qrcodes',$data);
	}   

	public function openwindow()
    {
        echo "<script>window.top.location.href = 'https://pmb.ittelkom-sby.ac.id';</script>";
    }  
	
	
	public function member()
	{
		if(!$this->input->post('inp')) return false; 
 
  
		$inp = $this->input->post('inp');  

		$institution = "";
		$address     = $inp['address'];
		// if($type=='umum'){
			$email = $inp['email_umum'];
			$institution = $inp['institution_umum']; 
		$user 		= strtolower($email);
		$data 		= $this->ApiModel->checkUsername($user)->row();
		 
		$status = 'true';

		$C_KODE_STATUS_PEGAWAI = 'umum'; 

 		$member_type_id = '3'; 
		
	 
		if (!($data)) {
			if ($status!="false"){ 
				$item['C_NIP'] 												= $email;
				$item['NAMA_PEGAWAI'] 										= $inp['name'];
				$item['NO_HP'] 												= preg_replace('~\D~', '', $inp['phone']);
				$item['ALAMAT'] 											= $address; 
				$item['EMAIL'] 												= $email;
				$item['C_KODE_STATUS_PEGAWAI'] 								= $C_KODE_STATUS_PEGAWAI;
				$item['C_KODE_STATUS_AKTIF_PEGAWAI'] 						= 'A';
				$item['F_AKTIF'] 											= '1';
				$item['C_DATE']												= date('Y-m-d H:i:s'); 
				
				
				$item2['C_USERNAME'] 				= $item['C_NIP'];
				$item2['PASSWORD'] 					= md5($_POST['password']);
				$item2['PASSWORD_X'] 				= $_POST['password'];
				$item2['C_KODE_JENIS_USER'] = 'pegawai';
				$item2['USR_SHARING'] 			= '1';
				$item2['USR_THEME'] 				= '1';
				$item2['USR_EXPIRED'] 			= '2025-01-01 00:00:00';
				$item2['USR_MDD'] 					= 'simak';
				$item2['STATUS_USER'] 		= '1';
				// $item2['STATUS_USER'] 			= '0';
				$item2['F_AKTIF'] 					= '1';
				$item2['C_DATE']						= date('Y-m-d H:i:s');
				
				$item3['USR'] 											= $item['C_NIP'];
				$item3['USR_FLG'] 									= '1';
				$item3['USR_SHR'] 									= '1';
				$item3['USR_UXP'] 									= '2025-01-01 00:00:00';
				$item3['USR_MDD'] 									= 'simak';
				$item3['USR_NAME'] 									= ucwords(strtolower($inp['name']));
				$item3['USR_PASS'] 									= md5($_POST['password']);
				$item3['THE'] 										= '1';
				$item3['USR_C_DATE']								= date('Y-m-d H:i:s'); 
				$item4['member_class_id'] 							= '2';
				$item4['member_type_id'] 							= $member_type_id;
				$item4['member_class_id'] 							= '2';
				$item4['master_data_user'] 							= $item['C_NIP'];
				$item4['master_data_password'] 						= md5($_POST['password']);
				$item4['master_data_email'] 						= $item['C_NIP'];
				$item4['master_data_mobile_phone'] 					= $item['NO_HP'];
				$item4['master_data_address'] 						= $address;
				$item4['master_data_type'] 							= 'umum';
				$item4['master_data_fullname'] 						= ucwords(strtolower($inp['name']));
				$item4['master_data_institution'] 					= ucwords(strtolower($institution));
				$item4['created_at'] 								= date('Y-m-d H:i:s');
				// $item4['status'] 									= '2';
				$item4['status'] 									= '1';

				 
				$number 		= $this->ApiModel->getMasterDataNumber("3")->row(); 
				if($number){
					$temp = substr($number->max,-4,4);
					$temp = (int)$temp;
					$temp = $temp+1; 
					$item4['master_data_number'] 	= date('ymd')."3".sprintf("%04d", $temp);
				}
				else 	$item4['master_data_number'] 	= date('ymd')."30001";

					 
				 
				
				$this->ApiModel->addMember('member',$item4);
					 
				if ($this->ApiModel->add('t_mst_pegawai',$item) && $this->ApiModel->add('vfs_users',$item3) && $this->ApiModel->add('t_mst_user_login',$item2)) {
					$datas['email'] 		= $email;
					$datas['password'] 		= $item2['PASSWORD_X'];
					$datas['encode']	    = $encode;  

					// $jns_anggota = $this->cm->form_jenis_anggota(); 

					$content	= $this->load->view('email_template_all', $datas, true);
					$subject 	= "Register Telkom University Open Library";
					$callback = array('status' => 'success', 'text' => 'Terimakasih sudah mendaftar menjadi Anggota Digilib BRIN.');  
					// $state = SendEmail('library@telkomuniversity.ac.id','Digilib BRIN','Register Umum '.$item['NAMA_PEGAWAI'],'Digilib BRIN','');

					// $state = SendEmail($email,$subject,$content,'Digilib BRIN',ucwords(strtolower($item['NAMA_PEGAWAI'])));
 
				
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
		redirect('register');  
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

		redirect('/');
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