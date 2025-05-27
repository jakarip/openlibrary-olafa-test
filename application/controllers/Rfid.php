<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class Rfid extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config);
		$this->load->model("RfidModel","rm");
		$this->load->model('Usermodel', 'um', TRUE);
		$this->load->model('ApiMobileModel', 'api', TRUE); 
    } 
   
    function index_post() {
		
		$rfid 				= $this->input->post('rfid');
		$status 			= "success";
		
		if($rfid!=""){
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
					if($dt->member_type_id!='19'){  
						$item4['c_username'] 	= $dt->master_data_user;
						$item4['rfid1'] 		= $dt->rfid1;
						$item4['rfid2'] 		= $dt->rfid2;  
						$item4['date_input'] 	= date('Y-m-d H:i:s');  
						
						if($dt->member_type_id!='4') $item4['c_status_user'] = ($dt->status!=1?'GRADUATED':'STUDENT'); 
						
						if($this->um->checkUserinTemUserLoginIgracias($dt->master_data_user)->row()){
							$where = "c_username='".$dt->master_data_user."'"; 
							$this->um->updateItem('masterdata.t_tem_userlogin_igracias',$item4,$where); 
						}
						else { 
							$this->um->addItem('masterdata.t_tem_userlogin_igracias',$item4); 
						} 
					}
					
					$attendance = array( 
						"member_id" => $dt->id,
						"master_data_course" => $dt->master_data_course,
						"item_location_id" => '9',
						"attended_at" => date('Y-m-d H:i:s'),
						"created_by" => 'os_yosep',
						"created_at" => date('Y-m-d H:i:s'),
						"updated_by" => "os_yosep",
						"updated_at" => date('Y-m-d H:i:s')
					);
					$this->rm->add_attendance($attendance);

					$item_location = $this->api->item_location(9)->row();

					$messages 		= "Selamat Datang di Openlibrary ".$item_location->name;

					$itemnotif['notif_id_member'] 	= $dt->master_data_user;
					$itemnotif['notif_type'] 	= 'visitor';
					$itemnotif['notif_content'] 	= $messages;
					$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
					$itemnotif['notif_status'] 	= 'unread';
					$itemnotif['notif_id_detail'] 	= $id;

					$this->api->add_custom($itemnotif,'notification_mobile');

					$this->response(array('status' => 'success'), 200); 
				}
				else {
					if (array_key_exists($rfid, $case_member)){
						$member = $this->rm->GetMember($case_member[$rfid])->row(); 
						if ($member){  
							if($member->member_type_id!='19'){  
								$item4['c_username'] 	= $member->master_data_user;
								$item4['rfid1'] 		= $member->rfid1;
								$item4['rfid2'] 		= $member->rfid2;  
								$item4['date_input'] 	= date('Y-m-d H:i:s');  
								
								if($member->member_type_id!='4') $item4['c_status_user'] = ($member->status!=1?'GRADUATED':'STUDENT'); 
								
								if($this->um->checkUserinTemUserLoginIgracias($member->master_data_user)->row()){
									$where = "c_username='".$member->master_data_user."'"; 
									$this->um->updateItem('masterdata.t_tem_userlogin_igracias',$item4,$where); 
								}
								else { 
									$this->um->addItem('masterdata.t_tem_userlogin_igracias',$item4); 
								} 
							} 
							
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

							$item_location = $this->api->item_location(9)->row();

							$messages 		= "Selamat Datang di Openlibrary ".$item_location->name;

							$itemnotif['notif_id_member'] 	= $dt->master_data_user;
							$itemnotif['notif_type'] 	= 'visitor';
							$itemnotif['notif_content'] 	= $messages;
							$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
							$itemnotif['notif_status'] 	= 'unread';
							$itemnotif['notif_id_detail'] 	= $id;

							$this->api->add_custom($itemnotif,'notification_mobile');

							$this->response(array('status' => 'success'), 200); 
						} else $this->response(array('status' => 'failed'), 502); 
					}
					else $this->response(array('status' => 'failed'), 502); 
				}
			}
			else $this->response(array('status' => 'success'), 200);
		}
		else $this->response(array('status' => 'failed'), 502);
    }
	
	function index_get() {
		
		$rfid 				= $this->input->get('rfid');
		$status 			= "success";
		
		if($rfid!=""){
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
					if($dt->member_type_id!='19'){  
						$item4['c_username'] 	= $dt->master_data_user;
						$item4['rfid1'] 		= $dt->rfid1;
						$item4['rfid2'] 		= $dt->rfid2;  
						$item4['date_input'] 	= date('Y-m-d H:i:s');  
						
						if($dt->member_type_id!='4') $item4['c_status_user'] = ($dt->status!=1?'GRADUATED':'STUDENT'); 
						
						if($this->um->checkUserinTemUserLoginIgracias($dt->master_data_user)->row()){
							$where = "c_username='".$dt->master_data_user."'"; 
							$this->um->updateItem('masterdata.t_tem_userlogin_igracias',$item4,$where); 
						}
						else { 
							$this->um->addItem('masterdata.t_tem_userlogin_igracias',$item4); 
						} 
					}
					
					$attendance = array( 
						"member_id" => $dt->id,
						"master_data_course" => $dt->master_data_course,
						"item_location_id" => '9',
						"attended_at" => date('Y-m-d H:i:s'),
						"created_by" => 'os_yosep',
						"created_at" => date('Y-m-d H:i:s'),
						"updated_by" => "os_yosep",
						"updated_at" => date('Y-m-d H:i:s')
					);
					$this->rm->add_attendance($attendance);
					$this->response(array('status' => 'success'), 200); 
				}
				else {
					if (array_key_exists($rfid, $case_member)){
						$member = $this->rm->GetMember($case_member[$rfid])->row();   
						if ($member){ 
							if($member->member_type_id!='19'){  
								$item4['c_username'] 	= $member->master_data_user;
								$item4['rfid1'] 		= $member->rfid1;
								$item4['rfid2'] 		= $member->rfid2;  
								$item4['date_input'] 	= date('Y-m-d H:i:s');  
								
								if($member->member_type_id!='4') $item4['c_status_user'] = ($member->status!=1?'GRADUATED':'STUDENT'); 
								
								if($this->um->checkUserinTemUserLoginIgracias($member->master_data_user)->row()){
									$where = "c_username='".$member->master_data_user."'"; 
									$this->um->updateItem('masterdata.t_tem_userlogin_igracias',$item4,$where); 
								}
								else { 
									$this->um->addItem('masterdata.t_tem_userlogin_igracias',$item4); 
								} 
							} 
							
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
							$this->response(array('status' => 'success'), 200); 
						} else $this->response(array('status' => 'failed'), 502); 
					}
					else $this->response(array('status' => 'failed'), 502); 
				}
			}
			else $this->response(array('status' => 'success'), 200);   
		}
		else $this->response(array('status' => 'failed'), 502); 
    }
}

?>