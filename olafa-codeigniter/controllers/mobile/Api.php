<?php

require APPPATH . '/libraries/REST_Controller.php';
  
class Api extends REST_Controller {
  
    function __construct($config = 'rest') {
        parent::__construct($config); 
		$this->load->model('ApiMobileModel', 'api', TRUE); 
		$this->load->model('Usermodel', 'um', TRUE);
    }  
   
	function login_post() { 
		$public_key = $this->input->post('public_key'); 
		 
		   
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		
		$username = strtolower($this->input->post('username'));
		$password = $this->input->post('password');
			
		$sso 	  = SSO($username,$password);  
		if($sso=='false'){
			$dt = $this->api->getbymember($username,md5($password))->row();
			
		}
		else {  
			$dt = $this->api->checkuser($username)->row(); 
		}
		
		if($dt){   
			$token = $this->uuid->v4();
			$datas['master_data_uuid'] 	= $token;
			$where = "master_data_user= '".$dt->master_data_user."'";
		
			$member_id = $this->api->update($datas,$where);
			
			$civitas = $this->civitas_array(); 
		 
			if(in_array($dt->member_type_id,$civitas)){ 
				$data['class'] = 'civitas';
			} 
			else if($dt->member_type_id=='23') {
				$data['class'] = 'public_premium';
			}
			else $data['class'] = 'public_general'; 

			//sementara
			// $data['class'] = 'civitas';
			
			if($dt->master_data_photo=="") $photo = 'https://openlibrary.telkomuniversity.ac.id/images/default_photo_profile.png';
			else $photo = $dt->master_data_photo;
			
			$data['private_key'] 	= $token; 
			$data['photo'] 			= $photo;
			$data['username'] 		= $dt->master_data_user;
			$data['fullname'] 		= $dt->master_data_fullname;
			$data['email'] 			= $dt->master_data_email;
			$data['cardid'] 		= $dt->master_data_number;
			$data['mobilephone'] 	= $dt->master_data_mobile_phone;  
			$data['membertype'] 	= $dt->typename; 
			$data['prodi'] 			= $dt->nama_prodi; 
			
			$this->response(array('status' => "true",'message'=>$data), 200); 
		} 
		else $this->response(array('status' => "false",'message'=>'Username / Password is Wrong'), 200); 
	}

	function login2_post() { 
		$public_key = $this->input->post('public_key'); 
		 
		   
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		
		$username = strtolower($this->input->post('username'));
		$password = $this->input->post('password');
			
		$sso 	  = SSO($username,$password);  
		if($sso=='false'){
			$dt = $this->api->getbymember($username,md5($password))->row();
			
		}
		else {  
			$dt = $this->api->checkuser($username)->row(); 
		}
		
		if($dt){   
			$token = $this->uuid->v4();
			$datas['master_data_uuid'] 	= $token;
			$where = "master_data_user= '".$dt->master_data_user."'";
		
			$member_id = $this->api->update($datas,$where);
			
			$civitas = $this->civitas_array(); 
		 
			if(in_array($dt->member_type_id,$civitas)){ 
				$data['class'] = 'civitas';
			} 
			else if($dt->member_type_id=='23') {
				$data['class'] = 'public_premium';
			}
			else $data['class'] = 'public_general'; 

			//sementara
			// $data['class'] = 'civitas';
			
			if($dt->master_data_photo=="") $photo = 'https://openlibrary.telkomuniversity.ac.id/images/default_photo_profile.png';
			else $photo = $dt->master_data_photo;
			
			$data['private_key'] 	= $token; 
			$data['photo'] 			= $photo;
			$data['username'] 		= $dt->master_data_user;
			$data['fullname'] 		= $dt->master_data_fullname;
			$data['email'] 			= $dt->master_data_email;
			$data['cardid'] 		= $dt->master_data_number;
			$data['mobilephone'] 	= $dt->master_data_mobile_phone;  
			$data['membertype'] 	= $dt->typename; 
			$data['prodi'] 			= $dt->nama_prodi; 
			
			$this->response(array('status' => "true",'message'=>$data), 200); 
		} 
		else { 
			if($sso!='false'){
				$member = $sso;

				if($member['c_kode_jenis_user']=='pegawai'){
					$fields = array(
						'c_kode_jenis_user' 			=> $member['c_kode_jenis_user'],
						'status' 						=> $status,  
						'member_type_id' 				=> ($member['isdosen']!='NO'?'4':'7'),
						'master_data_user' 				=> $member['username'],
						'master_data_email' 			=> $member['email'],
						'master_data_mobile_phone' 		=> $member['hp'],
						'master_data_faculty' 			=> ($member['c_kode_fakultas']!='-'?$member['c_kode_fakultas']:''),
						'master_data_course' 			=> ($member['c_kode_prodi']!='-'?$member['c_kode_prodi']:''),
						'master_data_fullname' 			=> $member['name'],
						'master_data_number' 			=> $member['employeeid'],
						'rfid1' 						=> $member['rfid1'],
						'rfid2' 						=> $member['rfid2'],
						'studenttypename' 				=> $member['studenttypename'],
						'master_data_lecturer_status' 	=> $member['isdosen'],
						'master_data_generation' 		=> $member['angkatan'],
						'master_data_photo' 			=> $member['photourl'],
						'master_data_nidn' 				=> $member['nidn']
					); 
				}  
				else {    
					$type = "";
					if($member['studyprogramname'])
						
					if (strpos($member['studyprogramname'], 'D3') !== false) {
						$type="6";
					}
					else if (strpos($member['studyprogramname'], 'D4') !== false) {
						$type="6";
					}
					else if (strpos($member['studyprogramname'], 'S1') !== false) {
						$type="5";
					}
					else  if (strpos($member['studyprogramname'], 'S2') !== false) {
						$type="10";
					}
					else  if (strpos($member['studyprogramname'], 'S3') !== false) {
						$type="25";
					}
					
					$fields = array(
						'c_kode_jenis_user' 			=> $member['c_kode_jenis_user'],
						'status' 						=> $status,
						'member_type_id' 				=> $type,
						'master_data_user' 				=> $member['username'],
						'master_data_email' 			=> $member['email'],
						'master_data_mobile_phone' 		=> $member['hp'],
						'master_data_faculty' 			=> $member['facultyid'],
						'master_data_course' 			=> $member['studyprogramid'],
						'master_data_fullname' 			=> $member['name'],
						'master_data_number' 			=> $member['studentid'],
						'rfid1' 						=> $member['rfid1'],
						'rfid2' 						=> $member['rfid2'],
						'studenttypename' 				=> $member['studenttypename'],
						'master_data_lecturer_status' 	=> '',
						'master_data_generation' 		=> $member['angkatan'],
						'master_data_photo' 			=> $member['photourl']
					); 
				}
			
			
				$data = array(  
					"member_class_id" 			=> '2', 
					"status" 					=> "1",
					"created_by" 				=> 'openlibrary',
					"created_at" 				=> date("Y-m-d H:i:s")
				);
				
				$data['member_type_id'] 				= $fields['member_type_id'];
				$data['master_data_user']  				= $fields['master_data_user'];
				$data['master_data_email']  			= $fields['master_data_email'];
				$data['master_data_mobile_phone']  		= $fields['master_data_mobile_phone'];
				$data['master_data_course']  			= $fields['master_data_course'];
				$data['master_data_fullname']  			= $fields['master_data_fullname'];
				$data['master_data_number']  			= $fields['master_data_number'];
				$data['rfid1']  						= $fields['rfid1'];
				$data['rfid2']  						= $fields['rfid2']; 
				$data['master_data_lecturer_status']  	= $fields['master_data_lecturer_status']; 
				$data['master_data_generation']  		= $fields['master_data_generation']; 
				$data['master_data_photo']  			= $fields['master_data_photo']; 
				$data['master_data_status']  			= $fields['studenttypename']; 
				$data['master_data_nidn']  				= $fields['master_data_nidn']; 
				
				$member_id = $this->um->add($data);  
				
				if($fields['c_kode_jenis_user']=='pegawai'){
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
					$item['C_KODE_FAKULTAS'] 				= $fields['master_data_faculty'];
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
				$item2['C_KODE_JENIS_USER'] = ($fields['c_kode_jenis_user']=='pegawai'?'pegawai':'mahasiswa');
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
				$item4['c_username'] 	= $fields['master_data_user'];
				$item4['rfid1'] 		= $fields['rfid1'];
				$item4['rfid2'] 		= $fields['rfid2'];  
				$item4['date_input'] 	= date('Y-m-d H:i:s');  
				$item4['c_status_user'] = $fields['studenttypename']; 
				
				if($this->um->checkUserinTemUserLoginIgracias($data['master_data_user'])->row()){
					$where = "c_username='".$fields['master_data_user']."'"; 
					$this->um->updateItem('masterdata.t_tem_userlogin_igracias',$item4,$where); 
				}
				else { 
					$this->um->addItem('masterdata.t_tem_userlogin_igracias',$item4); 
				} 
				
				$members = $this->um->checkstatus($fields['master_data_user'],'')->row();
				
				$item5['log_username'] 		= $fields['master_data_user'];
				$item5['log_cookies'] 	 	= md5('Open'.$fields['master_data_user'].'Library'.strtotime(date('Y-m-d H:i:s')));
				$item5['log_status'] 		= 'login';  
				$item5['log_login_date'] 	= date('Y-m-d H:i:s');  
				$item5['log_session_id'] 	= $fields['session_id'];
				$item5['log_id'] 	= $members->id;
			
				$this->um->addItem('batik.member_log',$item5); 

				$dt = $this->api->checkuser($username)->row(); 
				if($dt){   
					$token = $this->uuid->v4();
					$datas['master_data_uuid'] 	= $token;
					$where = "master_data_user= '".$dt->master_data_user."'";
				
					$member_id = $this->api->update($datas,$where);
					
					$civitas = $this->civitas_array(); 
				
					if(in_array($dt->member_type_id,$civitas)){ 
						$data_new['class'] = 'civitas';
					} 
					else if($dt->member_type_id=='23') {
						$data_new['class'] = 'public_premium';
					}
					else $data_new['class'] = 'public_general'; 
		
					//sementara
					// $data['class'] = 'civitas';
					
					if($dt->master_data_photo=="") $photo = 'https://openlibrary.telkomuniversity.ac.id/images/default_photo_profile.png';
					else $photo = $dt->master_data_photo;
					
					$data_new['private_key'] 	= $token; 
					$data_new['photo'] 			= $photo;
					$data_new['username'] 		= $dt->master_data_user;
					$data_new['fullname'] 		= $dt->master_data_fullname;
					$data_new['email'] 			= $dt->master_data_email;
					$data_new['cardid'] 		= $dt->master_data_number;
					$data_new['mobilephone'] 	= $dt->master_data_mobile_phone;  
					$data_new['membertype'] 	= $dt->typename; 
					$data_new['prodi'] 			= $dt->nama_prodi; 
					
					$this->response(array('status' => "true",'message'=>$data_new), 200); 
				} 
			}
			else $this->response(array('status' => "false",'message'=>'Username / Password is Wrong'), 200); 
		}
		
	}

	

	function notif_token_post() {
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');   
		  
		$token = $this->input->post('token');
  
		$dt = $this->api->getbymemberUuid($private_key)->row();
		  
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200);   

		$datas['master_data_token'] 	= $token;
		$where = "master_data_user= '".$dt->master_data_user."'";
	
		$member_id = $this->api->update($datas,$where); 
  		
		$this->response(array('status' => "true",'message'=>'success'), 200); 		
	} 

	
   
	function generate_post() { 
		$member_id = $this->input->post('member_id');  
		   
		  
		$rent = $this->api->getGenerateMember($member_id)->result();
		
		if($rent){    
			foreach($rent as $key => $row){ 
				$data['data'][$key]['code'] = $row->code; 
				$data['data'][$key]['title'] = $row->title;
				$data['data'][$key]['author'] = $row->author;
				$data['data'][$key]['rent_date'] = $row->rent_date;
				$data['data'][$key]['return_date'] = $row->return_date;
				$data['data'][$key]['return_date_expected'] = $row->return_date_expected;
				$data['data'][$key]['penalty_per_day'] = $row->penalty_per_day;
				$data['data'][$key]['penalty_total'] = $row->penalty_total; 
				
				if($row->status=='1'){
					//hanya biaya penalty tidak 0 yg dihitung penaltynya & yg masih status pinjam / 1
					if($row->penalty_per_day!=0) $data['data'][$key]['penalty_total'] = $this->calculatepenalty($row->id,$row->return_date_expected,$row->penalty_per_day,$row->member_id); 
				}
				// break;
			} 
		} 

		$this->response(array('status' => "true",'message'=>$data), 200); 		
	}
   
	function rent_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');   
		
		$limit = $this->input->post('limit'); 
		$page = $this->input->post('page');  
		$typeid = $this->input->post('typeid');
 
