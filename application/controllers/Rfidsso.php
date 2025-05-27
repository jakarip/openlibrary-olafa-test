<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class Rfidsso extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config); 
		$this->load->model('Usermodel', 'um', TRUE);
    }
  
   
     function index_post() { 
		$status = $this->input->post('status');
		if($status=='insert'){
		
			$data = array(  
				"member_class_id" 			=> '2', 
				"status" 					=> "1",
				"created_by" 				=> 'openlibrary',
				"created_at" 				=> date("Y-m-d H:i:s")
			);
			
			$data['member_type_id'] 				= $this->input->post('member_type_id');
			$data['master_data_user']  				= $this->input->post('master_data_user');
			$data['master_data_email']  			= $this->input->post('master_data_email');
			$data['master_data_mobile_phone']  		= $this->input->post('master_data_mobile_phone');
			$data['master_data_course']  			= $this->input->post('master_data_course');
			$data['master_data_fullname']  			= $this->input->post('master_data_fullname');
			$data['master_data_number']  			= $this->input->post('master_data_number');
			$data['rfid1']  						= $this->input->post('rfid1');
			$data['rfid2']  						= $this->input->post('rfid2'); 
			$data['master_data_lecturer_status']  	= $this->input->post('master_data_lecturer_status'); 
			$data['master_data_generation']  		= $this->input->post('master_data_generation'); 
			$data['master_data_photo']  			= $this->input->post('master_data_photo'); 
			$data['master_data_status']  			= $this->input->post('studenttypename'); 
			$data['master_data_nidn']  				= $this->input->post('master_data_nidn'); 
			
			$member_id = $this->um->add($data);  
			
			if($this->input->post('c_kode_jenis_user')=='pegawai'){
				//pegawai / dosen
				$item['C_NIP'] 							= $data['master_data_user'];
				$item['NAMA_PEGAWAI'] 					= $data['master_data_fullname']; 
				$item['NO_HP'] 							= $data['master_data_mobile_phone'];
				$item['EMAIL'] 							= $data['master_data_email'];
				$item['C_KODE_STATUS_PEGAWAI'] 			= 'P';
				$item['C_KODE_STATUS_AKTIF_PEGAWAI'] 	= 'A';
				$item['F_AKTIF'] 						= '1';
				$item['C_DATE']							= date('Y-m-d H:i:s');
				$item['C_USER']							= 'Openlibrary'; 
				
				$this->um->addItem('masterdata.t_mst_pegawai',$item); 
			}
			else {
				//mahasiswa
				$item['C_KODE_PT'] 						= '42009';
				$item['C_KODE_FAKULTAS'] 				= $this->input->post('master_data_faculty');
				$item['C_KODE_PRODI'] 					= $data['master_data_course'];
				$item['C_NPM'] 							= $data['master_data_user'];
				$item['C_NPM_IGRACIAS'] 				= $data['master_data_number'];  
				$item['RFID1'] 							= $data['rfid1'];
				$item['RFID2'] 							= $data['rfid2'];
				$item['NAMA_MAHASISWA'] 				= $data['master_data_fullname'];
				$item['C_KODE_STATUS_AKTIF_MHS'] 		= 'A';
				$item['F_AKTIF'] 						= '1'; 
				$item['C_DATE']							= date('Y-m-d H:i:s');
				$item['C_USER']							= 'Openlibrary'; 
				
				$this->um->addItem('masterdata.t_mst_mahasiswa',$item); 
			}
			
			$item2['C_USERNAME'] 		= $data['master_data_user'];
			$item2['PASSWORD'] 			= '';
			$item2['PASSWORD_X'] 		= '';
			$item2['C_KODE_JENIS_USER'] = ($this->input->post('c_kode_jenis_user')=='pegawai'?'pegawai':'mahasiswa');
			$item2['USR_SHARING'] 		= '1';
			$item2['USR_THEME'] 		= '1';
			$item2['USR_EXPIRED'] 		= '2030-01-01 00:00:00';
			$item2['USR_MDD'] 			= 'simak'; 
			$item2['STATUS_USER'] 		= '1';
			$item2['F_AKTIF'] 			= '1';
			$item2['C_DATE']			= date('Y-m-d H:i:s');
			
			
			$this->um->addItem('masterdata.t_mst_user_login',$item2);
			
			$item3['USR'] 			= $data['master_data_user'];
			$item3['USR_FLG'] 		= '1';
			$item3['USR_SHR'] 		= '1';
			$item3['USR_UXP'] 		= '2030-01-01 00:00:00';
			$item3['USR_MDD'] 		= 'simak';
			$item3['USR_NAME'] 		= ucwords(strtolower($data['master_data_fullname']));
			$item3['USR_PASS'] 		= '';
			$item3['THE'] 			= '1';
			$item3['USR_C_DATE']	= date('Y-m-d H:i:s');
			
			$this->um->addItem('masterdata.vfs_users',$item3);  
			
			//t_tem_userlogin_igracias
			$item4['c_username'] 	= $this->input->post('master_data_user');
			$item4['rfid1'] 		= $this->input->post('rfid1');
			$item4['rfid2'] 		= $this->input->post('rfid2');  
			$item4['date_input'] 	= date('Y-m-d H:i:s');  
			$item4['c_status_user'] = $this->input->post('studenttypename'); 
			
			if($this->um->checkUserinTemUserLoginIgracias($data['master_data_user'])->row()){
				$where = "c_username='".$this->input->post('master_data_user')."'"; 
				$this->um->updateItem('masterdata.t_tem_userlogin_igracias',$item4,$where); 
			}
			else { 
				$this->um->addItem('masterdata.t_tem_userlogin_igracias',$item4); 
			} 
			
			$members = $this->um->checkstatus($this->input->post('master_data_user'),'')->row();
			
			$item5['log_username'] 		= $this->input->post('master_data_user');
			$item5['log_cookies'] 	 	= md5('Open'.$this->input->post('master_data_user').'Library'.strtotime(date('Y-m-d H:i:s')));
			$item5['log_status'] 		= 'login';  
			$item5['log_login_date'] 	= date('Y-m-d H:i:s');  
			$item5['log_session_id'] 	= $this->input->post('session_id');
			$item5['log_id'] 	= $members->id;
		 
			$this->um->addItem('batik.member_log',$item5); 
			 
			$this->response(array('status' => "success",'cookies' => $item5['log_cookies']), 200); 
			
		} 
		else if ($status=='update'){
			$data = array(  
				"updated_by" 				=> 'openlibrary',
				"updated_at" 				=> date("Y-m-d H:i:s")
			); 
			$data['master_data_email']  			= $this->input->post('master_data_email');
			$data['master_data_mobile_phone']  		= $this->input->post('master_data_mobile_phone');  
			$data['master_data_course']  			= $this->input->post('master_data_course');
			$data['master_data_number']  			= $this->input->post('master_data_number');
			$data['master_data_fullname']  			= $this->input->post('master_data_fullname');
			$data['rfid1']  						= $this->input->post('rfid1');
			$data['rfid2']  						= $this->input->post('rfid2'); 
			$data['master_data_lecturer_status']  	= $this->input->post('master_data_lecturer_status'); 
			$data['master_data_generation']  		= $this->input->post('master_data_generation'); 
			$data['master_data_photo']  			= $this->input->post('master_data_photo'); 
			$data['master_data_status']  			= $this->input->post('studenttypename'); 
			$data['master_data_nidn']  				= $this->input->post('master_data_nidn'); 
			 
			$session = array('usergroup' => 'superadmin', 'username' => $this->input->post('master_data_user'),'user_id' => '1','login' => true,'language' => 'ina');
			$this->session->set_userdata($session);  
			$where = "master_data_user='".$this->input->post('master_data_user')."'";
			
			// $where = "master_data_user='".$this->input->post('master_data_user')."' and (master_data_email!='".$this->input->post('master_data_email')."' or master_data_mobile_phone!='".$this->input->post('master_data_mobile_phone')."' or master_data_number!='".$this->input->post('master_data_number')."' or rfid1!='".$this->input->post('rfid1')."' or rfid2!='".$this->input->post('rfid2')."' or master_data_lecturer_status!='".$this->input->post('master_data_lecturer_status')."' or master_data_generation!='".$this->input->post('master_data_generation')."' or master_data_photo!='".$this->input->post('master_data_photo')."' or master_data_status!='".$this->input->post('master_data_status')."')";
			$member_id = $this->um->update($data,$where);
 
			
			//t_tem_userlogin_igracias
			$item4['c_username'] 	= $this->input->post('master_data_user');
			$item4['rfid1'] 		= $this->input->post('rfid1');
			$item4['rfid2'] 		= $this->input->post('rfid2');  
			$item4['date_input'] 	= date('Y-m-d H:i:s'); 
			$item4['c_status_user'] = $this->input->post('studenttypename'); 
			
			if($this->um->checkUserinTemUserLoginIgracias($this->input->post('master_data_user'))->row()){
				$where = "c_username='".$this->input->post('master_data_user')."'"; 
				$this->um->updateItem('masterdata.t_tem_userlogin_igracias',$item4,$where); 
			}
			else { 
				$this->um->addItem('masterdata.t_tem_userlogin_igracias',$item4); 
			}
			
			  
			$members = $this->um->checkstatus($this->input->post('master_data_user'),'')->row();
			$item5['log_username'] 	 = $this->input->post('master_data_user');
			$item5['log_cookies'] 	 = md5('Open'.$this->input->post('master_data_user').'Library'.strtotime(date('Y-m-d H:i:s')));
			$item5['log_status'] 	 = 'login';  
			$item5['log_login_date'] = date('Y-m-d H:i:s');  
			$item5['log_session_id'] 	= $this->input->post('session_id');
			$item5['log_id'] 	= $members->id;
			$this->um->addItem('batik.member_log',$item5); 
			
			$this->response(array('status' => "success",'cookies' => $item5['log_cookies']), 200); 
		}
		else if ($status=='checkmember'){
			$members = $this->um->getbymemberAll($this->input->post('username'),$this->input->post('password'))->row();
			if($members){ 
			
				$item5['log_username'] 	 = $this->input->post('username');
				$item5['log_cookies'] 	 = md5('Open'.$this->input->post('username').'Library'.strtotime(date('Y-m-d H:i:s')));
				$item5['log_status'] 	 = 'login';  
				$item5['log_login_date'] = date('Y-m-d H:i:s');  
				$item5['log_session_id'] 	= $this->input->post('session_id');
				$item5['log_id'] 	= $members->id;
				$this->um->addItem('batik.member_log',$item5);  
			
				$this->response(array('status' => "success"), 200); 
			}
			else 
				$this->response(array('status' => "failed"), 401); 
		}  
		else if ($status=='checkstatus'){
			
			// $this->response(array('status' => "success"), 401); 	
			if($this->um->checkstatus($this->input->post('username'),$this->input->post('password'))->row()){ 
				$this->response(array('status' => "success"), 200); 
			}
			else 
				$this->response(array('status' => "failed"), 401); 
		} 
		else if ($status=='logout'){ 
			$item5['log_status'] 		= 'logout';  
			$item5['log_logout_date'] 	= date('Y-m-d H:i:s');  
			$where = "log_username='".$this->input->post('username')."' and log_cookies='".$this->input->post('cookies')."'"; 
			$this->um->updateItem('batik.member_log',$item5,$where);
		} 
	
    } 
	
	   
}

?>