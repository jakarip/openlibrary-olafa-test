<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class Rfidscannerattendancecheckin extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config);
		$this->load->model("RfidModel","rm");
		$this->load->model('Usermodel', 'um', TRUE);
		$this->load->model('ApiMobileModel', 'api', TRUE); 
    } 
   
    function index_get() {
		
		$rfid 				= $this->input->get('rfid');
		$location 				= $this->input->get('location');
		$status 			= "success";
 

	  
		// $this->response(array('status' => 'success', 'username'=>$rfid, 'nim_nip'=>$rfid), 200);
		
		if($rfid!=""){ 
			$case 	= $this->rm->GetRfidNotSameWithIgracias()->result(); 

			foreach($case as $ca){
				$case_member[$ca->rfid] = $ca->username;
			}

			$dt = $this->rm->GetRfidScanner($rfid)->row();  
			if ($dt){  
				if($dt->member_type_id!='19'){   
					$temp = array(
						"member_id" => $dt->id,
						"master_data_course" => $dt->master_data_course,
						"item_location_id" => $location,
						"attended_at" => date('Y-m-d H:i:s'),
						"created_at" => date('Y-m-d H:i:s'),
						"created_by" => "checkin", 
						"updated_by" => "checkin",
						"updated_at" => date('Y-m-d H:i:s')
					);  
					$this->rm->addAttendance($temp); 

					$item_location = $this->api->item_location($location)->row();

					$messages 		= "Selamat Datang di Openlibrary ".$item_location->name;

					$itemnotif['notif_id_member'] 	= $dt->master_data_user;
					$itemnotif['notif_type'] 	= 'visitor';
					$itemnotif['notif_content'] 	= $messages;
					$itemnotif['notif_date'] 	= date('Y-m-d H:i:s', strtotime('+7 hour'));
					$itemnotif['notif_status'] 	= 'unread';
					$itemnotif['notif_id_detail'] 	= $id;

					$this->api->add_custom($itemnotif,'notification_mobile');

					$this->response(array('status' => 'success', 'username'=>$dt->master_data_user, 'nim_nip'=>$dt->master_data_number), 200);
				} 
				else $this->response(array('status' => 'failed'), 502);  
			} 
			else {
				if (array_key_exists($rfid, $case_member)){
					$member = $this->rm->GetMemberScanner($case_member[$rfid])->row(); 
					if ($member){  
						if($member->member_type_id!='19'){    
							$temp = array(
								"member_id" => $member->id,
								"master_data_course" => $member->master_data_course,
								"item_location_id" => $location,
								"attended_at" => date('Y-m-d H:i:s'),
								"created_at" => date('Y-m-d H:i:s'),
								"created_by" => "checkin", 
								"updated_by" => "checkin",
								"updated_at" => date('Y-m-d H:i:s')
							);
							$this->rm->addAttendance($temp);  

							$item_location = $this->api->item_location($location)->row();

							$messages 		= "Selamat Datang di Openlibrary ".$item_location->name;

							$itemnotif['notif_id_member'] 	= $dt->master_data_user;
							$itemnotif['notif_type'] 	= 'visitor';
							$itemnotif['notif_content'] 	= $messages;
							$itemnotif['notif_date'] 	= date('Y-m-d H:i:s', strtotime('+7 hour'));
							$itemnotif['notif_status'] 	= 'unread';
							$itemnotif['notif_id_detail'] 	= $id;

							$this->api->add_custom($itemnotif,'notification_mobile');

							$this->response(array('status' => 'success', 'username'=>$member->master_data_user, 'nim_nip'=>$member->master_data_number), 200);
						} 
						else $this->response(array('status' => 'failed'), 502);  
						
					} else $this->response(array('status' => 'failed'), 502); 
				}
				else $this->response(array('status' => 'failed'), 502); 
			} 
		}
		else $this->response(array('status' => 'failed'), 502);
    }
} 

?> 