		$dt = $this->api->getbymemberUuid($private_key)->row();
		  
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200);  
		if($limit=="" or (int)$limit<1) $limit = 10;
		if($page=="" or (int)$page<1) $page = 1;
		
	  
		$offset = ($page-1)*$limit; 
 
		
		$rent = $this->api->getRentIdMemberFilter($dt->memberid,$limit,$offset,$typeid)->result();
		$total = $this->api->getRentIdMember($dt->memberid,$typeid)->row();
 
		$data['total']['all'] = $total->total;
		$data['page'] = $page;
		if($total->total==0){
			$data['total']['book_rent'] = 0;
		}
		$data['data'] = array();
		  
		if($rent){   
		
			$status = array('1'=>'Dipinjam','2'=>'Dikembalikan','3'=>'Rusak','4'=>'Hilang');
 
			foreach($rent as $key => $row){
				$data['data'][$key]['rent_id'] = $row->id;
				$data['data'][$key]['status'] = $status[$row->status];
				$data['data'][$key]['code'] = $row->code;
				$data['data'][$key]['title'] = $row->title;
				$data['data'][$key]['author'] = $row->author;
				$data['data'][$key]['rent_date'] = $row->rent_date;
				$data['data'][$key]['return_date'] = $row->return_date;
				$data['data'][$key]['return_date_expected'] = $row->return_date_expected;
				$data['data'][$key]['penalty_per_day'] = $row->penalty_per_day;
				$data['data'][$key]['penalty_total'] = $row->penalty_total; 
				$data['data'][$key]['extension_total'] = $row->extended_count;
				$data['data'][$key]['extension_status'] = 'Tidak';
				
				if($row->status=='1'){
					if($row->extended_count<$row->rent_extension_count and $row->return_date_expected>=date('Y-m-d', strtotime('+7 hours'))){
						$data['data'][$key]['extension_status'] = 'Ya'; 
					}

					//hanya biaya penalty tidak 0 yg dihitung penaltynya & yg masih status pinjam / 1
					if($row->penalty_per_day!=0) $data['data'][$key]['penalty_total'] = $this->calculatepenalty($row->id,$row->return_date_expected,$row->penalty_per_day,$row->member_id); 
				}
				// break;
			} 
		} 

		$this->response(array('status' => "true",'message'=>$data), 200); 		
	}   

	function rent_extension_post() {
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');   
		 
		$id = $this->input->post('id');
  
		$dt = $this->api->getbymemberUuid($private_key)->row();
		  
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200);   
 
		
		$rent = $this->api->getRentIdFilter($id)->row(); 
	
		if($rent->extended_count<$rent->rent_extension_count and $rent->return_date_expected>=date('Y-m-d', strtotime('+7 hours'))){  
			
			$inp['extended_count'] 		= $rent->extended_count + 1;
			$inp['extended_from_date'] 		= date('Y-m-d');
  
			$extend_to_date = date('Y-m-d', strtotime("+{$rent->rent_extension_day} day", time())); 
			 
			$return_date = $this->calculateReturnDateExpected($extend_to_date);
				
			$inp['return_date_expected'] 	= $return_date;
			$inp['extended_to_date'] 		= $return_date;
 
			$this->api->editCustom('id', 'rent', $id, $inp);  

			$messages 		= "Anda telah memperpanjang buku dengan kode ".$rent->code.".";

			$itemnotif['notif_id_member'] 	= $dt->master_data_user;
			$itemnotif['notif_type'] 	= 'perpanjangan';
			$itemnotif['notif_content'] 	= $messages;
			$itemnotif['notif_date'] 	= date('Y-m-d H:i:s', strtotime('+7 hour'));
			$itemnotif['notif_status'] 	= 'unread';
			$itemnotif['notif_id_detail'] 	= $id;

			$this->api->add_custom($itemnotif,'notification_mobile');
			
			$this->response(array('status' => "true",'message'=>'Berhasil'), 200); 
		}
		else $this->response(array('status' => "false",'message'=>'sudah melewati batas waktu pengembalian / sudah melewati jumlah perpanjangan ('.$rent->rent_extension_count.' kali)'), 200);  
  		
	} 

	function calculateReturnDateExpected($return_date, $current_date = null){ 
		if ($current_date == null) $current_date = date('Y-m-d');
		$holidays_on_rent = $this->api->doCountHolidayBetween($current_date, $return_date);
		$next_return_date = $return_date;
 
		// echo $holidays_on_rent." ".$current_date." ".$return_date."\n";
		while ($holidays_on_rent > 0) {
		  $next_return_date = date('Y-m-d', strtotime("+{$holidays_on_rent} day", strtotime($return_date)));
		  $holidays_on_rent = $this->api->doCountHolidayBetween($return_date, $next_return_date); 
		  $return_date = $next_return_date;
		//   echo $holidays_on_rent." ".$current_date." ".$return_date."\n";
		}
		return $return_date;
	}
   
	function payment_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');   
		
		$limit = $this->input->post('limit'); 
		$page = $this->input->post('page');  
 
		$dt = $this->api->getbymemberUuid($private_key)->row();
		  
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200);  
		if($limit=="" or (int)$limit<1) $limit = 10;
		if($page=="" or (int)$page<1) $page = 1;
		
		$offset = ($page-1)*$limit;
		
		$total = $this->api->getRentPenaltyPayment($dt->memberid)->row();
 
		$data['total']['all'] = $total->total; 
		$data['page'] = $page;
		$data['data'] = $this->api->getRentPenaltyPaymentFilter($dt->memberid,$limit,$offset)->result();
		  
		$this->response(array('status' => "true",'message'=>$data), 200); 		
		
		 
	}  

	function digital(){
		return '4,5,6,21,24,25,47,49,51,52,55,62,70,73,75,79';
	}

	function civitas(){
		return '1,2,3,4,5,6,7,9,10,25';
	}

	function pegawai_dosen(){
		return '2,3,4,7';
	}

	function mahasiswa(){
		return '5,6,9,10,25';
	}

	function civitas_array(){
		return array(1,2,3,4,5,6,7,9,10,25);
	}

	function location_post() { 
		$public_key = $this->input->post('public_key');     ;
 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 

		$rent = $this->api->getLocation()->result(); 
		
		if($rent){      
			foreach($rent as $key => $row){  
				$data['data']['location'][$key]['id']= $row->id;
				$data['data']['location'][$key]['name']= $row->name;
				$data['data']['location'][$key]['phone']= $row->phone;
				$data['data']['location'][$key]['fax']= $row->fax;
				$data['data']['location'][$key]['email']= $row->email;
				$data['data']['location'][$key]['address']= $row->address;
				$data['data']['location'][$key]['open_mon']= $row->open_mon;
				$data['data']['location'][$key]['open_tue']= $row->open_tue;
				$data['data']['location'][$key]['open_wed']= $row->open_wed;
				$data['data']['location'][$key]['open_thu']= $row->open_thu;
				$data['data']['location'][$key]['open_fri']= $row->open_fri;
				$data['data']['location'][$key]['open_sat']= $row->open_sat;
				$data['data']['location'][$key]['open_sun']= $row->open_sun; 
				$data['data']['location'][$key]['npp']= '3204122D0000002';
			} 
		}   

		$this->response(array('status' => "true",'message'=>$data), 200);  
	}  

	function fisik_type_post() { 
		$public_key = $this->input->post('public_key');     ;
 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200);  

		$type = $this->digital(); 
		$fisik = $this->api->getKnowledgeType('fisik',$type)->result();   
		if($fisik){      
			foreach($fisik as $key => $row){  
				$data['data']['type_fisik'][$key]['id']= $row->id;
				$data['data']['type_fisik'][$key]['name']= $row->name; 
			} 
		}  
		  

		$this->response(array('status' => "true",'message'=>$data), 200); 	 
	}

	function digital_type_post() { 
		$public_key = $this->input->post('public_key');     ;
 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200);  

		
		$type = $this->digital();  
		$digital = $this->api->getKnowledgeType('digital',$type)->result();  

		if($digital){      
			foreach($digital as $key => $row){  
				$data['data']['type_digital'][$key]['id']= $row->id;
				$data['data']['type_digital'][$key]['name']= $row->name; 
			} 
		} 

		$this->response(array('status' => "true",'message'=>$data), 200); 	 
	}

	function dashboard_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');    
 
		$dt = $this->api->getbymemberUuid($private_key)->row();
 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if($dt) {  
			
			$rent = $this->api->getRentNotYetReturnIdMember($dt->memberid)->result();
		
			$data['data']['book_rent'] = 0;
			
			if($rent){  
				// echo count($rent);   
				$data['data']['book_rent'] = count($rent);
		 
				foreach($rent as $key => $row){  
					if($row->penalty_per_day!=0) $this->calculatepenalty($row->id,$row->return_date_expected,$row->penalty_per_day,$row->member_id);  
				} 
			} 
			
			$penalty = $this->api->getRemainingPenalty($dt->memberid)->row();
			$data['data']['remaining_penalty'] = $penalty->penalty - $penalty->payment; 
		} 
		  
		$catalog = $this->api->getCatalogDashboard()->row();
		$eksemplar = $this->api->getEksemplar()->row(); 

		$notification = $this->api->getNotification($dt->master_data_user)->row();
		
		$data['data']['catalog'] = $catalog->total;
		$data['data']['notification'] = $notification->unread;
		$data['data']['eksemplar'] = $eksemplar->total;    

		$this->response(array('status' => "true",'message'=>$data), 200); 		
	} 

	function status_post() { 
		$public_key = $this->input->post('public_key');  
 
		// $dt = $this->api->getbymemberUuid($private_key)->row();
 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		 

		$type = $this->digital();
 

		$digital = $this->api->getKnowledgeType('digital',$type)->result(); 
		$fisik = $this->api->getKnowledgeType('fisik',$type)->result();  
		$karyailmiah = $this->api->getKaryaIlmiahStatus()->result(); 

		if($digital){      
			foreach($digital as $key => $row){  
				$data['data']['type_digital'][$key]['id']= $row->id;
				$data['data']['type_digital'][$key]['name']= $row->name; 
			} 
		} 

		if($fisik){      
			foreach($fisik as $key => $row){  
				$data['data']['type_fisik'][$key]['id']= $row->id;
				$data['data']['type_fisik'][$key]['name']= $row->name; 
			} 
		} 

		if($karyailmiah){      
			foreach($karyailmiah as $key => $row){  
				$data['data']['karyailmiah'][$key]['id']= $row->id;
				$data['data']['karyailmiah'][$key]['name']= $row->name; 
			} 
		} 

		$karyailmiah_general = array('Semua','Approved','Review','Revision','On Draft');
		foreach($karyailmiah_general as $key => $row){   
			$data['data']['karyailmiah_general'][$key]['name']= $row; 
		}   

		$ruangan = array('Semua','Request','Approved','Not Approved','Attend','Not Attend','Cancel');
		foreach($ruangan as $key => $row){   
			$data['data']['ruangan'][$key]['name']= $row; 
		}   

		$bahanpustaka = array('Semua','Request','Approved','Not Approved','Process','Completed');
		foreach($bahanpustaka as $key => $row){   
			$data['data']['bahanpustaka'][$key]['name']= $row; 
		}   
 
		$bds = array('Semua','Request','Approved','Not Approved','Process','Send','Received','Completed');
		foreach($bds as $key => $row){   
			$data['data']['bds'][$key]['name']= $row; 
		}  

		$this->response(array('status' => "true",'message'=>$data), 200); 		
	} 

	function reminder_h1_get() { 
		$date = date('Y-m-d 05:00:00');
		$dt = $this->api->getScheduler($date)->row();
		if($dt->total==0){
			$this->api->insertScheduler($date);

			$datetime = new DateTime('tomorrow');
			
			$dts = $this->api->reminder_H_1()->result();
			// print_r($dts);
			foreach($dts as $row){  

				$messages 	 = "Hai ".$row->master_data_fullname.", Openlibrary menginfokan bahwa besok tanggal ".$datetime->format('d-m-Y')." adalah batas akhir peminjaman buku dengan kode ".$row->code.".\n\nSilahkan melakukan pengembalian buku atau perpanjangan peminjaman buku sebelum melewati batas akhir peminjaman buku agar terhindar dari denda keterlambatan.\n\nTerimakasih";

				$itemnotif['notif_id_member'] 	= $row->master_data_user;
				$itemnotif['notif_type'] 	= 'reminder H-1';
				$itemnotif['notif_content'] 	= $messages;
				$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
				$itemnotif['notif_status'] 	= 'unread';
				$itemnotif['notif_id_detail'] 	= $row->id;

				// print_r($itemnotif);
				$notif_id = $this->api->add_custom($itemnotif,'notification_mobile'); 

				$token = $this->api->getTokenNotificationMobile($row->memberid)->row();

				$title = "Reminder H-1 Pengembalian Buku";  
				$notif_content = $messages;
				$notif_id_detail = $row->id;
				$notif_type = 'sirkulasi';
				$token = $token->master_data_token;

				$messages = str_replace("\n","<br>",$messages);
 
				SendEmail($row->master_data_email,$title,$messages."<br><br><br><br><br>",'Telkom University Open Library',ucwords(strtolower($row->master_data_fullname))); 

				NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);   
			} 

			$dts = $this->api->reminder_H()->result();
			// print_r($dts);
			foreach($dts as $row){  

				$messages 	 = "Hai ".$row->master_data_fullname.", Openlibrary menginfokan bahwa hari ini tanggal ".date('d-m-Y')." adalah batas akhir peminjaman buku dengan kode ".$row->code.".\n\nSilahkan melakukan pengembalian buku atau perpanjangan peminjaman buku sebelum melewati batas akhir peminjaman buku agar terhindar dari denda keterlambatan.\n\nTerimakasih";

				$itemnotif['notif_id_member'] 	= $row->master_data_user;
				$itemnotif['notif_type'] 	= 'reminder Hari H';
				$itemnotif['notif_content'] 	= $messages;
				$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
				$itemnotif['notif_status'] 	= 'unread';
				$itemnotif['notif_id_detail'] 	= $row->id;

				// print_r($itemnotif);
				$notif_id = $this->api->add_custom($itemnotif,'notification_mobile'); 

				$token = $this->api->getTokenNotificationMobile($row->memberid)->row();

				$title = "Reminder Hari H Pengembalian Buku";  
				$notif_content = $messages;
				$notif_id_detail = $row->id;
				$notif_type = 'sirkulasi';
				$token = $token->master_data_token; 

				$messages = str_replace("\n","<br>",$messages);
 
				SendEmail($row->master_data_email,$title,$messages."<br><br><br><br><br>",'Telkom University Open Library',ucwords(strtolower($row->master_data_fullname))); 

				NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);   
			} 
		} 
		$this->response(array('status' => "true",'message'=>'OK'), 200); 	
	} 

	function reminder_peminjaman_buku_get() {
		$date = date('Y-m-01 05:05:00');
		$dt = $this->api->getScheduler($date)->row();
		if($dt->total==0){
			$this->api->insertScheduler($date);
 
			$messages 	 = "Hai TelUtizen\n\nOpenlibrary mengingatkan kepada TelUtizen yang sedang meminjam buku, untuk mengecek tanggal maksimal pengembalian buku secara berkala agar TelUtizen terhindar dari denda keterlambatan\n\nJika TelUtizen tidak ada peminjaman buku dapat mengabaikan pesan ini.\n\nTerimakasih";
 
			$title = "Reminder Peminjaman Buku";  
			$notif_content = $messages;
			$notif_id_detail = '1';
			$notif_type = 'sirkulasi'; 
  
			NotificationMobileBroadcast($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$title); 

			
			
			
		} 
		$this->response(array('status' => "true",'message'=>'OK'), 200); 	
	}

  function send_personal_notification_post(){
    
		$public_key = $this->input->post('public_key'); 
    if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 

    
		$notif_type = $this->input->post('notif_type'); 
		$title = $this->input->post('title');
		$username = $this->input->post('username'); 
		$message = $this->input->post('message');

    
    $itemnotif['notif_id_member'] 	= $username;
    $itemnotif['notif_type'] 	= $notif_type;
    $itemnotif['notif_content'] 	= $message;
    $itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
    $itemnotif['notif_status'] 	= 'unread';
    $itemnotif['notif_id_detail'] 	= '1';

    $member = $this->api->getMemberByUsername($username)->row();

    $notif_id = $this->api->add_custom($itemnotif,'notification_mobile'); 
    

    $token = $this->api->getTokenNotificationMobile($member->id)->row();


    $notif_content = $message;
    $notif_id_detail = '1';
    $notif_type = $notif_type;
    $token = $token->master_data_token;



    NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);  


		$this->response(array('status' => "true",'message'=>'sudah berhasil'), 200); 		
  }

  function send_notification_broadcast_get($username,$title,$notif_content){ 

	
    $member = $this->api->getMemberByUsername($username)->row();


	$notif_id_detail = '1';
	$notif_id = '1';
	$notif_type = 'broadcast'; 

    
	if($member->member_type_id=='1'){
		// NotificationMobileBroadcast($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$title); 
		
		$this->response(array('status' => "true",'message'=>'berhasil'), 200); 	
	}
	else 
	$this->response(array('status' => "false",'message'=>'gagal'), 200); 	

  }

	function notification_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');   
		
		$limit = $this->input->post('limit'); 
		$page = $this->input->post('page');  
 
		$dt = $this->api->getbymemberUuid($private_key)->row();
 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200);  
		if($limit=="" or (int)$limit<1) $limit = 10;
		if($page=="" or (int)$page<1) $page = 1; 
		
		$offset = ($page-1)*$limit;
 
		$notif = $this->api->getNotificationFilter($limit,$offset,$dt->master_data_user)->result();
		$total = $this->api->getNotification($dt->master_data_user)->row();

		$data['total']['all'] = $total->total;
		$data['total']['unread'] = $total->unread;
		$data['page'] = $page;
		 
		$data['data'] = array(); 
		   
		if($notif){      
			
			foreach($notif as $key => $row){  
				$data['data'][$key]['notif_id'] = $row->notif_id;
				$data['data'][$key]['notif_type'] = $row->type; 
				$data['data'][$key]['notif_content'] = $row->notif_content; 
				$data['data'][$key]['notif_date'] = $row->notif_date; 
				$data['data'][$key]['notif_status'] = $row->notif_status; 
				$data['data'][$key]['notif_id_detail'] = $row->notif_id_detail; 
			} 
		} 

		$this->response(array('status' => "true",'message'=>$data), 200); 		
	} 

	function notification_ketersediaan_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');    
		$id = $this->input->post('id');    
 
		$dt = $this->api->getbymemberUuid($private_key)->row();
 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200);   

		$data['member_id'] = $dt->memberid;
		$data['knowledge_item_id'] = $id;
		$data['order_at'] = date('Y-m-d H:i:s');
		$data['status'] = '1';
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $dt->master_data_user;
 
		$this->api->addNotifikasiKetersediaan($data); 
 

		$this->response(array('status' => "true",'message'=>'success'), 200); 		
	}  

	function notification_read_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');    
		$id = $this->input->post('id');    
 
		$dt = $this->api->getbymemberUuid($private_key)->row();
 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200);   
 
		$data['notif_status'] = 'read'; 
 
		$this->api->setNotifikasiRead($id,$data); 
 

		$this->response(array('status' => "true",'message'=>'success'), 200); 		
	} 

	function faculty_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');     
 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200);   
 
		$dt = $this->api->getFaculty()->result_array();
	 
		$dt[count($dt)] = array("c_kode_fakultas"=>"0","nama_fakultas"=>"LAINNYA");

		$this->response(array('status' => "true",'message'=>$dt), 200); 	 	
	}

	function prodi_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');    
		$faculty_id = $this->input->post('faculty_id');     
 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200);   
 
		$dt = $this->api->getProdi($faculty_id)->result_array();
		$dt[count($dt)] = array("c_kode_prodi"=>"0","nama_prodi"=>"LAINNYA");

		$this->response(array('status' => "true",'message'=>$dt), 200); 		
	}

	function news_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');   
		
		$limit = $this->input->post('limit'); 
		$page = $this->input->post('page');  
		$search = $this->input->post('search');  
		$important = $this->input->post('important');  
 
		$dt = $this->api->getbymemberUuid($private_key)->row();
		  
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if($important=='') $this->response(array('status' => "false",'message'=>'Important Status is empty'), 200);

		if(!$dt) $status = "and member_only='0'";
		else $status = "";

		if($limit=="" or (int)$limit<1) $limit = 10;
		if($page=="" or (int)$page<1) $page = 1;
		
		$offset = ($page-1)*$limit;

		// if($important=='Y'){ 
		// 	$status .=" and featured='1'";
		// }
		// else 
		// $status .=" and featured='0'";

		$where = "";
		if($search!=""){ 
			$where[] = "title like '%$search%'"; 
		} 
		
		$news = $this->api->getNewsFilter($limit,$offset,$status,$where)->result();
		$total = $this->api->getNews($status,$where)->row();
 
		$data['total']['all'] = $total->total;
		$data['page'] = $page;
		 
		$data['data'] = array(); 
		   
		if($news){      
			
			foreach($news as $key => $row){  
				$data['data'][$key]['id'] = $row->id;
				$data['data'][$key]['title'] = $row->title; 
				$data['data'][$key]['created_by'] = $row->created_by; 
				$data['data'][$key]['created_at'] = $row->created_at; 
			} 
		} 

		$this->response(array('status' => "true",'message'=>$data), 200); 		
	} 

	

	function news_detail_post() { 
		$public_key = $this->input->post('public_key');   
		$id = $this->input->post('id');   
		  
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if($id=="") $this->response(array('status' => "false",'message'=>'Id is empty'), 200);   
	 
 
	 
		
		$row = $this->api->getNewsDetail($id)->row();  
 
		// $data['total']['all'] = $total->total;
		 
		$data['data'] = array(); 
		   
		if($row){      
			$key = 0; 
			$data['data'][$key]['id'] = $row->id;
			$data['data'][$key]['title'] = $row->title; 
			$data['data'][$key]['content'] = str_replace('href="/home/information/id/','href="https://openlibrary.telkomuniversity.ac.id//home/information/id/',$row->content); 
			$data['data'][$key]['created_by'] = $row->created_by; 
			$data['data'][$key]['created_at'] = $row->created_at;  
		} 

		$this->response(array('status' => "true",'message'=>$data), 200); 		
	}

	function layanan_lain_post() { 
		$public_key = $this->input->post('public_key');   
		$id = 246;   
		   
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if($id=="") $this->response(array('status' => "false",'message'=>'Id is empty'), 200);   
	 
 
	 
		
		$row = $this->api->getNewsDetail($id)->row();  
		 
		$data['data'] = array(); 
		   
		if($row){       
			$key = 0; 
			// $data['data'][$key]['id'] = $row->id;
			$data['data'][$key]['title'] = $row->title; 
			
			$data['data'][$key]['content'] = str_replace('href="/information/','href="https://openlibrary.telkomuniversity.ac.id/home/information/id/',$row->content); 
			// $data['data'][$key]['content'] = $row->content; 
			// $data['data'][$key]['created_by'] = $row->created_by; 
			// $data['data'][$key]['created_at'] = $row->created_at;  
		} 

		$this->response(array('status' => "true",'message'=>$data), 200); 		
	}
   
	function catalog_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');   
		
		$limit = $this->input->post('limit'); 
		$page = $this->input->post('page');  
		$search = $this->input->post('search');  
		$type = $this->input->post('type'); 
		$typeid = $this->input->post('typeid'); 
		$location = $this->input->post('location');  
		$title = $this->input->post('title');  
		$author = $this->input->post('author');  
		$subject = $this->input->post('subject'); 
		$editor = $this->input->post('editor'); 
 
		$dt = $this->api->getbymemberUuid($private_key)->row();
		  
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		// if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200);   
		if($limit=="" or (int)$limit<1) $limit = 5;
		if($page=="" or (int)$page<1) $page = 1;
		
		$offset = ($page-1)*$limit;
		
		$where = "";
		if($search!=""){ 
			$where[] = "title like '%$search%'";
			$where[] = "published_year like '%$search%'";
			$where[] = "publisher_name like '%$search%'";
			$where[] = " publisher_city like '%$search%'";
			$where[] = "author like '%$search%'";
			$where[] = "ks.name like '%$search%'";
			$where[] = "alternate_subject like '%$search%'";
			$where[] = "cc.name like '%$search%'";
			$where[] = "kp.name like '%$search%'";
			$where[] = "cc.code like '%$search%'";
			$where[] = "kt.code like '%$search%'";
		} 

		$digital = $this->digital();
		
		$catalog = $this->api->getCatalogFilter($type,$typeid,$location,$digital,$limit,$offset,$where,$dt->memberid,$title,$author,$subject,$editor)->result();
		$total = $this->api->getCatalog($type,$typeid,$location,$digital,$where,$title,$author,$subject,$editor)->row();
 
		$data['total']['all'] = $total->total;
		$data['page'] = $page;
		 
		$data['data'] = array(); 
		   
		if($catalog){      
			
			foreach($catalog as $key => $row){ 
				
				// $directory  = $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$row->softcopy_path.'/'; 
				// $filecount = count(glob($directory . "*"));
				
				$cover  = $_SERVER['DOCUMENT_ROOT'].'uploads/book/cover/'.$row->cover_path;
				if (file_exists($cover) and $row->cover_path!="") $data['data'][$key]['cover'] = openlibrary_url().'uploads/book/cover/'.$row->cover_path;
				else $data['data'][$key]['cover'] = openlibrary_url().'uploads/book/cover/default.jpg';
				// if(file_exists('/'))
				$data['data'][$key]['id'] = $row->id;
				$data['data'][$key]['cover_path'] = $row->cover_path;
				// $data['data'][$key]['entrance_date'] = $row->entrance_date;
				$data['data'][$key]['title'] = $row->title;
				$data['data'][$key]['typename'] = $row->typename; 
				$data['data'][$key]['catalog_code'] = $row->catalog_code; 
				$data['data'][$key]['author'] = $row->author;
				$data['data'][$key]['published_year'] = $row->published_year;
				$data['data'][$key]['publisher_city'] = $row->publisher_city;
				$data['data'][$key]['publisher_name'] = $row->publisher_name; 
				$data['data'][$key]['subjectname'] = $row->subjectname;
				$data['data'][$key]['classification_code'] = $row->classification_code; 
				$data['data'][$key]['classification_name'] = $row->classification_name; 
				$data['data'][$key]['rak'] = '-'; 
				$data['data'][$key]['location'] = $row->location; 
				// $data['data'][$key]['tersedia'] = $row->tersedia; 
				// $data['data'][$key]['total'] = $row->total;  
				// $data['data'][$key]['file'] = $filecount;  
				// $data['data'][$key]['sirkulasi'] = ($row->type=='1'?'Ya':'Tidak'); 
				// $data['data'][$key]['notif_ketersediaan'] = ($row->notif_ketersediaan>0?'Ya':'Tidak'); 
			} 
		}  

		$this->response(array('status' => "true",'message'=>$data), 200); 		
	}  
   
	function catalog_detail_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');   
		$type = $this->input->post('type'); 
		$typeid = $this->input->post('typeid'); 
		$location = $this->input->post('location');
		
		$id = $this->input->post('id');  
 
		$dt = $this->api->getbymemberUuid($private_key)->row(); 
		  
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if($id=="") $this->response(array('status' => "false",'message'=>'Id is empty'), 200);   
   

		$digital = $this->digital();
		$temp = explode(",",$digital);

		$row = $this->api->getCatalogDetail($type,$typeid,$location,$digital,$id,$dt->memberid)->row();  

		$dts['kiv_id_item'] = $id;
		$dts['kiv_date'] = 	date('Y-m-d H:i:s');
		$dts['kiv_type'] = 'mobile';
		$dts['kiv_id_member'] = $dt->memberid;
		$this->api->addKIV($dts);
		
		 
		$data['data'] = array(); 
		   
		if($row){       
				$key=0;
				$directory  = $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$row->softcopy_path.'/'; 
				$filecount = count(glob($directory . "*"));
				
				$cover  = $_SERVER['DOCUMENT_ROOT'].'uploads/book/cover/'.$row->cover_path;
				if (file_exists($cover) and $row->cover_path!="") $data['data'][$key]['cover'] = openlibrary_url().'uploads/book/cover/'.$row->cover_path;
				else $data['data'][$key]['cover'] = openlibrary_url().'uploads/book/cover/default.jpg';
			 
				$data['data'][$key]['id'] = $row->id;
				$data['data'][$key]['cover_path'] = $row->cover_path;
				$data['data'][$key]['entrance_date'] = $row->entrance_date;
				$data['data'][$key]['title'] = $row->title;
				$data['data'][$key]['typename'] = $row->typename; 
				$data['data'][$key]['catalog_code'] = $row->catalog_code; 
				$data['data'][$key]['author'] = $row->author;
				$data['data'][$key]['author_type'] = ($row->author_type=='1'?'Perorangan':'Organisasi'); 
				$data['data'][$key]['published_year'] = $row->published_year;
				$data['data'][$key]['publisher_city'] = $row->publisher_city;
				$data['data'][$key]['publisher_name'] = $row->publisher_name; 
				$data['data'][$key]['subjectname'] = $row->subjectname;
				$data['data'][$key]['subjectalternate'] = $row->alternate_subject;
				$data['data'][$key]['classification_code'] = $row->classification_code; 
				$data['data'][$key]['classification_name'] = $row->classification_name; 
				$data['data'][$key]['tersedia'] =  (in_array($row->knowledge_type_id,$digital)?'1':$row->tersedia);
				$data['data'][$key]['total'] = (in_array($row->knowledge_type_id,$digital)?'1':$row->total);
				$data['data'][$key]['file'] = $filecount;  
				$data['data'][$key]['abstract_content'] = $row->abstract_content; 
				$data['data'][$key]['editor'] = $row->editor; 
				$data['data'][$key]['translator'] = $row->translator; 
				$data['data'][$key]['language'] = $row->language; 
				$data['data'][$key]['supplier'] = $row->supplier; 
				$data['data'][$key]['isbn'] = $row->isbn; 
				$data['data'][$key]['collation'] = $row->collation; 
				$data['data'][$key]['price'] = $row->price; 
				$data['data'][$key]['rent_cost'] = (in_array($row->knowledge_type_id,$digital)?'1':$row->rent_cost);
				$data['data'][$key]['penalty_cost'] = (in_array($row->knowledge_type_id,$digital)?'1':$row->penalty_cost);
				$data['data'][$key]['sirkulasi'] = ($row->rentable=='1'?'Ya':'Tidak'); 
				$data['data'][$key]['notif_ketersediaan'] = ($row->notif_ketersediaan>0?'Ya':'Tidak'); 
				$data['data'][$key]['url'] = "https://openlibrary.telkomuniversity.ac.id/home/catalog/id/".$row->id."/slug/".$row->id.".html"; 
				$data['data'][$key]['type'] = (in_array($row->knowledge_type_id,$temp)?'digital':'fisik');
				$data['data'][$key]['location'] = $row->location; 


			 
				if (strpos(strtolower($row->typename), 'reference') !== false) { 
				  $sql 	 = "select maps_no from maps where maps_range_start <= '".$row->classification_code."' and maps_range_end >='".$row->classification_code."' and maps_type='reference'";  
				} 
				else { 
				  $sql 	 = "select maps_no from maps where maps_range_start <= '".$row->classification_code."' and maps_range_end >='".$row->classification_code."'";  
				}
				$rak = $this->api->getRak($sql)->row();

				
				$data['data'][$key]['rak'] = (in_array($row->knowledge_type_id,$temp)?'-':$rak->maps_no);
			 

				$data['file'] = array();

				if($filecount!=0){
					$file = $this->api->getfileUpload($dt->member_type_id)->result();
					if($file){
						$key = 0;
						foreach($file as $rows){
							$directory  = $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$row->softcopy_path.'/'.$row->catalog_code.'_'.$rows->name.'.'.$rows->extension;  
							
							if (file_exists($directory)) {
								// $data['file'][$key]['id'] = $rows->id;  
								$data['file'][$key]['name'] = $rows->title;  
								$data['file'][$key]['file'] = $rows->name.'.'.$rows->extension;
								$data['file'][$key]['ext'] 	= $rows->extension;
									
								$datas['download'] = ($rows->download>0?'1':'0');
								$datas['dwn']['knowledge_item_id'] = $row->id;
								$datas['dwn']['member_id'] = $dt->memberid;
								$datas['dwn']['name'] = $row->catalog_code.'_'.$rows->name.'.'.$rows->extension; 
								
								$datas['readonly'] = ($rows->readonly>0?'1':'0'); 
								$datas['read']['knowledge_item_id'] = $row->id;
								$datas['read']['member_id'] = $dt->memberid;
								$datas['read']['name'] = $row->catalog_code.'_'.$rows->name.'.'.$rows->extension;
 

								$datas['name'] = $row->catalog_code.'_'.$rows->name.'.'.$rows->extension;
								$datas['link'] = $directory; 
								// print_r($datas);
								$test = json_encode($datas);  
								$test2 = base64_encode($test);
								$data['file'][$key]['download'] = ($rows->download>0?'1':'0'); 
								$data['file'][$key]['readonly'] = ($rows->readonly>0?'1':'0'); 

								$data['file'][$key]['url'] = (($data['file'][$key]['download']==0 and $data['file'][$key]['readonly']==0)?"":"https://openlibrary.telkomuniversity.ac.id/open/index.php/download/flippingbook_url_download_bypass/".$test2);
								$key++;
							}  
						}
					}
				}
		
		}  

		$this->response(array('status' => "true",'message'=>$data), 200); 		
	}   

	function karyailmiah_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');   
		
		$limit = $this->input->post('limit'); 
		$page = $this->input->post('page');  
		$search = $this->input->post('search');   
		$typeid = $this->input->post('typeid');  
		$start_date = $this->input->post('start_date');  
		$end_date = $this->input->post('end_date');
		$status_general = $this->input->post('status_general');
 
		$dt = $this->api->getbymemberUuidCivitas($private_key,$this->civitas(),$this->pegawai_dosen(),$this->mahasiswa())->row();
		 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200); 
		if($limit=="" or (int)$limit<1) $limit = 10;
		if($page=="" or (int)$page<1) $page = 1;
		
		$offset = ($page-1)*$limit;
		
		$where = "";
		if($search!=""){ 
			$where[] = "title like '%$search%'";
			$where[] = "m.master_data_fullname like '%$search%'";
			$where[] = "m.master_data_username like '%$search%'";
			$where[] = "m2.master_data_fullname like '%$search%'";
			$where[] = "m3.master_data_fullname like '%$search%'"; 
			$where[] = "ks.name like '%$search%'"; 
			$where[] = "kt.name like '%$search%'";
		}  
 
		$catalog = $this->api->getKaryaIlmiahFilter($typeid,$dt->memberid,$dt->civitas_type,$limit,$offset,$where,$start_date,$end_date,$status_general)->result();
		$total = $this->api->getKaryaIlmiah($typeid,$dt->memberid,$dt->civitas_type,$where,$start_date,$end_date,$status_general)->row();
 
		$data['total']['all'] = $total->total;
		$data['page'] = $page;
		 
		$data['data'] = array(); 
		
		$approved = $this->approved();

		if($catalog){       
			foreach($catalog as $key => $row){  
				$data['data'][$key]['id'] = $row->id;
				$data['data'][$key]['title'] = $row->title; 
				$data['data'][$key]['student'] = $row->student;
				$data['data'][$key]['nim'] = $row->nim; 
				$data['data'][$key]['prodi'] = $row->nama_prodi;  
				$data['data'][$key]['subjectname'] = $row->subjectname;
				$data['data'][$key]['typename'] = $row->typename; 
				$data['data'][$key]['lecturer_1'] = $row->lecturer_name_1; 
				$data['data'][$key]['lecturer_2'] = $row->lecturer_name_2;  
				$data['data'][$key]['date'] = $row->created_at;   
				$data['data'][$key]['status_name'] = $row->status;  

				if(in_array($row->latest_state_id,$approved)) 
					$data['data'][$key]['status_general'] = 'Approved';
				else if($row->latest_state_id=='1') $data['data'][$key]['status_general'] = 'Review';
				else if($row->latest_state_id=='22') $data['data'][$key]['status_general'] = 'On Draft';
				else if($row->latest_state_id=='2') $data['data'][$key]['status_general'] = 'Revision';
			} 
		} 
 
		$this->response(array('status' => "true",'message'=>$data), 200); 		
	} 

	function approved() {
		return array(3,52,64,53,91,5);
	}
   
	function karyailmiah_detail_post() {
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');    
		
		$id = $this->input->post('id');  
 
		$dt = $this->api->getbymemberUuidCivitas($private_key,$this->civitas(),$this->pegawai_dosen(),$this->mahasiswa())->row();
		  
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200); 
		if($id=="") $this->response(array('status' => "false",'message'=>'Id is empty'), 200);   
  
 
		$row = $this->api->getKaryaIlmiahDetail($id)->row();   
		
		
		    
		if($row){
				if($dt->memberid==$row->lecturer_id and $row->latest_state_id=='1') $data['option'] = 'edit';
				else if($dt->civitas_type=='admin') $data['option'] = 'edit';
				else $data['option'] = 'view';
		$data['data'] = array(); 
				$key=0;
				$data['data'][$key]['id'] = $row->id;
				$data['data'][$key]['title'] = $row->title; 
				$data['data'][$key]['abstract_content'] = $row->abstract_content; 
				$data['data'][$key]['student'] = $row->student;
				$data['data'][$key]['nim'] = $row->nim; 
				$data['data'][$key]['prodi'] = $row->nama_prodi;  
				$data['data'][$key]['subjectname'] = $row->subjectname;
				$data['data'][$key]['typename'] = $row->typename; 
				$data['data'][$key]['lecturer_1'] = $row->lecturer_name_1; 
				$data['data'][$key]['lecturer_2'] = $row->lecturer_name_2; 
				$data['data'][$key]['date'] = $row->created_at;  
				$data['data'][$key]['unit'] = $row->unit;   
				$data['data'][$key]['status_name'] = $row->status;  
				$approved = $this->approved();
				if(in_array($row->latest_state_id,$approved)) 
				$data['data'][$key]['status_general'] = 'Approved';
				else if($row->latest_state_id=='1') $data['data'][$key]['status_general'] = 'Review';
				else if($row->latest_state_id=='22') $data['data'][$key]['status_general'] = 'Draft';
				else if($row->latest_state_id=='2') $data['data'][$key]['status_general'] = 'Revision';

				$next = $this->api->getNextState($row->latest_state_id)->result();  

				foreach($next as $key2 => $row2){
					$data['status_option'][$key2]['id'] = $row2->id;
					$data['status_option'][$key2]['name'] = $row2->name; 
				}     
				 
				$kompetensi = $this->api->getDocumentMasterSubjectByUnitId($row->course_code,$row->id)->result();  

				foreach($kompetensi as $key3 => $row3){ 
					$data['kompetensi'][$key3]['name'] = $row3->name; 
				} 
				
				$sdgs_value = $this->api->getDocumentSdgs($row->id)->result(); 
				$data['sdgs_value'] = array();
				if($sdgs_value){ 
					foreach($sdgs_value as $key2=> $row2){
						$data['sdgs_value'][$key2]['id'] = $row2->sdgs_kode;  
						$data['sdgs_value'][$key2]['name'] = $row2->sdgs_name;  
					}
				}  

				$sdgs = $this->sdgs();
				$i = 0;
				foreach($sdgs as $key2 => $row2){
					
					$data['sdgs_option'][$i]['id'] = $key2;
					$data['sdgs_option'][$i]['name'] = $row2; 
					$i++;
				}      

				$history_state = $this->api->getDocumentState($id)->result();
				if($history_state){
					$key = 0;
					foreach($history_state as $rows){  
						$data['history_state'][$key]['state_name'] = $rows->state_name;     
						$data['history_state'][$key]['name'] = $rows->master_data_fullname;  
						$data['history_state'][$key]['date'] = ($rows->close_date!=""?$rows->close_date: $rows->open_date);   
						$key++; 
					}
				}
				
				
				$data['file'] = array(); 

				$file = $this->api->getfileUploadDocument($id)->result();
				if($file){
					$key = 0;
					foreach($file as $rows){
						$directory  = $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$row->master_data_user.'/'.$rows->location; 
						$directory2  = $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/document/'.$row->master_data_user.'/'.$rows->location; 
					 
						if (file_exists($directory)) {
							// $data['file'][$key]['id'] = $rows->id;  
							$data['file'][$key]['name'] = $rows->name;    

							$datas['download'] = 1;
							$datas['readonly'] = 1;
							$datas['name'] = $rows->location;
							$datas['link'] = $directory; 
						 
							$test = json_encode($datas);  
							$test2 = base64_encode($test); 

							$data['file'][$key]['url'] = "https://openlibrary.telkomuniversity.ac.id/open/index.php/download/flippingbook_url_download_bypass/".$test2;
							$key++;
						}  
					}
				} 
				
		
		}  

		$this->response(array('status' => "true",'message'=>$data), 200); 		
	}   

	function karyailmiah_comment_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');   
		$id = $this->input->post('id');   
		
		$limit = $this->input->post('limit'); 
		$page = $this->input->post('page');   
 
		$dt = $this->api->getbymemberUuidCivitas($private_key,$this->civitas(),$this->pegawai_dosen(),$this->mahasiswa())->row();
		 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200); 
		if($limit=="" or (int)$limit<1) $limit = 10;
		if($page=="" or (int)$page<1) $page = 1;
		
		$offset = ($page-1)*$limit; 
 
		$where = "where document_id='$id' and parent_id is null";

		$catalog = $this->api->getKaryaIlmiahCommentFilter($where,$limit,$offset)->result();
		$total = $this->api->getKaryaIlmiahComment($where)->row();
 
		$data['total']['all'] = $total->total;
		$data['page'] = $page;
		 
		$data['data'] = array(); 
		 

		if($catalog){       
			foreach($catalog as $key => $row){  
				$data['data'][$key]['id'] = $row->id;
				$data['data'][$key]['user'] = $row->master_data_user.' - '.$row->master_data_fullname;
				$data['data'][$key]['comment'] = $row->comment; 
				$data['data'][$key]['created_at'] = $row->created_at; 
			} 
		} 
 
		$this->response(array('status' => "true",'message'=>$data), 200); 		
	} 

	function karyailmiah_update_post() {
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');    
		
		$id = $this->input->post('id');  
		// $latest_state_id_old = $this->input->post('status_id_old');  
		$latest_state_id = $this->input->post('status_id'); 
		$comment = $this->input->post('comment'); 
		$sdgs_id = $this->input->post('sdgs_id');  
		$sdgs_id = explode(",",$sdgs_id);
		$dt = $this->api->getbymemberUuidCivitas($private_key,$this->civitas(),$this->pegawai_dosen(),$this->mahasiswa())->row();

		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200); 
		if($id=="") $this->response(array('status' => "false",'message'=>'Id is empty'), 200);   
		$member_doc = $this->api->getWorkflowDocumentMember($id)->row();
		
		$wd = $this->api->getWorkflowDocumentbyId($id)->row();  
		//workflow_document    
		$date_now				= date('Y-m-d H:i:s');
		// $date_now				= date('Y-m-d H:i:s', strtotime('+7 hours'));
		$inp['updated_by'] 		= $dt->master_data_user;
		$inp['updated_at'] 		= $date_now;
		if($latest_state_id!="") $inp['latest_state_id'] = $latest_state_id;   
		$this->api->editCustom('id', 'workflow_document', $id, $inp);
		
		//workflow_document_state
		if($latest_state_id!=""){ 
			$state = $this->api->getstatebyid($latest_state_id)->row(); 
			$this->api->edit_workflow_document_state($id,$wd->latest_state_id,$dt->memberid); 
 
			$temp2 = array();
			$temp2['document_id'] 	= $id;
			$temp2['member_id'] 	= $dt->memberid;
			$temp2['state_id'] 		= $latest_state_id;
			$temp2['open_date'] 	= $date_now;
			
			$approval = array(3,52,64,53,91);

			if($state->rule_type==1 or $state->rule_type==0){   

				if($latest_state_id=='2'){
					$temp2['allowed_member_id'] = $member_doc->member_id;
					$temp2['open_by'] 			= $member_doc->member_id;
					// ".$state->name."  
					$messages 		= $dt->master_data_fullname." telah meminta dilakukan revisi karya ilmiah terkait ".$comment;  
					$title = "Karya Ilmiah - Need Revision";   
				}
				else{
					$approved_status = array(3,4,52,53,64,91);
					if(in_array($latest_state_id,$approved_status)){  
						$messages 		= $dt->master_data_fullname." telah melakukan approval karya ilmiah";
						$title = "Karya Ilmiah - Approved";  
					}
				} 

				$itemnotif['notif_id_member'] 	= $member_doc->master_data_user;
				$itemnotif['notif_type'] 	= 'karyailmiah';
				$itemnotif['notif_content'] 	= $messages;
				$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
				$itemnotif['notif_status'] 	= 'unread';
				$itemnotif['notif_id_detail'] 	= $id;

				$notif_id = $this->api->add_custom($itemnotif,'notification_mobile'); 

				$token = $this->api->getTokenNotificationMobile($member_doc->member_id)->row();

				$notif_content = $messages;
				$notif_id_detail = $id;
				$notif_type = 'karyailmiah';
				$token = $token->master_data_token;

				NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);    
			
			} 
			//rule_type = 2  Khusus Pembimbing Akademik
			else if($state->rule_type==2){
				//jika workflow skripsi ta pa, ambil lecturer dari kolom lecturer_id tabel workflow_document
  
				// if($workflow_id=='1'){
					$temp2['allowed_member_id'] = $wd->lecturer_id;
					$temp2['open_by'] 			= $wd->lecturer_id; 

					$messages 		= $dt->master_data_fullname." telah melakukan perubahan status document menjadi ".$state->name."";

					$itemnotif['notif_id_member'] 	= $wd->lecturer_username;
					$itemnotif['notif_type'] 	= 'karyailmiah';
					$itemnotif['notif_content'] 	= $messages;
					$itemnotif['notif_date'] 	= date('Y-m-d H:i:s', strtotime('+7 hour'));
					$itemnotif['notif_status'] 	= 'unread'; 
					$itemnotif['notif_id_detail'] 	= $id; 
					$notif_id = $this->dm->add_custom($itemnotif,'notification_mobile'); 

					$token = $this->api->getTokenNotificationMobile($inp['lecturer_id'])->row();
	
					$title = "Karya Ilmiah - Request Approval";  
					$notif_content = $messages;
					$notif_id_detail = $id;
					$notif_type = 'karyailmiah';
					$token = $token->master_data_token;
	
					NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);    

				// }
				// else {
				// 	$lecturer = $this->dm->smk_t_tra_mhs_skripsi($member_doc->master_data_user)->row();
				// 	if($lecturer){
				// 		$member = $this->dm->getmembers($lecturer->C_KODE_DOSEN_PEMBIMBING_SATU)->row();
				// 		if($member){ 
				// 			$temp2['allowed_member_id'] = $member->id;
				// 			$temp2['open_by'] 			= $member->id;
				// 		}
				// 	}
				// }
			} 

			$this->api->add_custom($temp2,'workflow_document_state');
			
		}   
					
		if($comment!=""){ 

			$item_comment['document_id'] =  $id;
			$item_comment['comment']  = $comment; 
			$item_comment['created_at']  = $date_now; 
			$item_comment['member_id']  = $dt->memberid; 

			$state = $this->api->getDocumentStateId($id ,$latest_state_id)->row();

			$item_comment['document_state_id'] = $state->id;
			$this->api->add_custom($item_comment,'workflow_comment'); 
	
		}
		
		//workflow_document_sdgs 
		if($sdgs_id){ 
			$mastersdgs = $this->sdgs(); 
			$this->api->delete_workflow_document_sdgs($id); 
			$tempsdgs = array();
			foreach($sdgs_id as $key){
				$tempsdgs['document_id'] = $id;
				$tempsdgs['sdgs_kode'] = $key;
				$tempsdgs['sdgs_name'] = $mastersdgs[$key];
				$this->api->add_custom($tempsdgs,'workflow_document_sdgs');
			}
		}  


		$this->response(array('status' => "true",'message'=>'OK'), 200); 		
	}  
	
	function sdgs()
	{
		$array['1'] 	= 'Pilar pembangunan sosial - Menghapus kemiskinan';  
		$array['2'] 	= 'Pilar pembangunan sosial - Mengakhiri kelaparan';  
		$array['3'] 	= 'Pilar pembangunan sosial - Kesehatan yang baik dan kesejahteraan';  
		$array['4'] 	= 'Pilar pembangunan sosial - Pendidikan Bermutu';  
		$array['5'] 	= 'Pilar pembangunan sosial - Kesetaraan gender';  
		$array['7'] 	= 'Pilar pembangunan ekonomi - Energi bersih dan terjangkau';  
		$array['8'] 	= 'Pilar pembangunan ekonomi - Pekerjaan layak dan pertumbuhan ekonomi';  
		$array['9'] 	= 'Pilar pembangunan ekonomi - Infrastruktur, industri, dan inovasi';  
		$array['10'] 	= 'Pilar pembangunan ekonomi - Mengurangi ketimpangan';  
		$array['17'] 	= 'Pilar pembangunan ekonomi - Kemitraan untuk mencapai tujuan';  
		$array['6'] 	= 'Pilar pembangunan lingkungan - Akses air bersih dan sanitasi';  
		$array['11'] 	= 'Pilar pembangunan lingkungan - Kota dan komunitas yang berkelanjutan';  
		$array['12'] 	= 'Pilar pembangunan lingkungan - Konsumsi dan produksi yang bertanggungjawab';  
		$array['13'] 	= 'Pilar pembangunan lingkungan - Penanganan perubahan iklim';  
		$array['14'] 	= 'Pilar pembangunan lingkungan - Menjaga ekosistem laut';  
		$array['15'] 	= 'Pilar pembangunan lingkungan - Menjaga ekosistem darat';  
		$array['16'] 	= 'Pilar pembangunan  hukum dan tata kelola - Perdamaian, keadilan, dan kelembagaan yang kuat';  
		return $array;
	}

	function room_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');   
		
		$limit = $this->input->post('limit'); 
		$page = $this->input->post('page');  
		$search = $this->input->post('search');   
		$typeid = $this->input->post('typeid');   
		$start_date = $this->input->post('start_date');   
		$end_date = $this->input->post('end_date');   
 
		$dt = $this->api->getbymemberUuidCivitas($private_key,$this->civitas(),$this->pegawai_dosen(),$this->mahasiswa())->row();
		 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200); 
		if($limit=="" or (int)$limit<1) $limit = 10;
		if($page=="" or (int)$page<1) $page = 1;
		
		$offset = ($page-1)*$limit;
		
		$where = "";
		if($search!=""){ 
			$where[] = "room_name like '%$search%'";
			$where[] = "bk_name like '%$search%'";
		} 
 
		$catalog = $this->api->getRoomFilter($typeid,$dt->memberid,$limit,$offset,$where,$start_date,$end_date)->result();
		$total = $this->api->getRoom($typeid,$dt->memberid,$where,$start_date,$end_date)->row();
 
		$data['total']['all'] = $total->total;
		$data['page'] = $page;
		 
		$data['data'] = array(); 
		     
		if($catalog){       
			foreach($catalog as $key => $row){  
				$data['data'][$key]['id'] = $row->bk_id;
				$data['data'][$key]['room'] = $row->room_name; 
				$data['data'][$key]['purpose'] = $row->bk_purpose;
				$data['data'][$key]['status'] = $row->bk_status;
				$data['data'][$key]['start_booking_date'] = $row->bk_startdate; 
				$data['data'][$key]['end_booking_date'] = $row->bk_enddate;   
				$data['data'][$key]['create_date'] = $row->bk_createdate;  
			} 
		} 
 
		$this->response(array('status' => "true",'message'=>$data), 200); 		
	} 

	function room_detail_post() {
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');    
		
		$id = $this->input->post('id');  
 
		$dt = $this->api->getbymemberUuidCivitas($private_key,$this->civitas(),$this->pegawai_dosen(),$this->mahasiswa())->row();
		  
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200); 
		if($id=="") $this->response(array('status' => "false",'message'=>'Id is empty'), 200);   
  
 
		$row = $this->api->getRoomDetail($id)->row();   
		
		
		    
		if($row){ 
		$data['data'] = array(); 
				$key=0;
				$data['data'][$key]['id'] = $row->bk_id;
				$data['data'][$key]['room'] = $row->room_name; 
				$data['data'][$key]['purpose'] = $row->bk_purpose;
				$data['data'][$key]['reason_reject'] = $row->reason;
				$data['data'][$key]['status'] = $row->bk_status;
				$data['data'][$key]['start_booking_date'] = $row->bk_startdate; 
				$data['data'][$key]['end_booking_date'] = $row->bk_enddate;   
				$data['data'][$key]['create_date'] = $row->bk_createdate;  

				$data['data'][$key]['total_person'] = $row->bk_total;
				$temp = explode(",",$row->bk_name);

				$data['person'] = array();
				foreach($temp as $key2 => $row2){ 
					$data['person'][$key2] = ucwords(strtolower($row2));
				}  
				
		
		}  

		$this->response(array('status' => "true",'message'=>$data), 200); 		
	}   

	function room_component_post() { 
		$public_key = $this->input->post('public_key');  
		 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		 
		$room = $this->api->getroombyactiveid()->result();  
		
		foreach($room as $key => $row){  
			$data['room'][$key]['room_id'] = $row->room_id;
			$data['room'][$key]['room_name'] = $row->room_name; 
			$data['room'][$key]['room_min'] = $row->room_min;
			$data['room'][$key]['room_max'] = $row->room_max;
			$data['room'][$key]['room_capacity'] = $row->room_min." - ".$row->room_max." orang";   

			if($row->room_id=='10'){
				$j = 0;
				$data['duration'][$row->room_id][$j]['id'] = '180';
				$data['duration'][$row->room_id][$j]['name'] = '180 menit';
			}
			else if($row->room_id=='11' || $row->room_id=='12' || $row->room_id=='14' || $row->room_id=='15'){
				$j = 0;
				$data['duration'][$row->room_id][$j]['id'] = '120';
				$data['duration'][$row->room_id][$j]['name'] = '120 menit';
			} 
			else {  
				$j = 0;
				$data['duration'][$row->room_id][$j]['id'] = '30';
				$data['duration'][$row->room_id][$j]['name'] = '30 menit';
				$j++;
				$data['duration'][$row->room_id][$j]['id'] = '60';
				$data['duration'][$row->room_id][$j]['name'] = '60 menit';
				$j++;
				$data['duration'][$row->room_id][$j]['id'] = '90';
				$data['duration'][$row->room_id][$j]['name'] = '90 menit';
				$j++;
				$data['duration'][$row->room_id][$j]['id'] = '120';
				$data['duration'][$row->room_id][$j]['name'] = '120 menit';
			}
		} 
		
		$j = 0;
		//weekday
		for($i=8;$i<=18;$i++){
			$data['time'][$j] = sprintf('%02d', $i).":00";
			$j++;

			// if($i!=19){
				$data['time'][$j] = sprintf('%02d', $i).":30";
				$j++;
			// }
		} 

		//weekday_libur
		// for($i=8;$i<=15;$i++){
		// 	$data['time'][$j] = sprintf('%02d', $i).":00";
		// 	$j++;

		// 	if($i!=16){
		// 		$data['time'][$j] = sprintf('%02d', $i).":30";
		// 		$j++;
		// 	}
		// } 

		//ramadhan
		// for($i=8;$i<=15;$i++){
		// 	$data['time'][$j] = sprintf('%02d', $i).":00";
		// 	$j++;

		// 	// if($i!=19){
		// 		$data['time'][$j] = sprintf('%02d', $i).":30";
		// 		$j++;
		// 	// }
		// } 
		 
		$this->response(array('status' => "true",'message'=>$data), 200); 		
	}

	function room_member_post() { 
		$public_key = $this->input->post('public_key');   
		
		$limit = $this->input->post('limit'); 
		$page = $this->input->post('page');  
		$search = $this->input->post('search');   
		 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200);  
		if($limit=="" or (int)$limit<1) $limit = 10;
		if($page=="" or (int)$page<1) $page = 1;
		
		$offset = ($page-1)*$limit;
		 
 
		$catalog = $this->api->getMemberFilter($limit,$offset,strtolower($search))->result();
		$total = $this->api->getMember(strtolower($search))->row();
 
		$data['total']['all'] = $total->total;
		$data['page'] = $page; 
		 
		$data['data'] = array(); 
		     
		if($catalog){       
			foreach($catalog as $key => $row){  
				$data['data'][$key]['username'] = $row->master_data_user;
				$data['data'][$key]['fullname'] = $row->master_data_number." - ".$row->master_data_fullname;  
			} 
		} 
 
		$this->response(array('status' => "true",'message'=>$data), 200); 		
	} 

	function room_insert_post() { 
		$public_key = $this->input->post('public_key');   
		$private_key = $this->input->post('private_key'); 
		
		$room_id = $this->input->post('room_id'); 
		$duration = $this->input->post('duration');  
		$member = $this->input->post('member_username');   
		$starthour = $this->input->post('starthour');   
		$tujuan = $this->input->post('tujuan');   
		$date = $this->input->post('date');    
 
		$dt = $this->api->getbymemberUuidCivitas($private_key,$this->civitas(),$this->pegawai_dosen(),$this->mahasiswa())->row();
	 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200);   
		if(!$dt) $this->response(array('status' => "false",'message'=>'Mohon maaf, layanan Peminjaman Ruangan hanya bisa dilakukan oleh Civitas Tel-U'), 200); 
		

		$endhour 	= date('H:i', strtotime($starthour.' + '.$duration.' minutes') );
		 
		$startdate 	= $date." ".$starthour;
		$enddate	= $date." ".$endhour;
	  
		$count = $this->api->checkBannedPerMonthStatus($dt->master_data_user)->row();
	 
		$blacklist = $this->api->getBlacklist($dt->master_data_user)->row();
		if ($blacklist){
			$this->response(array('status' => "false",'message'=>'Anda sedang di blacklist'), 200);  
		}
		else {
			//  echo $dt->member_type_id;
			if (($count->total<'2' && $dt->member_type_id!=1) or ($dt->member_type_id==1)) {
				$count = $this->api->checkCountBookingRoomStatus($dt->master_data_user)->row(); 
				if (($count->total<'2' && $dt->member_type_id!=1) or ($dt->member_type_id==1)) {
					if(!$this->api->checkExistBooking($startdate,$enddate,$room_id)->row()){
						$data2 						= ($member!=""?explode(",",$member):array());
					 
						$room 						= $this->api->getroombyid($room_id)->row(); 
			 
						if (($room->room_min <= (count($data2)+1) && $dt->member_type_id!=1) or ($dt->member_type_id==1)) {
							$item['bk_username']		= $dt->master_data_user;
							$item['bk_memberid']		= $dt->memberid;
							$item['bk_mobile_phone']	=  $dt->master_data_number;
							$item['bk_status']  		= 'Request';
							$item['bk_createdby']  		= $dt->master_data_user;
							$item['bk_createdate'] 		= date("Y-m-d H:i:s"); 
							$item['bk_startdate']		= $startdate;
							$item['bk_enddate']			= $enddate;
							$item['bk_purpose']			= $tujuan;
							$item['bk_room_id']			= $room_id;
							$item['bk_via']				= 'mobile';
							$data2 						= explode(",",$member);
							$item['bk_total']			= count($data2);
							$item2['bm_bk_id']			= $this->api->add_custom($item,'room.booking');
							$temp						= array(); 
						 
							if($data2){
								foreach($data2 as $row){
									$mm = $this->api->getMemberByUsername($row)->row();
									$temp[]					= $mm->id;
									$item2['bm_username'] 	= $row;
									$item2['bm_userid'] 	= $mm->id;
									$this->api->addBookingMember($item2);
								} 
								$temp 						= implode(",",$temp);
							 
								$temp 						= $this->api->getListNameMember($temp)->row();
								$item3['bk_name']			= $temp->nama;
							}
							
							$this->api->editCustom('bk_id', 'room.booking', $item2['bm_bk_id'],$item3); 
							$messages 		= "Anda telah melakukan permintaan peminjaman ruangan ".$room->room_name." tanggal : ".$date." dan jam : ".$starthour." - ".$endhour.". Akan dikonfirmasi jika telah diproses.";
 
							$itemnotif['notif_id_member'] 	= $dt->master_data_user;
							$itemnotif['notif_type'] 	= 'ruangan';
							$itemnotif['notif_content'] 	= $messages;
							$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
							// $itemnotif['notif_date'] 	= date('Y-m-d H:i:s', strtotime('7 hour'));
							$itemnotif['notif_status'] 	= 'unread';
							$itemnotif['notif_id_detail'] 	= $item2['bm_bk_id'];

							$notif_id = $this->api->add_custom($itemnotif,'notification_mobile');  

							$token = $this->api->getTokenNotificationMobile($dt->memberid)->row();

							$title = "Ruangan - Request";  
							$notif_content = $messages;
							$notif_id_detail = $item2['bm_bk_id'];
							$notif_type = 'ruangan';
							$token = $token->master_data_token;

							NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);   
							
							//notif admin ulan, gilang, zaky
							$token = $this->api->getTokenNotificationMobile('12186')->row(); //ulan

							$itemnotif['notif_id_member'] 	= 'ulanlan'; 
							$itemnotif['notif_type'] 	= 'ruangan';
							$itemnotif['notif_content'] =	$dt->master_data_user.' Request Peminjaman Ruangan';
							$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
							$itemnotif['notif_status'] 	= 'unread';
							$itemnotif['notif_id_detail'] 	= $item2['bm_bk_id'];

							$notif_id = $this->api->add_custom($itemnotif,'notification_mobile'); 

							$title = "Ruangan - Request";  
							$notif_content = $dt->master_data_user.' Request Peminjaman Ruangan';
							$notif_id_detail = $item2['bm_bk_id'];
							$notif_type = 'ruangan';
							$token = $token->master_data_token; 
							NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);
							
							$token = $this->api->getTokenNotificationMobile('12186')->row(); //ulan

							$itemnotif['notif_id_member'] 	= 'ulanlan'; 
							$itemnotif['notif_type'] 	= 'ruangan';
							$itemnotif['notif_content'] =	$dt->master_data_user.' Request Peminjaman Ruangan';
							$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
							$itemnotif['notif_status'] 	= 'unread';
							$itemnotif['notif_id_detail'] 	= $item2['bm_bk_id'];

							$notif_id = $this->api->add_custom($itemnotif,'notification_mobile'); 

							$title = "Ruangan - Request";  
							$notif_content = $dt->master_data_user.' Request Peminjaman Ruangan';
							$notif_id_detail = $item2['bm_bk_id'];
							$notif_type = 'ruangan';
							$token = $token->master_data_token; 
							NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);  

							$token = $this->api->getTokenNotificationMobile('123126')->row(); //gilang

							$itemnotif['notif_id_member'] 	= 'gilangistriadip'; 
							$itemnotif['notif_type'] 	= 'ruangan';
							$itemnotif['notif_content'] =	$dt->master_data_user.' Request Peminjaman Ruangan';
							$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
							$itemnotif['notif_status'] 	= 'unread';
							$itemnotif['notif_id_detail'] 	= $item2['bm_bk_id'];

							$notif_id = $this->api->add_custom($itemnotif,'notification_mobile'); 

							$title = "Ruangan - Request";  
							$notif_content = $dt->master_data_user.' Request Peminjaman Ruangan';
							$notif_id_detail = $item2['bm_bk_id'];
							$notif_type = 'ruangan';
							$token = $token->master_data_token; 
							NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);  

							$token = $this->api->getTokenNotificationMobile('109765')->row(); //zaky 

							$itemnotif['notif_id_member'] 	= 'mzakyrakhmat'; 
							$itemnotif['notif_type'] 	= 'ruangan';
							$itemnotif['notif_content'] =	$dt->master_data_user.' Request Peminjaman Ruangan';
							$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
							$itemnotif['notif_status'] 	= 'unread';
							$itemnotif['notif_id_detail'] 	= $item2['bm_bk_id'];
							
							$notif_id = $this->api->add_custom($itemnotif,'notification_mobile');

							$title = "Ruangan - Request";  
							$notif_content = $dt->master_data_user.' Request Peminjaman Ruangan';
							$notif_id_detail = $item2['bm_bk_id'];
							$notif_type = 'ruangan';
							$token = $token->master_data_token; 
							NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token); 

							$this->response(array('status' => "true",'message'=>'Data berhasil ditambahkan'), 200);
						}
						else $this->response(array('status' => "false",'message'=>'Jumlah orang yang diinput ('.count($data2).' orang) tidak memenuhi jumlah minimum orang di ruangan '.$room->room_name.', yaitu '.$room->room_min.' orang'), 200);    
					}
					else $this->response(array('status' => "false",'message'=>'Ruangan yang dipilih untuk tanggal '.$date.' '.$starthour.' - '.$endhour.' sudah ada yang menggunakan. Silahkan memilih ruangan / jadwal yang lain'), 200);   
				}
				else $this->response(array('status' => "false",'message'=>'Anda sudah melebihi jumlah permintaan peminjaman yang diperbolehkan. Maksimal hanya diperbolehkan 2x permintaan peminjaman ruangan. Silahkan menunggu admin untuk melakukan proses pada request jadwal peminjaman anda yang sebelumnya'), 200);  
			}
			else $this->response(array('status' => "false",'message'=>'Anda tidak dapat melakukan peminjaman pada bulan ini, dikarenakan anda telah 2x melakukan peminjaman tetapi tidak hadir pada hari peminjaman.'), 200); 
		} 	
	}
	
	function bds_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');   
		
		$limit = $this->input->post('limit'); 
		$page = $this->input->post('page');  
		$search = $this->input->post('search');   
		$typeid = $this->input->post('typeid'); 
 
		$dt = $this->api->getbymemberUuidCivitas($private_key,$this->civitas(),$this->pegawai_dosen(),$this->mahasiswa())->row();
		 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200); 
		if($limit=="" or (int)$limit<1) $limit = 10;
		if($page=="" or (int)$page<1) $page = 1;
		
		$offset = ($page-1)*$limit; 
		 
		$where = "";
		if($search!=""){
			$where[] = "title like '%$search%'";
			$where[] = "bds_number like '%$search%'";
		}  
 
		$catalog = $this->api->getBdsFilter($typeid,$dt->memberid,$limit,$offset,$where)->result();
		$total = $this->api->getBds($typeid,$dt->memberid,$where)->row();
 
		$data['total']['all'] = $total->total;
		$data['page'] = $page;
		 
		$data['data'] = array(); 
		     
		if($catalog){       
			foreach($catalog as $key => $row){  
				$data['data'][$key]['id'] = $row->bds_id;  
				$data['data'][$key]['number'] = $row->bds_number;  
				$data['data'][$key]['status'] = $row->bds_status;  
				$data['data'][$key]['title'] = $row->title;
				$data['data'][$key]['reason'] = $row->bds_reason;  
				$data['data'][$key]['photo_courier'] = $row->bds_photo_courier;  
				$data['data'][$key]['address'] = $row->bds_address;  
				$data['data'][$key]['phone'] = $row->bds_phone;  
				$data['data'][$key]['receiver'] = $row->bds_receiver;  
				$data['data'][$key]['date'] = $row->bds_createdate;
			} 
		} 
 
		$this->response(array('status' => "true",'message'=>$data), 200); 		
	} 

	function bds_detail_post() {
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');    
		
		$id = $this->input->post('id');  
 
		$dt = $this->api->getbymemberUuidCivitas($private_key,$this->civitas(),$this->pegawai_dosen(),$this->mahasiswa())->row();
		  
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200); 
		if($id=="") $this->response(array('status' => "false",'message'=>'Id is empty'), 200);   
  
 
		$row = $this->api->getBdsDetail($id)->row();     
		     
		if($row){  
		$data['data'] = array(); 
				$key=0;
				$data['data'][$key]['id'] = $row->bds_id;  
				$data['data'][$key]['number'] = $row->bds_number;
				$data['data'][$key]['status'] = $row->bds_status;  
				$data['data'][$key]['title'] = $row->title;
				$data['data'][$key]['reason'] = $row->bds_reason;  
				$data['data'][$key]['photo_courier'] = $row->bds_photo_courier;  
				$data['data'][$key]['address'] = $row->bds_address;  
				$data['data'][$key]['phone'] = $row->bds_phone;  
				$data['data'][$key]['receiver'] = $row->bds_receiver;
				$data['data'][$key]['date'] = $row->bds_createdate;  
				$temp = explode(",",$row->bk_name); 

				$status = $this->api->getBdsStatus($id)->result();   
				$data['status'] = array();
				foreach($status as $key2 => $row2){ 
					$data['status'][$key2]['status'] = $row2->bdss_status;
					$data['status'][$key2]['date'] = $row2->bdss_date;
				}  


				$status_rent = array('1'=>'Dipinjam','2'=>'Dikembalikan','3'=>'Rusak','4'=>'Hilang');

				$status = $this->api->getBdsBook($id)->result();   
				$data['book'] = array();
				foreach($status as $key2 => $row2){ 
					$data['book'][$key2]['id'] = $row2->bdsb_id;
					$data['book'][$key2]['title'] = $row2->title;
					$data['book'][$key2]['catalog_code'] = $row2->bdsb_item_code;
					$data['book'][$key2]['barcode'] = $row2->bdsb_stock_code;
					$data['book'][$key2]['status'] = $status_rent[$row2->bdsb_status];
				}  
				 
		}  

		$this->response(array('status' => "true",'message'=>$data), 200); 		
	} 

	function bds_insert_post() { 
		$public_key = $this->input->post('public_key');   
		$private_key = $this->input->post('private_key'); 
		
		$itemid = $this->input->post('itemid'); 
		$bp_address = $this->input->post('address');  
		$bds_phone = $this->input->post('phone');   
		$bds_receiver = $this->input->post('receiver');       
 
		$dt = $this->api->getbymemberUuidCivitas($private_key,$this->civitas(),$this->pegawai_dosen(),$this->mahasiswa())->row();
	 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200);   
		if(!$dt) $this->response(array('status' => "false",'message'=>'Mohon maaf, layanan Book Delivery hanya bisa dilakukan oleh Civitas Tel-U'), 200); 
		 

		$item['bds_idmember']		=  $dt->memberid;
		$item['bds_address']		=  $bp_address;
		$item['bds_phone']			=  $bds_phone;
		$item['bds_receiver']		=  $bds_receiver; 
		$item['bds_number']			=  strtotime("now"); 
		$item['bds_status']  		= 'Request'; 
		$item['bds_createdate'] 	= date('Y-m-d H:i:s');
		 
		$item2['bdss_idbds']			= $this->api->add_custom($item,'batik.book_delivery_service');
		$temp						= array(); 
		
		$item2['bdss_status'] 	= 'Request';
		$item2['bdss_date'] 	= $item['bds_createdate'];
		$this->api->add_custom($item2,'batik.book_delivery_service_status'); 
		
		$temp = array();
		$book = $this->api->getBook($itemid)->result(); 
		foreach($book as $row){
			$item3['bdsb_idbds']		=  $item2['bdss_idbds'];
			$item3['bdsb_item_id']		=  $row->id;
			$item3['bdsb_item_code']	=  $row->code; 
			$this->api->add_custom($item3,'batik.book_delivery_service_book'); 
			$temp[] = $row->title;
		}

		$title = implode(" ; ",$temp);
 
		$messages 		= "Anda telah merequest peminjaman buku dengan no pesanan ".$item['bds_number']." pada tanggal : ".$item['bds_createdate']." . Akan dikonfirmasi jika telah disetujui / ditolak.";

		$itemnotif['notif_id_member'] 	= $dt->master_data_user;
		$itemnotif['notif_type'] 		= 'bds';
		$itemnotif['notif_content'] 	= $messages;
		$itemnotif['notif_date'] 		= date('Y-m-d H:i:s');
		$itemnotif['notif_status'] 		= 'unread';
		$itemnotif['notif_id_detail'] 	= $item2['bdss_idbds'];
		
		$notif_id = $this->api->add_custom($itemnotif,'notification_mobile');  

		$token = $this->api->getTokenNotificationMobile($dt->memberid)->row();

		$title = "Book Delivery Service - Request";  
		$notif_content = $messages;
		$notif_id_detail = $item2['bdss_idbds']; 
		$notif_type = 'bds';
		$token = $token->master_data_token;

		NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);   
		
		//notif admin risya
		
		$itemnotif['notif_id_member'] 	= 'risyarmaulidaa';
		$itemnotif['notif_type'] 		= 'bds';
		$itemnotif['notif_content'] 	= $dt->master_data_user.' Request Book Delivery Service';
		$itemnotif['notif_date'] 		= date('Y-m-d H:i:s');
		$itemnotif['notif_status'] 		= 'unread';
		$itemnotif['notif_id_detail'] 	= $item2['bdss_idbds'];
		
		$notif_id = $this->api->add_custom($itemnotif,'notification_mobile');

		$token = $this->api->getTokenNotificationMobile('123110')->row();

		$title = "Book Delivery Service - Request";  
		$notif_content = $dt->master_data_user.' Request Book Delivery Service';
		$notif_id_detail = $item2['bdss_idbds']; 
		$notif_type = 'bds';
		$token = $token->master_data_token; 
		NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);   
		
		//notif admin adit

		$itemnotif['notif_id_member'] 	= 'aditprtm';
		$itemnotif['notif_type'] 		= 'bds';
		$itemnotif['notif_content'] 	= $dt->master_data_user.' Request Book Delivery Service';
		$itemnotif['notif_date'] 		= date('Y-m-d H:i:s');
		$itemnotif['notif_status'] 		= 'unread';
		$itemnotif['notif_id_detail'] 	= $item2['bdss_idbds'];
		
		$notif_id = $this->api->add_custom($itemnotif,'notification_mobile');

		$token = $this->api->getTokenNotificationMobile('146215')->row();

		$title = "Book Delivery Service - Request";  
		$notif_content = $dt->master_data_user.' Request Book Delivery Service';
		$notif_id_detail = $item2['bdss_idbds']; 
		$notif_type = 'bds';
		$token = $token->master_data_token; 
		NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);   
		
		$this->response(array('status' => "true",'message'=>'Data berhasil ditambahkan'), 200);     		 
	}

	function bds_completed_post() { 
		$public_key = $this->input->post('public_key');   
		$private_key = $this->input->post('private_key'); 
		
		$bdsid = $this->input->post('id');     
 
		$dt = $this->api->getbymemberUuidCivitas($private_key,$this->civitas(),$this->pegawai_dosen(),$this->mahasiswa())->row();
	 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200);   
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200); 
		 
 
		$item['bds_status']  		= 'Completed';  
		$this->api->editCustom('bds_id', 'batik.book_delivery_service', $bdsid, $item);

		$temp						= array(); 
		
		$item2['bdss_idbds'] 	= $bdsid;
		$item2['bdss_status'] 	= 'Completed';
		$item2['bdss_date'] 	= date('Y-m-d H:i:s');
		$this->api->add_custom($item2,'batik.book_delivery_service_status'); 
		
		$temp = array();
		// $book = $this->api->getBook($itemid)->result(); 
		// foreach($book as $row){
		// 	$item3['bdsb_idbds']		=  $item2['bdss_idbds'];
		// 	$item3['bdsb_item_id']		=  $row->id;
		// 	$item3['bdsb_item_code']	=  $row->code; 
		// 	$this->api->add_custom($item3,'batik.book_delivery_service_book'); 
		// 	$temp[] = $row->title;
		// }

		// $title = implode(" ; ",$temp);
 
		$row = $this->api->getBdsDetail($bdsid)->row();     

		$messages 		= "Anda telah menyelesaikan proses peminjaman buku dengan no pesanan ".$row->bds_number." pada tanggal : ".$item2['bdss_date'].".";

		$itemnotif['notif_id_member'] 	= $dt->master_data_user;
		$itemnotif['notif_type'] 		= 'bds';
		$itemnotif['notif_content'] 	= $messages;
		$itemnotif['notif_date'] 		= date('Y-m-d H:i:s');
		$itemnotif['notif_status'] 		= 'unread';
		$itemnotif['notif_id_detail'] 	= $item2['bdss_idbds'];

		$notif_id = $this->api->add_custom($itemnotif,'notification_mobile');  
 
		//notif admin 
		$token = $this->api->getTokenNotificationMobile('12186')->row();

		$title = "Book Delivery Service - Completed";  
		$notif_content = $dt->master_data_user.' Completed Book Delivery Service';
		$notif_id_detail = $item2['bdss_idbds']; 
		$notif_type = 'bds';
		$token = $token->master_data_token; 
		NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);  
		
		$this->response(array('status' => "true",'message'=>'Data berhasil ditambahkan'), 200);     		 
	}

	function bahanpustaka_post() { 
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');   
		
		$limit = $this->input->post('limit'); 
		$page = $this->input->post('page');  
		$search = $this->input->post('search');   
		$typeid = $this->input->post('typeid'); 
 
		$dt = $this->api->getbymemberUuidCivitasTemporary($private_key)->row();
		 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		// if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200); 
		if($limit=="" or (int)$limit<1) $limit = 10;
		if($page=="" or (int)$page<1) $page = 1;
		
		$offset = ($page-1)*$limit; 
		
		$where = "";
		if($search!=""){ 
			$where[] = "bp_title like '%$search%'";
			$where[] = "bp_author like '%$search%'";
			$where[] = "bp_publisher like '%$search%'";
			$where[] = "bp_publishedyear like '%$search%'";
			$where[] = "bp_reference like '%$search%'";
		} 
 
		$catalog = $this->api->getBahanpustakaFilter($typeid,$dt->memberid,$limit,$offset,$where)->result();
		$total = $this->api->getBahanpustaka($typeid,$dt->memberid,$where)->row();
 
		$data['total']['all'] = $total->total;
		$data['page'] = $page;
		 
		$data['data'] = array(); 
		     
		if($catalog){       
			foreach($catalog as $key => $row){  
				$data['data'][$key]['id'] = $row->bp_id;
				$data['data'][$key]['fakultas'] = ucwords(strtolower($row->nama_fakultas)); 
				$data['data'][$key]['prodi'] = $row->nama_prodi; 
				$data['data'][$key]['title'] = $row->bp_title; 
				$data['data'][$key]['author'] = $row->bp_author;
				$data['data'][$key]['publisher'] = $row->bp_publisher;
				$data['data'][$key]['publishedyear'] = $row->bp_publishedyear; 
				$data['data'][$key]['matakuliah'] = $row->bp_matakuliah; 
				$data['data'][$key]['semester'] = $row->bp_semester; 
				$data['data'][$key]['reference'] = $row->bp_reference;   
				$data['data'][$key]['status'] = $row->bp_status;  
				$data['data'][$key]['reason'] = $row->bp_reason;
				$data['data'][$key]['date'] = $row->bp_createdate;  
			} 
		} 
 
		$this->response(array('status' => "true",'message'=>$data), 200); 		
	} 

	function bahanpustaka_detail_post() {
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');    
		
		$id = $this->input->post('id');  
 
		$dt = $this->api->getbymemberUuidCivitasTemporary($private_key)->row();
		  
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200); 
		if($id=="") $this->response(array('status' => "false",'message'=>'Id is empty'), 200);   
  
 
		$row = $this->api->getBahanpustakaDetail($id)->row();   
		
		
		    
		if($row){ 
		$data['data'] = array(); 
				$key=0;
				$data['data'][$key]['id'] = $row->bp_id;
				$data['data'][$key]['fakultas'] = ucwords(strtolower($row->nama_fakultas)); 
				$data['data'][$key]['prodi'] = $row->nama_prodi; 
				$data['data'][$key]['title'] = $row->bp_title; 
				$data['data'][$key]['author'] = $row->bp_author;
				$data['data'][$key]['publisher'] = $row->bp_publisher;
				$data['data'][$key]['publishedyear'] = $row->bp_publishedyear; 
				$data['data'][$key]['matakuliah'] = $row->bp_matakuliah; 
				$data['data'][$key]['semester'] = $row->bp_semester; 
				$data['data'][$key]['reference'] = $row->bp_reference;   
				$data['data'][$key]['status'] = $row->bp_status;  
				$data['data'][$key]['reason'] = $row->bp_reason;  
				$data['data'][$key]['date'] = $row->bp_createdate;  
				$temp = explode(",",$row->bk_name);

				$status = $this->api->getBahanpustakaStatus($id)->result();   
				$data['status'] = array();
				foreach($status as $key2 => $row2){ 
					$data['status'][$key2]['status'] = $row2->bps_status;
					$data['status'][$key2]['date'] = $row2->bps_date;
				}  
				
		
		}  

		$this->response(array('status' => "true",'message'=>$data), 200); 		
	} 

	function bahanpustaka_insert_post() { 
		$public_key = $this->input->post('public_key');   
		$private_key = $this->input->post('private_key'); 
 
		$dt = $this->api->getbymemberUuidCivitasTemporary($private_key)->row();
	 
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200);   
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200);  
		
		$item['bp_idmember']		=  $dt->memberid;
		$item['bp_title'] 			= $this->input->post('title'); 
		$item['bp_author'] 			= $this->input->post('author');  
		$item['bp_publisher'] 		= $this->input->post('publisher');   
		$item['bp_publishedyear'] 	= $this->input->post('publishedyear');   
		$item['bp_reference'] 		= $this->input->post('reference');      
		$item['bp_faculty_id'] 		= $this->input->post('faculty_id');
		$item['bp_prodi_id'] 		= $this->input->post('prodi_id');
		$item['bp_semester'] 		= $this->input->post('semester');
		$item['bp_matakuliah'] 		= $this->input->post('matakuliah');
		$item['bp_status']  		= 'Request'; 
		$item['bp_createdate'] 		= date('Y-m-d H:i:s'); 
		 
		$item2['bps_idbp']			= $this->api->add_custom($item,'batik.usulan_bahanpustaka');
		$temp						= array(); 
		
		$item2['bps_status'] 	= 'Request';
		$item2['bps_date'] 	= $item['bp_createdate'];
		$this->api->add_custom($item2,'batik.usulan_bahanpustaka_status');
 
		$messages 		= "Anda telah mengusulkan bahan pustaka dengan judul ".$item['bp_title']." pada tanggal : ".$item['bp_createdate']." . Akan dikonfirmasi jika telah disetujui / ditolak.";

		$itemnotif['notif_id_member'] 	= $dt->master_data_user;
		$itemnotif['notif_type'] 		= 'bahanpustaka';
		$itemnotif['notif_content'] 	= $messages;
		$itemnotif['notif_date'] 		= date('Y-m-d H:i:s');
		$itemnotif['notif_status'] 		= 'unread';
		$itemnotif['notif_id_detail'] 	= $item2['bps_idbp'];

		$notif_id = $this->api->add_custom($itemnotif,'notification_mobile');  

		$token = $this->api->getTokenNotificationMobile($dt->memberid)->row();

		$title = "Usulan Bahan Pustaka - Request";  
		$notif_content = $messages;
		$notif_id_detail = $item2['bps_idbp']; 
		$notif_type = 'bahanpustaka'; 
		$token = $token->master_data_token;

		NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);   
		
		//notif admin teh ima dan ridwan
		$token = $this->api->getTokenNotificationMobile('7814')->row(); //teh irma
		
		$itemnotif['notif_id_member'] 	= 'irmasari';
		$itemnotif['notif_type'] 		= 'bahanpustaka';
		$itemnotif['notif_content'] 	= $dt->master_data_user.' Request Usulan Bahan Pustaka';
		$itemnotif['notif_date'] 		= date('Y-m-d H:i:s');
		$itemnotif['notif_status'] 		= 'unread';
		$itemnotif['notif_id_detail'] 	= $item2['bps_idbp'];

		$notif_id = $this->api->add_custom($itemnotif,'notification_mobile');  

		$title = "Usulan Bahan Pustaka - Request";  
		$notif_content = $dt->master_data_user.' Request Usulan Bahan Pustaka';
		$notif_id_detail = $item2['bps_idbp']; 
		$notif_type = 'bahanpustaka';
		$token = $token->master_data_token; 
		NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);   

		$token = $this->api->getTokenNotificationMobile('117389')->row(); //ridwan
		
		$itemnotif['notif_id_member'] 	= 'readoner';
		$itemnotif['notif_type'] 		= 'bahanpustaka';
		$itemnotif['notif_content'] 	= $dt->master_data_user.' Request Usulan Bahan Pustaka';
		$itemnotif['notif_date'] 		= date('Y-m-d H:i:s');
		$itemnotif['notif_status'] 		= 'unread';
		$itemnotif['notif_id_detail'] 	= $item2['bps_idbp'];

		$notif_id = $this->api->add_custom($itemnotif,'notification_mobile');  

		$title = "Usulan Bahan Pustaka - Request";  
		$notif_content = $dt->master_data_user.' Request Usulan Bahan Pustaka';
		$notif_id_detail = $item2['bps_idbp']; 
		$notif_type = 'bahanpustaka';
		$token = $token->master_data_token; 
		NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);  
		
		$this->response(array('status' => "true",'message'=>'Data berhasil ditambahkan'), 200);    
						 
	}
	
	function stock_post() {
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');   
		
		$limit = $this->input->post('limit'); 
		$page = $this->input->post('page');  
		$id = $this->input->post('id');
		$status = $this->input->post('status');     
		  
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200);   
		if($id=="") $this->response(array('status' => "false",'message'=>'Id is empty'), 200);     
		if($limit=="" or (int)$limit<1) $limit = 10;
		if($page=="" or (int)$page<1) $page = 1;
		
		$offset = ($page-1)*$limit;
		
		// if($status=='all') $status = "";
		// else $status = " and status='1'";
		 
		$stock = $this->api->getStockFilter($limit,$offset,$id)->result();
		$total = $this->api->getStock($id)->row();
 
		$data['total']['all'] = $total->total;
		$data['page'] = $page;
		 
		$data['data'] = array();  
		if($stock){      
			$status = array('1'=>'Tersedia','2'=>'Dipinjam','3'=>'Rusak','4'=>'Hilang','5'=>'Expired','6'=>'Hilang Diganti','7'=>'Sedang Diproses','8'=>'Cadangan','9'=>'Weeding');
			foreach($stock as $key => $row){  
				// if(file_exists('/'))
				$data['data'][$key]['id'] = $row->id; 
				$data['data'][$key]['title'] = $row->title;
				$data['data'][$key]['barcode'] = $row->code; 
				$data['data'][$key]['typename'] = $row->typename;
				$data['data'][$key]['supplier'] = $row->supplier; 
				$data['data'][$key]['origination'] = ($row->origination=='1'?'Beli':'Sumbangan'); 
				$data['data'][$key]['location'] = $row->building;
				$data['data'][$key]['status'] = $status[$row->status]; 
			} 
		} 

		$this->response(array('status' => "true",'message'=>$data), 200); 		
	} 
	
	private function calculatePenalty($rent_id, $return_date_expected, $penalty_per_day, $member_id, $current_date = null) {  
		if ($current_date == null) $current_date = date('Y-m-d');
		
		$do_calculate_penalty = true; 

		$start_calculate_penalty_date = $return_date_expected;
		
		$dt = $this->api->getLastRentPenalty($rent_id)->row();
		 
		if ($dt) {
		  // dont calculate if latest penalty is the same with given return date
		  if ($dt->penalty_date == $current_date) {
			$do_calculate_penalty = false;
			//return;
		  }
		  $start_calculate_penalty_date = $dt->penalty_date;
		}

		if ($do_calculate_penalty) {
		  // get holidays between expected return date and today
		  
			$holidays_on_penalty = $this->api->getHoliday($start_calculate_penalty_date, $current_date)->result();
			// $holidays_on_penalty = HolidayPeer::getHolidaysBetween($start_calculate_penalty_date, $current_date);
			$holidays_date = array();
			foreach ($holidays_on_penalty as $holiday) $holidays_date[$holiday->holiday_date] = 1;

			$day_round = 60*60*24; // store 1 day in second
			$today_time = floor(strtotime($current_date)/$day_round);
			$expected_return_time = floor(strtotime($start_calculate_penalty_date)/$day_round);
			// echo "today : ".$today." expected : $start_calculate_penalty_date ".strtotime($start_calculate_penalty_date);
			if ($today_time - $expected_return_time > 0) $day_on_penalty = $today_time - $expected_return_time;
			else $day_on_penalty = 0;
			
			
			// echo "day_on_penalty : ".$day_on_penalty;
				  
			for ($i = 0; $i < $day_on_penalty; $i++) {
				$penalty_date = date('Y-m-d', strtotime("-{$i} day", strtotime($current_date)));
				if (!array_key_exists($penalty_date, $holidays_date)) {
					$penalty['member_id'] = $member_id; 
					$penalty['rent_id'] = $rent_id;  
					$penalty['penalty_date'] = $penalty_date;  
					$penalty['amount'] = $penalty_per_day;  
					
					// print_r($penalty);
					$this->api->addRentPenalty($penalty);
				}
			}
		}
		
		
		$dt = $this->api->countsumRentPenalty($rent_id)->row();
		$holiday = $this->api->countHoliday($return_date_expected, $current_date)->row();
		
		$data['penalty_day'] = $dt->total;
		$data['penalty_total'] = $dt->total_amount;
		$data['penalty_holiday'] = $holiday->total;
		
	 
		$where = "id= '".$rent_id."'"; 
		$this->api->updateRent($data,$where);
		
		
		return $dt->total_amount;
	}  
	

	function qrcode_post() {
		$public_key = $this->input->post('public_key'); 
		$private_key = $this->input->post('private_key');   
		  
		$token = $this->input->post('token');
  
		$dt = $this->api->getbymemberUuid($private_key)->row();
		  
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		if(!$dt) $this->response(array('status' => "false",'message'=>'Private Key is wrong'), 200);   

		$this->load->helper('jwt');
		$jwt = new JWT();

		$currentTime = date("Y-m-d H:i:s");
		// $newTime = date("Y-m-d H:i:s", strtotime($currentTime . ' + 1 hours'));
		$newTime = date("Y-m-d H:i:s", strtotime($currentTime . ' + 5 minutes'));

		$payload = array( 
			'username' => $dt->master_data_user,
			'expired' => $newTime
		); 
		$token = $jwt->encode($payload, 'qrcode');
  		 	
		$this->response(array('status' => "true",'message'=>$token), 200); 
	} 
	
	  
}

?>