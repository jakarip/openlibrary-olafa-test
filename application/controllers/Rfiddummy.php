<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class Rfiddummy extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config);
		$this->load->model("RfidModel","rm");
    } 
   
    function index_get() {
		
		$rfid 				= $this->input->get('rfid');
		$status 			= "success";
		
		$static = $this->rm->GetRfidNotInDb()->result();
		$case 	= $this->rm->GetRfidNotSameWithIgracias()->result();
		
		$static_member = array();
		$case_member = array();
		foreach($static as $st){
			$static_member[] = $st->rfid;
		}
		foreach($case as $ca){
			$case_member[$ca->rfid] = $ca->username;
		}
		
		if (!in_array($rfid,$static_member)){
			
			if (array_key_exists($rfid, $case_member)){
				$dt = $this->rm->GetRfid($rfid,$case_member[$rfid])->row();
			}
			else  $dt = $this->rm->GetRfid($rfid)->row();
			
			if ($dt){ 
				$member = $this->rm->GetMember($dt->c_username)->row();
				if ($member){
					$attendance = array( 
						"member_id" => $member->id,
						"master_data_course" => $member->master_data_course,
						"item_location_id" => '9',
						"attended_at" => date('Y-m-d H:i:s'),
						"created_by" => 'os_yosep',
						"created_at" => date('Y-m-d H:i:s'),
						"updated_by" => "os_yosep",
						"updated_at" => date('Y-m-d H:i:s')
					);
					$this->rm->add_attendance($attendance);
					$info = array('id'=>$dt->nomor_induk,'fullname'=>$dt->fullname);
					$this->response(array('status' => 'success','info'=>$info), 200);  
				}else{
					if ($dt->C_KODE_JENIS_USER=='mahasiswa'){
						$user = $this->rm->GetUser('mahasiswa',$dt->c_username)->row();
						if ($user) {
							$master_data_course = $user->c_kode_prodi;
							$member_type_api = $this->rm->GetMemberTypeApi('MAHASISWA',$user->c_kode_prodi)->row(); 
						}
						else $status = "failed";
					}
					else {
						$user = $this->rm->GetUser('pegawai',$dt->c_username)->row();
						if ($user) {
							$master_data_course = NULL;
							$member_type_api = $this->rm->GetMemberTypeApi('PEGAWAI',$user->c_kode_status_pegawai)->row();
						}
						else $status = "failed";
					}
						
					if ($status=="success"){
						$data = array( 
							"member_type_id" 		=> $member_type_api->member_type_id,
							"member_class_id" 		=> $member_type_api->member_class_id,
							"master_data_user" 		=> $dt->c_username,
							"master_data_email" 	=> $dt->email,
							"master_data_course" 	=> $master_data_course,
							"master_data_fullname" 	=> $dt->fullname,
							"status" 				=> "1",
							"created_at" 			=> date("Y-m-d")
						);
					
						$member_id = $this->rm->add($data);
						if ($member_id) {
							$attendance = array( 
								"member_id" => $member_id,
								"master_data_course" => $master_data_course,
								"item_location_id" => '9',
								"attended_at" => date('Y-m-d H:i:s'),
								"created_by" => 'os_magang',
								"created_at" => date('Y-m-d H:i:s'),
								"updated_by" => "os_magang",
								"updated_at" => date('Y-m-d H:i:s')
							);
							$this->rm->add_attendance($attendance);
							
							$info = array('id'=>$dt->nomor_induk,'fullname'=>$dt->fullname);
							$this->response(array('status' => 'success','info'=>$info), 200);  
						}
						else $this->response(array('status' => 'failed'), 502); 
					}
					else {
						$this->response(array('status' => 'failed'), 502); 
					} 
				}
			}
			else {
				if (array_key_exists($rfid, $case_member)){
					$member = $this->rm->GetMember($case_member[$rfid])->row(); 
					if ($member){ 
						$dt = $this->rm->GetRfid($rfid,$case_member[$rfid])->row();
						$info = array('id'=>$dt->nomor_induk,'fullname'=>$dt->fullname);
						$this->response(array('status' => 'success','info'=>$info), 200); 
					} else $this->response(array('status' => 'failed'), 502); 
				}
				else $this->response(array('status' => 'failed'), 502); 
			}
		}
		else {
			$static = $this->rm->GetRfidNotInDbByRfid($rfid)->row();
			$info = array('id'=>'','fullname'=>$static->description);
			$this->response(array('status' => 'success','info'=>$info), 200);  
		}
	}	
}

?>