<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class Rfidsso extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config); 
		$this->load->model('Usermodel', 'um', TRUE);
    }
  
   
     function index_post() { 
	 
		// print_r($_POST);
		$status = $this->input->post('status');
		$data = $this->input->post('status');
		$username = $this->input->post('username');
		
		if($status=='insert_update'){
			if($this->um->checkstatus($username)->row()){ 
				//update
				
				$data = array(  
					"updated_by" 				=> 'openlibrary',
					"updated_at" 				=> date("Y-m-d H:i:s")
				); 
				 
				$data['master_data_email']  			= $this->input->post('mail');
				$data['master_data_mobile_phone']  		= $this->input->post('mobile'); 
				$data['master_data_fullname']  			= $this->input->post('displayname');
				$data['master_data_number']  			= $this->input->post('employeenumber'); 
				$data['master_data_institution']  		= $this->input->post('businesscategory'); 
				
				$where = "master_data_user='$username'";
				$member_id = $this->um->update($data,$where);
			}
			else {
				//insert
				 
				$data = array(  
					"member_type_id" 			=> '2', 
					"member_class_id" 			=> '2', 
					"status" 					=> "1",
					"created_by" 				=> 'library',
					"created_at" 				=> date("Y-m-d H:i:s")
				); 
			
				$data['master_data_user']  				= $this->input->post('username');
				$data['master_data_email']  			= $this->input->post('mail');
				$data['master_data_mobile_phone']  		= $this->input->post('mobile'); 
				$data['master_data_fullname']  			= $this->input->post('displayname');
				$data['master_data_number']  			= $this->input->post('employeenumber'); 
				$data['master_data_institution']  		= $this->input->post('businesscategory'); 
				
				$member_id = $this->um->add($data);  
			 
				//pegawai
				$item['C_NIP'] 							= $data['master_data_user'];
				$item['NAMA_PEGAWAI'] 					= $data['master_data_fullname']; 
				$item['NO_HP'] 							= $data['master_data_mobile_phone'];
				$item['EMAIL'] 							= $data['master_data_email'];
				$item['C_KODE_STATUS_PEGAWAI'] 			= 'P';
				$item['C_KODE_STATUS_AKTIF_PEGAWAI'] 	= 'A';
				$item['F_AKTIF'] 						= '1';
				$item['C_DATE']							= date('Y-m-d H:i:s');
				$item['C_USER']							= 'library'; 
				
				$this->um->addItem('masterdata.t_mst_pegawai',$item);  
				
				$item2['C_USERNAME'] 		= $data['master_data_user'];
				$item2['PASSWORD'] 			= '';
				$item2['PASSWORD_X'] 		= '';
				$item2['C_KODE_JENIS_USER'] = 'pegawai';
				$item2['USR_SHARING'] 		= '1';
				$item2['USR_THEME'] 		= '1';
				$item2['USR_EXPIRED'] 		= '2025-01-01 00:00:00';
				$item2['USR_MDD'] 			= 'simak'; 
				$item2['STATUS_USER'] 		= '1';
				$item2['F_AKTIF'] 			= '1';
				$item2['C_DATE']			= date('Y-m-d H:i:s'); 
				
				$this->um->addItem('masterdata.t_mst_user_login',$item2);
				
				$item3['USR'] 			= $data['master_data_user'];
				$item3['USR_FLG'] 		= '1';
				$item3['USR_SHR'] 		= '1';
				$item3['USR_UXP'] 		= '2025-01-01 00:00:00';
				$item3['USR_MDD'] 		= 'simak';
				$item3['USR_NAME'] 		= ucwords(strtolower($data['master_data_fullname']));
				$item3['USR_PASS'] 		= '';
				$item3['THE'] 			= '1';
				$item3['USR_C_DATE']	= date('Y-m-d H:i:s');
				
				$this->um->addItem('masterdata.vfs_users',$item3);  
				
			} 
			 
			$this->response(array('status' => "success"), 200);
			
		}  
		else if ($status=='checkmember'){
			if($this->um->getbymemberAll($this->input->post('username'),$this->input->post('password'))->row()){ 
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